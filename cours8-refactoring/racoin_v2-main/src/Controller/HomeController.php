<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Advert;
use App\Service\AdvertViewService;
use Twig\Environment;

final class HomeController
{
    private readonly AdvertViewService $advertViewService;

    public function __construct()
    {
        $this->advertViewService = new AdvertViewService();
    }

    public function displayAllAdvertisements(Environment $twig, array $menu, string $basePath, array $categories): void
    {
        $template = $twig->load('index.html.twig');
        $breadcrumb = [
            ['href' => $basePath, 'text' => 'Acceuil'],
        ];

        echo $template->render([
            'breadcrumb' => $breadcrumb,
            'chemin' => $basePath,
            'categories' => $categories,
            'annonces' => $this->loadLatestAdvertisements(),
        ]);
    }

    protected function loadLatestAdvertisements(): array
    {
        $records = Advert::with('advertiser')->orderBy('id_annonce', 'desc')->take(12)->get();

        return $this->advertViewService->enrichCollection($records, '/img/noimg.png');
    }
}
