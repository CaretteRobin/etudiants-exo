<?php

namespace App\DataFixtures;

use App\Entity\Film;
use App\Entity\Realisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');
        $realisateurs = [];

        for ($i = 0; $i < 5; $i++) {
            $realisateur = new Realisateur();
            $realisateur
                ->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setDateNaissance($faker->dateTimeBetween('-80 years', '-30 years'))
                ->setBiographie($faker->paragraph());

            $manager->persist($realisateur);
            $realisateurs[] = $realisateur;
        }

        for ($i = 0; $i < 15; $i++) {
            $film = new Film();
            $film
                ->setTitre($faker->sentence(3))
                ->setAnneeSortie($faker->numberBetween(1980, 2024))
                ->setResume($faker->paragraph())
                ->setRealisateur($faker->randomElement($realisateurs));

            $manager->persist($film);
        }

        $manager->flush();
    }
}
