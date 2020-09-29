<?php

namespace App\DataFixtures;

use \Faker\Factory;
use App\Entity\Societe;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SocieteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // créer 10 sociétés fakées

        for($i = 1 ; $i <= 10; $i++){
            $societe = new Societe();
            $societe->setNom($faker->sentence())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $manager->persist($societe);
        }
        $manager->flush();
    }
}
