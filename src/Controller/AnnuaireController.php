<?php

namespace App\Controller;

use App\Repository\Main\AnnuaireRepository;
use App\Repository\Main\UsersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]

class AnnuaireController extends AbstractController
{

    public function indexAction()
    {
    }

    #[Route("/annuaire", name: "annuaire")]

    public function index(AnnuaireRepository $repo)
    {

        $annuaires = $repo->getAnnuaire();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('annuaire/index.html.twig', [
            'controller_name' => 'AnnuaireController',
            'annuaires' => $annuaires,
            'title' => "Annuaire",
        ]);
    }

    public function home(UsersRepository $repo)
    {

        $users = $repo->findAll();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('annuaire/home.html.twig', [
            'title' => "page d'accueil",
            'users' => $users,

        ]);

    }
}
