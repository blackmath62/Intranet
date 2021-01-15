<?php

namespace App\Controller;

use App\Entity\Annuaire;
use App\Repository\ARTRepository;
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
        $this->addFlash('info', 'Vous etes arrivé dans l\'annuaire !');
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
    /**
     * @Route("/test", name="app_test")
     */

    public function test(AnnuaireRepository $repo)
    {
        //$test = $repo->test();
/*
        $repository = $this->getDoctrine()
                           ->getManager()
                           ->getRepository(Annuaire::class);
echo 'le repository est de classe '.get_class($repository);exit;
*/
        $fromDate = 12;
        $toDate = 17;
        $Articles = $repo->test2(array($fromDate, $toDate));
        return $this->render('test/test.html.twig',[
            //'title' => "Test",
            //'test' => $test,
            'Articles' => $Articles
        ]);

    }
}
