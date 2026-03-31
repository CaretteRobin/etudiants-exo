<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\ApiKey;
use Twig\Environment;

class ApiKeyController
{
    public function showForm(Environment $twig, array $menu, string $basePath, array $categories): void
    {
        $template = $twig->load('key-generator.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
            ['href' => $basePath . '/search', 'text' => 'Recherche'],
        ];

        echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath, 'categories' => $categories]);
    }

    public function store(Environment $twig, array $menu, string $basePath, array $categories, string $name): void
    {
        $normalizedName = str_replace(' ', '', $name);

        if ($normalizedName === '') {
            $template = $twig->load('key-generator-error.html.twig');
            $breadcrumb = [
                ['href' => $basePath, 'text' => 'Acceuil'],
                ['href' => $basePath . '/search', 'text' => 'Recherche'],
            ];

            echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath, 'categories' => $categories]);
            return;
        }

        $template = $twig->load('key-generator-result.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
            ['href' => $basePath . '/search', 'text' => 'Recherche'],
        ];

        $key = uniqid();
        $apiKey = new ApiKey();
        $apiKey->id_apikey = $key;
        $apiKey->name_key = htmlentities($name);
        $apiKey->save();

        echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath, 'categories' => $categories, 'key' => $key]);
    }
}
