<?php

namespace App\Tests\Entity;

use App\Entity\Film;
use App\Entity\Realisateur;
use PHPUnit\Framework\TestCase;

class RealisateurTest extends TestCase
{
    public function testRealisateurGettersAndSetters(): void
    {
        $realisateur = new Realisateur();
        $realisateur
            ->setNom('Villeneuve')
            ->setPrenom('Denis')
            ->setBiographie('Canadian director.');

        self::assertSame('Villeneuve', $realisateur->getNom());
        self::assertSame('Denis', $realisateur->getPrenom());
        self::assertSame('Canadian director.', $realisateur->getBiographie());
    }

    public function testRealisateurFilmRelation(): void
    {
        $realisateur = new Realisateur();
        $film = new Film();

        $realisateur->addFilm($film);

        self::assertTrue($realisateur->getFilms()->contains($film));
        self::assertSame($realisateur, $film->getRealisateur());

        $realisateur->removeFilm($film);

        self::assertFalse($realisateur->getFilms()->contains($film));
        self::assertNull($film->getRealisateur());
    }
}
