<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Service\AdvertViewService;
use Twig\Environment;

final class AdvertiserController
{
    private readonly AdvertViewService $advertViewService;

    public function __construct()
    {
        $this->advertViewService = new AdvertViewService();
    }

    public function show(Environment $twig, array $menu, string $basePath, int $advertiserId, array $categories): void
    {
        $this->annonceur = Advertiser::find($advertiserId);
        if (!isset($this->annonceur)) {
            echo '404';
            return;
        }

        $records = Advert::where('id_annonceur', '=', $advertiserId)->get();
        $advertisements = $this->advertViewService->enrichCollection($records, $basePath . '/img/noimg.png');

        $template = $twig->load('annonceur.html.twig');
        echo $template->render([
            'nom' => $this->annonceur,
            'chemin' => $basePath,
            'annonces' => $advertisements,
            'categories' => $categories,
        ]);
    }
}
