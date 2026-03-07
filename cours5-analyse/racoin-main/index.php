<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use db\connection;
use model\Annonce;
use model\Annonceur;
use model\Categorie;
use model\Departement;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

function captureResponse(Response $response, callable $callback): Response
{
    ob_start();
    $callback();
    $output = (string) ob_get_clean();
    $response->getBody()->write($output);

    return $response;
}

function withJson(Response $response, string $json, int $status = 200): Response
{
    $response->getBody()->write($json);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}

connection::createConn();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['formStarted'])) {
    $_SESSION['formStarted'] = true;
}

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = md5(uniqid((string) rand(), true));
    $_SESSION['token_time'] = time();
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/template');
$twig = new \Twig\Environment($loader);

$chemin = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
if ($chemin === '.' || $chemin === '\\') {
    $chemin = '';
}
if ($chemin !== '' && substr($chemin, -1) !== '/') {
    $chemin .= '/';
}

$menu = array(
    array(
        'href' => './index.php',
        'text' => 'Accueil',
    ),
);

$cat = new \controller\getCategorie();
$dpt = new \controller\getDepartment();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $cat) {
        $index = new \controller\index();
        $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
    });
});

$app->get('/item/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $cat, $args) {
        $item = new \controller\item();
        $item->afficherItem($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    });
});

foreach (array('/add', '/add/') as $path) {
    $app->get($path, function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat, $dpt) {
        return captureResponse($response, function () use ($twig, $menu, $chemin, $cat, $dpt) {
            $ajout = new \controller\addItem();
            $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
        });
    });

    $app->post($path, function (Request $request, Response $response) use ($twig, $menu, $chemin) {
        return captureResponse($response, function () use ($request, $twig, $menu, $chemin) {
            $allPostVars = $request->getParsedBody();
            if (!is_array($allPostVars)) {
                $allPostVars = array();
            }
            $ajout = new \controller\addItem();
            $ajout->addNewItem($twig, $menu, $chemin, $allPostVars);
        });
    });
}

$app->get('/item/{id}/edit', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $args) {
        $item = new \controller\item();
        $item->modifyGet($twig, $menu, $chemin, $args['id']);
    });
});

$app->post('/item/{id}/edit', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat, $dpt) {
    return captureResponse($response, function () use ($request, $twig, $menu, $chemin, $cat, $dpt, $args) {
        $allPostVars = $request->getParsedBody();
        if (!is_array($allPostVars)) {
            $allPostVars = array();
        }
        $item = new \controller\item();
        $item->modifyPost($twig, $menu, $chemin, $args['id'], $cat->getCategories(), $dpt->getAllDepartments(), $allPostVars);
    });
});

$app->map(array('GET', 'POST'), '/item/{id}/confirm', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin) {
    return captureResponse($response, function () use ($request, $twig, $menu, $chemin, $args) {
        $allPostVars = $request->getParsedBody();
        if (!is_array($allPostVars)) {
            $allPostVars = array();
        }
        $item = new \controller\item();
        $item->edit($twig, $menu, $chemin, $allPostVars, $args['id']);
    });
});

foreach (array('/search', '/search/') as $path) {
    $app->get($path, function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        return captureResponse($response, function () use ($twig, $menu, $chemin, $cat) {
            $search = new \controller\Search();
            $search->show($twig, $menu, $chemin, $cat->getCategories());
        });
    });

    $app->post($path, function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        return captureResponse($response, function () use ($request, $twig, $menu, $chemin, $cat) {
            $array = $request->getParsedBody();
            if (!is_array($array)) {
                $array = array();
            }
            $search = new \controller\Search();
            $search->research($array, $twig, $menu, $chemin, $cat->getCategories());
        });
    });
}

$app->get('/annonceur/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $cat, $args) {
        $annonceur = new \controller\viewAnnonceur();
        $annonceur->afficherAnnonceur($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    });
});

$app->get('/del/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $args) {
        $item = new \controller\item();
        $item->supprimerItemGet($twig, $menu, $chemin, $args['n']);
    });
});

