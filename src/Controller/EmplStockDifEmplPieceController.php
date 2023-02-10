<?php

namespace App\Controller;

use App\Repository\Divalto\EmplRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmplStockDifEmplPieceController extends AbstractController
{
    /**
     * @Route("/empl/stock/dif/empl/piece", name="app_empl_stock_dif_empl_piece")
     */
    public function index(EmplRepository $repo, Request $request): Response
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('empl_stock_dif_empl_piece/index.html.twig', [
            'controller_name' => 'EmplStockDifEmplPieceController',
            'pieces' => $repo->getBadPlaceProductOrder(),
            'stocks' => $repo->getBadPlaceProductStock(),
            'title' => 'Anomalie Emplacements',
        ]);
    }
}
