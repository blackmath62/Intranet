<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Divalto\Ent;
use App\Entity\Main\Trackings;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\AnnuaireRepository;
use App\Repository\Main\TrackingsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_USER")
 */

class AnnuaireController extends AbstractController
{  

    public function indexAction()
    {
    }
    
    /**
     * @Route("/annuaire", name="annuaire")
     */
    public function index(AnnuaireRepository $repo, Request $request)
    {

        $annuaires = $repo->findAll();
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        return $this->render('annuaire/index.html.twig', [
            'controller_name' => 'AnnuaireController',
            'annuaires' => $annuaires,
            'title' => "Annuaire"
        ]);
    }
    

    public function home(UsersRepository $repo, Request $request)
    {
        
            
        $today = new \DateTime();
        $lastYear = new \DateTime();
        $lastYear->sub(new DateInterval('P1Y'));
        $CmdsToday = $this->getDoctrine()->getRepository(Ent::class, 'divaltoreel')->findBy(['pidt' => $today, 'picod' => 2]);
        $CmdsLastYear = $this->getDoctrine()->getRepository(Ent::class, 'divaltoreel')->findBy(['pidt' => $lastYear, 'picod' => 2]);
        $users = $repo->findAll();

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('annuaire/home.html.twig', [
            'title' => "page d'accueil",
            'users' => $users,
            'CmdsToday' => $CmdsToday,
            'CmdsLastYear' => $CmdsLastYear  
        ]);

    }
}
