<?php

namespace App\Controller;

use DateTime;
use App\Entity\Annuaire;
use App\Entity\Divalto\Art;
use App\Entity\Divalto\Ent;
use App\Entity\Divalto\Mouv;
use App\Repository\ArtRepository;
use App\Repository\EntRepository;
use App\Repository\MouvRepository;
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
    
    public function index(): Response
    {   
        $em = $this->getDoctrine()->getManager('divaltoreel');

        return $this->render('deb_roby/test.html.twig', [
            'controller_name' => 'DebRobyController',
        ]);
    }
    /**
     * @Route("/roby/deb", name="app_deb_roby")
     */

    public function test(ArtRepository $repo): Response
    {
       
        
        // DIVALTO cette requête fonctionne correctement
        $Articles = $this->getDoctrine()->getRepository(Mouv::class, 'divaltoreel')->findBy(['fano' => 18020319]);
        // DIVALTO cette requête fonctionne correctement
        $ref = 'CO1010';
        $Test = $this->getDoctrine()->getRepository(Art::class, 'divaltoreel')->findBy(['ref' => $ref]);
        
        // Pour tester le répository, cela semble correct !
        //echo get_class($repo);

        return $this->render('deb_roby/index.html.twig', [
            'controller_name' => 'DebRobyController',
            'Articles' => $Articles,
            'test' => $Test
        ]);
    }

}
