<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Category;
use App\Service\AdvertViewService;
use Twig\Environment;

final class CategoryController
{
    private readonly AdvertViewService $advertViewService;

    public function __construct()
    {
        $this->advertViewService = new AdvertViewService();
    }

    public function getCategories(): array
    {
        return Category::orderBy('nom_categorie')->get()->toArray();
    }

    public function displayCategory(Environment $twig, array $menu, string $basePath, array $categories, int $categoryId): void
    {
        $template = $twig->load('index.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
            ['href' => $basePath . '/cat/' . $categoryId, 'text' => Category::find($categoryId)->nom_categorie],
        ];

        echo $template->render([
            'breadcrumb' => $breadcrumb,
            'chemin' => $basePath,
            'categories' => $categories,
            'annonces' => $this->loadCategoryContent($basePath, $categoryId),
        ]);
    }

    protected function loadCategoryContent(string $basePath, int $categoryId): array
    {
        $records = Advert::with('advertiser')->orderBy('id_annonce', 'desc')->where('id_categorie', '=', $categoryId)->get();

        return $this->advertViewService->enrichCollection($records, $basePath . '/img/noimg.png');
    }
}
