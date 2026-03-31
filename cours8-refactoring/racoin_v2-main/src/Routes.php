<?php

declare(strict_types=1);

namespace App;

use App\Controller\AddItemController;
use App\Controller\AdvertController;
use App\Controller\AdvertiserController;
use App\Controller\ApiKeyController;
use App\Controller\CategoryController;
use App\Controller\DepartmentController;
use App\Controller\HomeController;
use App\Controller\SearchController;
use App\Model\Advert;
use App\Model\Advertiser;
use App\Model\Category;
use App\Model\Department;
use Slim\App;
use Twig\Environment;

final class Routes
{
    public static function register(App $app, Environment $twig, string $basePath): void
    {
        $categoryController = new CategoryController();
        $departmentController = new DepartmentController();
        $homeController = new HomeController();
        $advertController = new AdvertController();
        $addItemController = new AddItemController();
        $searchController = new SearchController();
        $advertiserController = new AdvertiserController();
        $apiKeyController = new ApiKeyController();

        $menu = [
            [
                'href' => './index.php',
                'text' => 'Accueil',
            ],
        ];

        $app->get('/', function () use ($homeController, $twig, $menu, $basePath, $categoryController): void {
            $homeController->displayAllAdvertisements($twig, $menu, $basePath, $categoryController->getCategories());
        });

        $app->get('/item/{id}', function ($request, $response, array $args) use ($advertController, $twig, $menu, $basePath, $categoryController): void {
            $advertController->showItem($twig, $menu, $basePath, (int) $args['id'], $categoryController->getCategories());
        });

        $app->get('/add', function () use ($addItemController, $twig, $menu, $basePath, $categoryController, $departmentController): void {
            $addItemController->showForm($twig, $menu, $basePath, $categoryController->getCategories(), $departmentController->getAllDepartments());
        });

        $app->post('/add', function ($request) use ($addItemController, $twig, $menu, $basePath): void {
            $addItemController->store($twig, $menu, $basePath, (array) $request->getParsedBody());
        });

        $app->get('/item/{id}/edit', function ($request, $response, array $args) use ($advertController, $twig, $menu, $basePath): void {
            $advertController->showEditForm($twig, $menu, $basePath, (int) $args['id']);
        });

        $app->post('/item/{id}/edit', function ($request, $response, array $args) use ($advertController, $twig, $menu, $basePath, $categoryController, $departmentController): void {
            $advertController->checkEditPassword(
                $twig,
                $menu,
                $basePath,
                (int) $args['id'],
                (array) $request->getParsedBody(),
                $categoryController->getCategories(),
                $departmentController->getAllDepartments()
            );
        });

        $app->map(['GET', 'POST'], '/item/{id}/confirm', function ($request, $response, array $args) use ($advertController, $twig, $menu, $basePath): void {
            $advertController->updateItem($twig, $menu, $basePath, (array) $request->getParsedBody(), (int) $args['id']);
        });

        $app->get('/search', function () use ($searchController, $twig, $menu, $basePath, $categoryController): void {
            $searchController->showForm($twig, $menu, $basePath, $categoryController->getCategories());
        });

        $app->post('/search', function ($request) use ($searchController, $twig, $menu, $basePath, $categoryController): void {
            $searchController->showResults((array) $request->getParsedBody(), $twig, $menu, $basePath, $categoryController->getCategories());
        });

        $app->get('/annonceur/{id}', function ($request, $response, array $args) use ($advertiserController, $twig, $menu, $basePath, $categoryController): void {
            $advertiserController->show($twig, $menu, $basePath, (int) $args['id'], $categoryController->getCategories());
        });

        $app->get('/del/{id}', function ($request, $response, array $args) use ($advertController, $twig, $menu, $basePath): void {
            $advertController->showDeleteForm($twig, $menu, $basePath, (int) $args['id']);
        });

        $app->post('/del/{id}', function ($request, $response, array $args) use ($advertController, $twig, $menu, $basePath, $categoryController): void {
            $advertController->deleteItem($twig, $menu, $basePath, (int) $args['id'], (array) $request->getParsedBody(), $categoryController->getCategories());
        });

        $app->get('/cat/{id}', function ($request, $response, array $args) use ($categoryController, $twig, $menu, $basePath): void {
            $categoryController->displayCategory($twig, $menu, $basePath, $categoryController->getCategories(), (int) $args['id']);
        });

        $app->get('/api', function () use ($twig, $basePath): void {
            $template = $twig->load('api.html.twig');
            $breadcrumb = [
                ['href' => $basePath, 'text' => 'Acceuil'],
                ['href' => $basePath . '/api', 'text' => 'Api'],
            ];

            echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath]);
        });

        $app->group('/api', function () use ($app, $apiKeyController, $twig, $menu, $basePath, $categoryController): void {
            $app->group('/annonce', function () use ($app): void {
                $app->get('/{id}', function ($request, $response, array $args) use ($app): void {
                    $advertFields = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
                    $advert = Advert::select($advertFields)->find((int) $args['id']);

                    if ($advert === null) {
                        $app->notFound();
                        return;
                    }

                    $response->headers->set('Content-Type', 'application/json');
                    $advert->categorie = Category::find($advert->categorie);
                    $advert->annonceur = Advertiser::select('email', 'nom_annonceur', 'telephone')->find($advert->annonceur);
                    $advert->departement = Department::select('id_departement', 'nom_departement')->find($advert->departement);
                    $advert->links = ['self' => ['href' => '/api/annonce/' . $advert->id_annonce]];

                    echo $advert->toJson();
                });
            });

            $app->group('/annonces', function () use ($app): void {
                $app->get('', function ($request, $response): void {
                    $response->headers->set('Content-Type', 'application/json');
                    $advertisements = Advert::all(['id_annonce', 'prix', 'titre', 'ville']);

                    foreach ($advertisements as $advertisement) {
                        $advertisement->links = ['self' => ['href' => '/api/annonce/' . $advertisement->id_annonce]];
                    }

                    $advertisements->links = ['self' => ['href' => '/api/annonces/']];

                    echo $advertisements->toJson();
                });
            });

            $app->group('/categorie', function () use ($app): void {
                $app->get('/{id}', function ($request, $response, array $args): void {
                    $categoryId = (int) $args['id'];
                    $response->headers->set('Content-Type', 'application/json');

                    $advertisements = Advert::select('id_annonce', 'prix', 'titre', 'ville')
                        ->where('id_categorie', '=', $categoryId)
                        ->get();

                    foreach ($advertisements as $advertisement) {
                        $advertisement->links = ['self' => ['href' => '/api/annonce/' . $advertisement->id_annonce]];
                    }

                    $category = Category::find($categoryId);
                    if ($category === null) {
                        echo 'null';
                        return;
                    }

                    $category->links = ['self' => ['href' => '/api/categorie/' . $categoryId]];
                    $category->annonces = $advertisements;

                    echo $category->toJson();
                });
            });

            $app->group('/categories', function () use ($app): void {
                $app->get('', function ($request, $response): void {
                    $response->headers->set('Content-Type', 'application/json');
                    $categories = Category::get();

                    foreach ($categories as $category) {
                        $category->links = ['self' => ['href' => '/api/categorie/' . $category->id_categorie]];
                    }

                    $categories->links = ['self' => ['href' => '/api/categories/']];

                    echo $categories->toJson();
                });
            });

            $app->get('/key', function () use ($apiKeyController, $twig, $menu, $basePath, $categoryController): void {
                $apiKeyController->showForm($twig, $menu, $basePath, $categoryController->getCategories());
            });

            $app->post('/key', function () use ($apiKeyController, $twig, $menu, $basePath, $categoryController): void {
                $apiKeyController->store($twig, $menu, $basePath, $categoryController->getCategories(), (string) ($_POST['nom'] ?? ''));
            });
        });
    }
}
