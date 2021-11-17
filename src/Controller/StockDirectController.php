<?php

namespace App\Controller;

use App\Form\StockDirectType;
use App\Repository\Divalto\ArtRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class StockDirectController extends AbstractController
{
    /**
     * @Route("/stock/direct/{metier}", name="app_stock_direct")
     */
    public function index($metier = null,Request $request, ArtRepository $repo): Response
    {
        
        $form = $this->createForm(StockDirectType::class);
        $form->handleRequest($request);
        $dos = 1;
        $stockDirect = "";

        if($form->isSubmitted() && $form->isValid()){
            if ($form->getData()['metier']) {
                if ( strstr($form->getData()['metier'], 'RB') ) {
                    $dos = 3;
                }
            }
            $stockDirect = $repo->getControleStockDirectFiltre($form->getData()['metier'],$dos);
        }

        //dd($stockDirect);
        return $this->render('stock_direct/index.html.twig', [
            'controller_name' => 'StockDirectController',
            'stocks' =>$stockDirect,
            'title' => 'Stock Direct',
            'form' => $form->createView()
        ]);
    }
}
