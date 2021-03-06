<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_BOSS")
 */

class StatesExcelGlobalesController extends AbstractController
{
    /**
     * @Route("/states/excel/globales", name="app_states_excel_globales")
     */
    public function index(): Response
    {
        
        return $this->render('states_excel_globales/index.html.twig', [
            'controller_name' => 'StatesExcelGlobalesController',
            'title' => 'Export Excel'
        ]);
    }
}
