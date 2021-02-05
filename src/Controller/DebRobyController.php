<?php

namespace App\Controller;

use App\Entity\Annuaire;
use App\Entity\Divalto\Mouv;
use App\Repository\AnnuaireRepository;
use App\Repository\Divalto\MouvRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DebRobyController extends AbstractController
{   
    
        /**
     * @Route("/roby/deb", name="app_deb_roby")
     */

    public function test(EntityManagerInterface $entityManager, MouvRepository $repo): Response
    {        
        // DIVALTO cette requête fonctionne correctement
        //$Articles = $this->getDoctrine()->getRepository(Mouv::class, 'divaltoreel')->findBy(['fano' => 18020319]);
        
        
        //$connection = $this->getDoctrine()->getConnection('divaltoreel');
        //dd($connection);
        $max = 120;
        $min = 100;
        $numeroFacture = new \DateTime('2021-01-01');
        $numFactMax = new \DateTime('2021-01-31');

        //$repo = $this->getDoctrine()->getRepository(Annuaire::class);
        //$Articles = $repo->findTest(['max' => $max, 'min' => $min]);
        
        //$minPrice = 1000;
        //$repoArt = $this->getDoctrine()->getRepository(Annuaire::class);
        
        $entityManager = $this->getDoctrine()->getManager('divaltoreel');
        $Articles = $repo->test($entityManager, $numeroFacture, $numFactMax);
        //dd($repoArt);



        // DIVALTO cette requête fonctionne correctement
        //$ref = 'CO1010';
        //$Test = $this->getDoctrine()->getRepository(Art::class, 'divaltoreel')->findBy(['ref' => $ref]);
        
        // Pour tester le répository, cela semble correct !
        //echo get_class($repo);

        return $this->render('deb_roby/index.html.twig', [
            'controller_name' => 'DebRobyController',
            'Articles' => $Articles,
            'title' => 'Deb Roby'
        ]);
    }

}
