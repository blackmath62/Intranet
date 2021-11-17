<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\FinDeJourneeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

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
