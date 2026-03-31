<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Category;
use Twig\Environment;

class SearchController
{
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

        $nospace_mc = str_replace(' ', '', $criteria['motclef']);
        $nospace_cp = str_replace(' ', '', $criteria['codepostal']);

        $query = Advert::select();

        if (
            ($nospace_mc === '') &&
            ($nospace_cp === '') &&
            (($criteria['categorie'] === 'Toutes catégories' || $criteria['categorie'] === '-----')) &&
            ($criteria['prix-min'] === 'Min') &&
            (($criteria['prix-max'] === 'Max') || ($criteria['prix-max'] === 'nolimit'))
        ) {
            $annonce = Advert::all();
        } else {
            if ($nospace_mc !== '') {
                $query->where('description', 'like', '%' . $criteria['motclef'] . '%');
            }

            if ($nospace_cp !== '') {
                $query->where('ville', '=', $criteria['codepostal']);
            }

            if ($criteria['categorie'] !== 'Toutes catégories' && $criteria['categorie'] !== '-----') {
                $categoryId = Category::select('id_categorie')->where('id_categorie', '=', $criteria['categorie'])->first()->id_categorie;
                $query->where('id_categorie', '=', $categoryId);
            }

            if ($criteria['prix-min'] !== 'Min' && $criteria['prix-max'] !== 'Max') {
                if ($criteria['prix-max'] !== 'nolimit') {
                    $query->whereBetween('prix', [$criteria['prix-min'], $criteria['prix-max']]);
                } else {
                    $query->where('prix', '>=', $criteria['prix-min']);
                }
            } elseif ($criteria['prix-max'] !== 'Max' && $criteria['prix-max'] !== 'nolimit') {
                $query->where('prix', '<=', $criteria['prix-max']);
            } elseif ($criteria['prix-min'] !== 'Min') {
                $query->where('prix', '>=', $criteria['prix-min']);
            }

            $annonce = $query->get();
        }

        echo $template->render(['breadcrumb' => $breadcrumb, 'chemin' => $basePath, 'annonces' => $annonce, 'categories' => $categories]);
    }
}
