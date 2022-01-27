<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Film;

class FilmFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 5; $i++)
        {
            $film = new Film();
            $film->setNom("Nom du film n°$i")
                 ->setDescription("Description du film n°$i")
                 ->setNote("5")
                 ->setNb_Votants("3");
        
            $manager->persist($film);
        }

        $manager->flush();
    }
}
