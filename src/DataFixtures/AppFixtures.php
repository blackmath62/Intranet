<?php

namespace App\DataFixtures;

use App\Entity\Chats;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // créer 20 Annuaires fakées
        // todo probléme avec les societe_Id qui doivent exister dans la table société
        for($i = 1 ; $i <= 100; $i++){
            $chat = new Chats();
            $chat->setContent($faker->text(200))
                 ->setUser(9)
                 ->setCreatedAt($faker->datetime);
                    
            $manager->persist($chat);
        }
        $manager->flush();
    }
}
