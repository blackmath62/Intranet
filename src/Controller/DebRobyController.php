<?php

namespace App\Controller;

use DateTime;
use App\Entity\Annuaire;
use App\Entity\Divalto\Art;
use App\Entity\Divalto\Ent;
use App\Entity\Divalto\Mouv;
use App\Repository\ArtRepository;
use App\Repository\EntRepository;
use App\Repository\Divalto\MouvRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DebRobyController extends AbstractController
{
    /**
     * @Route("/roby/deb/test", name="app_deb_roby_test")
     */

    // test des requêtes Divalto via symfony, ça fonctionne ! cette requête ne sert à rien.... MOuhahahaha
    /*
    public function index(EntityManagerInterface $em): Response
    {
        $Articles = $this->getDoctrine()
            ->getRepository(Art::class, 'divaltosvg')
            ->findBy(array(), null, 10, null);

        return $this->render('deb_roby/test.html.twig', [
            'controller_name' => 'DebRobyController',
            'Articles' => $Articles
        ]);
    }*/
    /**
     * @Route("/roby/deb", name="app_deb_roby")
     */

    public function test(MouvRepository $repo): Response
    {
        $fromDate = '2018-12-31';
        $toDate = '2019-12-31';
        //$Articles = $this->getDoctrine()->getRepository(Mouv::class, 'divaltosvg')->findBy(['fano' => 18020319]);
        
        $Articles = $this->getDoctrine()->getRepository(Mouv::class, 'divaltosvg')->test18($fromDate);
        
        return $this->render('deb_roby/index.html.twig', [
            'controller_name' => 'DebRobyController',
            'Articles' => $Articles,
            //'test' => $test
        ]);
    }

}
