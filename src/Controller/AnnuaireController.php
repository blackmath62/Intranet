<?php

namespace App\Controller;

use App\Repository\AnnuaireRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class AnnuaireController extends AbstractController
{
    /**
     * @Route("/annuaire", name="annuaire")
     */
    public function index(AnnuaireRepository $repo)
    {
        $annuaires = $repo->findAll();
        
        return $this->render('annuaire/index.html.twig', [
            'controller_name' => 'AnnuaireController',
            'annuaires' => $annuaires,
            'title' => "Annuaire"
        ]);
    }
    /**
     * @Route("/", name="app_home", methods={"GET"})
     */

    public function home()
    {
        return $this->render('annuaire/home.html.twig', [
            'title' => "page d'accueil"
        ]);

    }
}