$app->post('/del/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($request, $twig, $menu, $chemin, $cat, $args) {
        $allPostVars = $request->getParsedBody();
        if (!is_array($allPostVars)) {
            $allPostVars = array();
        }
        $item = new \controller\item();
        $item->supprimerItemPost($twig, $menu, $chemin, $args['n'], $cat->getCategories(), $allPostVars);
    });
});

$app->get('/cat/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $cat, $args) {
        $categorie = new \controller\getCategorie();
        $categorie->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), $args['n']);
    });
});

foreach (array('/api', '/api/') as $path) {
    $app->get($path, function (Request $request, Response $response) use ($twig, $chemin) {
        return captureResponse($response, function () use ($twig, $chemin) {
            $menu = array(
                array('href' => $chemin, 'text' => 'Acceuil'),
                array('href' => $chemin . 'api', 'text' => 'Api'),
            );
            echo $twig->render('api.html.twig', array('breadcrumb' => $menu, 'chemin' => $chemin));
        });
    });
}

$app->get('/api/annonce/{id}', function (Request $request, Response $response, array $args) {
    $annonceList = array('id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville');
    $return = Annonce::select($annonceList)->find($args['id']);

    if (!isset($return)) {
        return $response->withStatus(404);
    }

    $return->categorie = Categorie::find($return->categorie);
    $return->annonceur = Annonceur::select('email', 'nom_annonceur', 'telephone')->find($return->annonceur);
    $return->departement = Departement::select('id_departement', 'nom_departement')->find($return->departement);
    $return->links = array(
        'self' => array(
            'href' => '/api/annonce/' . $return->id_annonce,
        ),
    );

    return withJson($response, $return->toJson());
});

foreach (array('/api/annonces', '/api/annonces/') as $path) {
    $app->get($path, function (Request $request, Response $response) {
        $annonceList = array('id_annonce', 'prix', 'titre', 'ville');
        $annonces = Annonce::all($annonceList);

        foreach ($annonces as $annonce) {
            $annonce->links = array(
                'self' => array(
                    'href' => '/api/annonce/' . $annonce->id_annonce,
                ),
            );
        }

        return withJson($response, $annonces->toJson());
    });
}

$app->get('/api/categorie/{id}', function (Request $request, Response $response, array $args) {
    $annonces = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
        ->where('id_categorie', '=', $args['id'])
        ->get();

    foreach ($annonces as $annonce) {
        $annonce->links = array(
            'self' => array(
                'href' => '/api/annonce/' . $annonce->id_annonce,
            ),
        );
    }

    $categorie = Categorie::find($args['id']);
    if (!isset($categorie)) {
        return $response->withStatus(404);
    }

    $categorie->links = array(
        'self' => array(
            'href' => '/api/categorie/' . $args['id'],
        ),
    );
    $categorie->annonces = $annonces;

    return withJson($response, $categorie->toJson());
});

foreach (array('/api/categories', '/api/categories/') as $path) {
    $app->get($path, function (Request $request, Response $response) {
        $categories = Categorie::get();

        foreach ($categories as $categorie) {
            $categorie->links = array(
                'self' => array(
                    'href' => '/api/categorie/' . $categorie->id_categorie,
                ),
            );
        }

        return withJson($response, $categories->toJson());
    });
}

$app->get('/api/key', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($twig, $menu, $chemin, $cat) {
        $keyGenerator = new \controller\KeyGenerator();
        $keyGenerator->show($twig, $menu, $chemin, $cat->getCategories());
    });
});

$app->post('/api/key', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
    return captureResponse($response, function () use ($request, $twig, $menu, $chemin, $cat) {
        $data = $request->getParsedBody();
        $name = is_array($data) && isset($data['nom']) ? (string) $data['nom'] : '';

        $keyGenerator = new \controller\KeyGenerator();
        $keyGenerator->generateKey($twig, $menu, $chemin, $cat->getCategories(), $name);
    });
});

$app->run();
