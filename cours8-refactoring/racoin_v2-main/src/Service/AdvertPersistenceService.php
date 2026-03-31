<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Advert;
use App\Model\Advertiser;

final class AdvertPersistenceService
{
    public function createFromPayload(array $payload): void
    {
        $advertiser = new Advertiser();
        $advert = new Advert();

        $this->fillAdvertiser($advertiser, $payload);
        $this->fillAdvert($advert, $payload);

        $advertiser->save();
        $advertiser->advertisements()->save($advert);
    }

    public function updateFromPayload(Advert $advert, array $payload): bool
    {
        $advertiser = Advertiser::find($advert->id_annonceur);
        if ($advertiser === null) {
            return false;
        }

        $this->fillAdvertiser($advertiser, $payload);
        $this->fillAdvert($advert, $payload);

        $advertiser->save();
        $advertiser->advertisements()->save($advert);

        return true;
    }

    private function fillAdvertiser(Advertiser $advertiser, array $payload): void
    {
        $advertiser->email = htmlentities((string) $payload['email']);
        $advertiser->nom_annonceur = htmlentities((string) $payload['nom']);
        $advertiser->telephone = htmlentities((string) $payload['phone']);
    }

    private function fillAdvert(Advert $advert, array $payload): void
    {
        $advert->ville = htmlentities((string) $payload['ville']);
        $advert->id_departement = $payload['departement'];
        $advert->prix = htmlentities((string) $payload['price']);
        $advert->mdp = password_hash((string) ($payload['psw'] ?? ''), PASSWORD_DEFAULT);
        $advert->titre = htmlentities((string) $payload['title']);
        $advert->description = htmlentities((string) $payload['description']);
        $advert->id_categorie = $payload['categorie'];
        $advert->date = date('Y-m-d');
    }
}
