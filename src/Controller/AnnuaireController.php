<?php

namespace App\Controller;

use App\Repository\Main\AnnuaireRepository;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\UsersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]

class AnnuaireController extends AbstractController
{

    public function indexAction()
    {
    }

    #[Route("/annuaire", name: "annuaire")]

    public function index(AnnuaireRepository $repo, Request $request)
    {

        $annuaires = $repo->getAnnuaire();
        /*
        SELECT u.societe_id AS societe, u.interne AS interne, u.pseudo AS nom, u.exterieur AS exterieur, u.email AS email, u.fonction AS fonction, u.portable AS portable
        FROM users u
        WHERE u.closedAt IS NULL
         */

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('annuaire/index.html.twig', [
            'controller_name' => 'AnnuaireController',
            'annuaires' => $annuaires,
            'title' => "Annuaire",
        ]);
    }

    public function home(UsersRepository $repo, Request $request, HolidayRepository $holidayRepo)
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
