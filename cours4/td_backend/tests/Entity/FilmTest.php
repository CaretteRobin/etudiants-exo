<?php

namespace App\Tests\Entity;

use App\Entity\Film;
use App\Entity\Realisateur;
use PHPUnit\Framework\TestCase;

class FilmTest extends TestCase
{
    public function testFilmGettersAndSetters(): void
    {
        $realisateur = new Realisateur();
        $realisateur->setNom('Nolan')->setPrenom('Christopher');

        $film = new Film();
        $film
            ->setTitre('Inception')
            ->setAnneeSortie(2010)
            ->setResume('A mind-bending heist.')
            ->setRealisateur($realisateur);

        self::assertSame('Inception', $film->getTitre());
        self::assertSame(2010, $film->getAnneeSortie());
        self::assertSame('A mind-bending heist.', $film->getResume());
        self::assertSame($realisateur, $film->getRealisateur());
    }
}
