<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Advert;
use App\Model\Advertiser;
use App\Model\Category;
use App\Model\Department;
use App\Model\Photo;
use Illuminate\Support\Collection;

final class AdvertViewService
{
    /**
     * @param iterable<Advert> $records
     * @return list<Advert>
     */
    public function enrichCollection(iterable $records, string $fallbackImagePath): array
    {
        $advertisements = [];

        foreach ($records as $record) {
            $advertisements[] = $this->enrich($record, $fallbackImagePath);
        }

        return $advertisements;
    }

    public function enrich(Advert $advert, string $fallbackImagePath): Advert
    {
        $advert->nb_photo = Photo::where('id_annonce', '=', $advert->id_annonce)->count();
        $advert->url_photo = $advert->nb_photo > 0
            ? Photo::select('url_photo')->where('id_annonce', '=', $advert->id_annonce)->first()->url_photo
            : $fallbackImagePath;
        $advert->nom_annonceur = Advertiser::select('nom_annonceur')
            ->where('id_annonceur', '=', $advert->id_annonceur)
            ->first()->nom_annonceur;

        return $advert;
    }

    public function getPhotosForAdvert(int $advertId): Collection
    {
        return Photo::where('id_annonce', '=', $advertId)->get();
    }

    public function getDepartmentName(int $departmentId): ?string
    {
        return Department::find($departmentId)?->nom_departement;
    }

    public function getCategoryName(int $categoryId): ?string
    {
        return Category::find($categoryId)?->nom_categorie;
    }
}
