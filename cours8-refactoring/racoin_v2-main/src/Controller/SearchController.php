<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SearchService;
use Twig\Environment;

final class SearchController
{
    private readonly SearchService $searchService;

    public function __construct()
    {
        $this->searchService = new SearchService();
    }

    public function showForm(Environment $twig, array $menu, string $basePath, array $categories): void
    {
        $template = $twig->load('search.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
            ['href' => $basePath . '/search', 'text' => 'Recherche'],
        ];
        echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath, 'categories' => $categories]);
    }

    public function showResults(array $criteria, Environment $twig, array $menu, string $basePath, array $categories): void
    {
        $template = $twig->load('index.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
            ['href' => $basePath . '/search', 'text' => 'Résultats de la recherche'],
        ];
        $annonce = $this->searchService->search($criteria);

        echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath, 'annonces' => $annonce, 'categories' => $categories]);
    }
}
