<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Model\Photo;
use Twig\Environment;

final class AdvertiserController
{
    public function show(Environment $twig, array $menu, string $basePath, int $advertiserId, array $categories): void
    {
        $this->annonceur = Advertiser::find($advertiserId);
        if (!isset($this->annonceur)) {
            echo '404';
            return;
        }

        $records = Advert::where('id_annonceur', '=', $advertiserId)->get();
        $advertisements = [];

        foreach ($records as $record) {
            $record->nb_photo = Photo::where('id_annonce', '=', $record->id_annonce)->count();
            $record->url_photo = $record->nb_photo > 0
                ? Photo::select('url_photo')->where('id_annonce', '=', $record->id_annonce)->first()->url_photo
                : $basePath . '/img/noimg.png';

            $advertisements[] = $record;
        }

        $template = $twig->load('annonceur.html.twig');
        echo $template->render([
            'nom' => $this->annonceur,
            'chemin' => $basePath,
            'annonces' => $advertisements,
            'categories' => $categories,
        ]);
    }
}
