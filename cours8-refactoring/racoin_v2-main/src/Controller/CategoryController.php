<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Model\Category;
use App\Model\Photo;
use Twig\Environment;

class CategoryController
{
    protected array $advertisements = [];

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

        $this->loadCategoryContent($basePath, $categoryId);

        echo $template->render([
            'breadcrumb' => $breadcrumb,
            'chemin' => $basePath,
            'categories' => $categories,
            'annonces' => $this->advertisements,
        ]);
    }

    protected function loadCategoryContent(string $basePath, int $categoryId): void
    {
        $records = Advert::with('advertiser')->orderBy('id_annonce', 'desc')->where('id_categorie', '=', $categoryId)->get();
        $advertisements = [];

        foreach ($records as $record) {
            $record->nb_photo = Photo::where('id_annonce', '=', $record->id_annonce)->count();
            $record->url_photo = $record->nb_photo > 0
                ? Photo::select('url_photo')->where('id_annonce', '=', $record->id_annonce)->first()->url_photo
                : $basePath . '/img/noimg.png';
            $record->nom_annonceur = Advertiser::select('nom_annonceur')
                ->where('id_annonceur', '=', $record->id_annonceur)
                ->first()->nom_annonceur;
            $advertisements[] = $record;
        }

        $this->advertisements = $advertisements;
    }
}
