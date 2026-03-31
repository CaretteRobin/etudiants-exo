<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AdvertPersistenceService;
use App\Support\AdvertFormValidator;
use Twig\Environment;

final class AddItemController
{
    private readonly AdvertFormValidator $validator;
    private readonly AdvertPersistenceService $advertPersistenceService;

    public function __construct()
    {
        $this->validator = new AdvertFormValidator();
        $this->advertPersistenceService = new AdvertPersistenceService();
    }

    public function showForm(Environment $twig, array $menu, string $basePath, array $categories, array $departments): void
    {
        $template = $twig->load('add.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $basePath,
            'categories' => $categories,
            'departements' => $departments,
        ]);
    }

    public function store(Environment $twig, array $menu, string $basePath, array $allPostVars): void
    {
        date_default_timezone_set('Europe/Paris');
        $errors = $this->validator->validateCreation($allPostVars);

        // S'il y a des erreurs on redirige vers la page d'erreur
        if (!empty($errors)) {

            $template = $twig->load('add-error.html.twig');
            echo $template->render([
                'breadcrumb' => $menu,
                'chemin' => $basePath,
                'errors' => $errors,
            ]);
        } // sinon on ajoute à la base et on redirige vers une page de succès
        else {
            $this->advertPersistenceService->createFromPayload($allPostVars);

            $template = $twig->load('add-confirm.html.twig');
            echo $template->render(['breadcrumb' => $menu, 'chemin' => $basePath]);
        }
    }
}
