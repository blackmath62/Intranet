<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Divalto\Ent;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\AnnuaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    public function home(UsersRepository $repo)
    {
        
            
        $today = new \DateTime();
        $lastYear = new \DateTime();
        $lastYear->sub(new DateInterval('P1Y'));
        $CmdsToday = $this->getDoctrine()->getRepository(Ent::class, 'divaltoreel')->findBy(['pidt' => $today, 'picod' => 2]);
        $CmdsLastYear = $this->getDoctrine()->getRepository(Ent::class, 'divaltoreel')->findBy(['pidt' => $lastYear, 'picod' => 2]);
        $users = $repo->findAll();
        return $this->render('annuaire/home.html.twig', [
            'title' => "page d'accueil",
            'users' => $users,
            'CmdsToday' => $CmdsToday,
            'CmdsLastYear' => $CmdsLastYear  
        ]);

    }
}
