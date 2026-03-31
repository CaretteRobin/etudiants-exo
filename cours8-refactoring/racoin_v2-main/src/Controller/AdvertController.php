<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Model\Category;
use App\Service\AdvertPersistenceService;
use App\Service\AdvertViewService;
use App\Support\AdvertFormValidator;
use Twig\Environment;

final class AdvertController
{
    private readonly AdvertFormValidator $validator;
    private readonly AdvertPersistenceService $advertPersistenceService;
    private readonly AdvertViewService $advertViewService;

    public function __construct()
    {
        $this->validator = new AdvertFormValidator();
        $this->advertPersistenceService = new AdvertPersistenceService();
        $this->advertViewService = new AdvertViewService();
    }

    public function showItem(Environment $twig, array $menu, string $basePath, int $advertId, array $categories): void
    {
        $advert = Advert::find($advertId);
        if (!isset($advert)) {
            echo '404';
            return;
        }

        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
            ['href' => $basePath . '/cat/' . $advertId, 'text' => Category::find($advert->id_categorie)?->nom_categorie],
            ['href' => $basePath . '/item/' . $advertId, 'text' => $advert->titre],
        ];

        $advertiser = Advertiser::find($advert->id_annonceur);
        $departmentName = $this->advertViewService->getDepartmentName((int) $advert->id_departement);
        $photos = $this->advertViewService->getPhotosForAdvert($advertId);
        $template = $twig->load('item.html.twig');

        echo $template->render([
            'breadcrumb' => $breadcrumb,
            'chemin' => $basePath,
            'annonce' => $advert,
            'annonceur' => $advertiser,
            'dep' => $departmentName,
            'photo' => $photos,
            'categories' => $categories,
        ]);
    }

    public function showDeleteForm(Environment $twig, array $menu, string $basePath, int $advertId): void
    {
        $advert = Advert::find($advertId);
        if (!isset($advert)) {
            echo '404';
            return;
        }

        $template = $twig->load('delGet.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
            'annonce' => $advert,
        ]);
    }

    public function deleteItem(Environment $twig, array $menu, string $basePath, int $advertId, array $payload, array $categories): void
    {
        $advert = Advert::find($advertId);
        $accepted = false;

        if ($advert !== null && password_verify((string) ($payload['pass'] ?? ''), $advert->mdp)) {
            $accepted = true;
            $this->advertViewService->getPhotosForAdvert($advertId)->each->delete();
            $advert->delete();
        }

        $template = $twig->load('delPost.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
            'annonce' => $advert,
            'pass' => $accepted,
            'categories' => $categories,
        ]);
    }

    public function showEditForm(Environment $twig, array $menu, string $basePath, int $advertId): void
    {
        $advert = Advert::find($advertId);
        if (!isset($advert)) {
            echo '404';
            return;
        }

        $template = $twig->load('modifyGet.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
            'annonce' => $advert,
        ]);
    }

    public function checkEditPassword(Environment $twig, array $menu, string $basePath, int $advertId, array $payload, array $categories, array $departments): void
    {
        $advert = Advert::find($advertId);
        $advertiser = $advert !== null ? Advertiser::find($advert->id_annonceur) : null;
        $categoryName = $advert !== null ? $this->advertViewService->getCategoryName((int) $advert->id_categorie) : null;
        $departmentName = $advert !== null ? $this->advertViewService->getDepartmentName((int) $advert->id_departement) : null;

        $accepted = false;
        if ($advert !== null && password_verify((string) ($payload['pass'] ?? ''), $advert->mdp)) {
            $accepted = true;
        }

        $template = $twig->load('modifyPost.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
            'annonce' => $advert,
            'annonceur' => $advertiser,
            'pass' => $accepted,
            'categories' => $categories,
            'departements' => $departments,
            'dptItem' => $departmentName,
            'categItem' => $categoryName,
        ]);
    }

    public function updateItem(Environment $twig, array $menu, string $basePath, array $payload, int $advertId): void
    {
        date_default_timezone_set('Europe/Paris');

        $errors = $this->validator->validateEdition($payload);

        if (!empty($errors)) {
            $template = $twig->load('add-error.html.twig');
            echo $template->render([
                'breadcrumb' => $menu,
                'chemin' => $basePath,
                'errors' => $errors,
            ]);
            return;
        }

        $advert = Advert::find($advertId);
        if ($advert === null) {
            echo '404';
            return;
        }

        if (!$this->advertPersistenceService->updateFromPayload($advert, $payload)) {
            echo '404';
            return;
        }

        $template = $twig->load('modif-confirm.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
        ]);
    }
}
