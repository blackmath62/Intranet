<?php

namespace App\Controller;

use App\Repository\AnnuaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/", name="home")
     */

    public function home()
    {
        return $this->render('annuaire/home.html.twig', [
            'title' => "page d'accueil"
        ]);
    }
}
