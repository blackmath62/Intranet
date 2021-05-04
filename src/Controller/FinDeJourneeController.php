<?php

namespace App\Controller;

use App\Repository\Divalto\FinDeJourneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FinDeJourneeController extends AbstractController
{
    /**
     * @Route("/logistique/fin_de_journee", name="app_fin_de_journee")
     */
    public function index(FinDeJourneeRepository $repo): Response
    {
        $listBl = $repo->getFinDeJournee();

        return $this->render('fin_de_journee/index.html.twig', [
            'controller_name' => 'FinDeJourneeController',
            'title' => 'Fin de journée',
            'listBls' => $listBl
        ]);
    }
}
