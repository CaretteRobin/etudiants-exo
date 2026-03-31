<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Model\Photo;
use Twig\Environment;

class HomeController
{
    protected array $advertisements = [];

    public function displayAllAdvertisements(Environment $twig, array $menu, string $basePath, array $categories): void
    {
        $template = $twig->load('index.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
        ];

        $this->loadLatestAdvertisements();

        echo $template->render([
            'breadcrumb' => $breadcrumb,
            'chemin' => $basePath,
            'categories' => $categories,
            'annonces' => $this->advertisements,
        ]);
    }

    protected function loadLatestAdvertisements(): void
    {
        $records = Advert::with('advertiser')->orderBy('id_annonce', 'desc')->take(12)->get();
        $advertisements = [];

        foreach ($records as $record) {
            $record->nb_photo = Photo::where('id_annonce', '=', $record->id_annonce)->count();
            $record->url_photo = $record->nb_photo > 0
                ? Photo::select('url_photo')->where('id_annonce', '=', $record->id_annonce)->first()->url_photo
                : '/img/noimg.png';
            $record->nom_annonceur = Advertiser::select('nom_annonceur')
                ->where('id_annonceur', '=', $record->id_annonceur)
                ->first()->nom_annonceur;
            $advertisements[] = $record;
        }

        $this->advertisements = $advertisements;
    }
}
