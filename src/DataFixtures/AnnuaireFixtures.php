<?php

namespace App\DataFixtures;

use \Faker\Factory;
use App\Entity\Societe;
use App\Entity\Annuaire;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AnnuaireFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // créer 20 Annuaires fakées
        // todo probléme avec les societe_Id qui doivent exister dans la table société
        for($i = 1 ; $i <= 10; $i++){
            $annuaire = new Annuaire();
            $annuaire->setNom($faker->name)
                    ->setInterne($faker->buildingNumber)
                    ->setFonction($faker->jobTitle)
                    ->setMail($faker->freeEmail)
                    ->setPortable($faker->phoneNumber)
                    ->setExterieur($faker->phoneNumber);
            $manager->persist($annuaire);
        }
        $manager->flush();
    }
}
