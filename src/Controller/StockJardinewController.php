<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\StocksJardinewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StockJardinewController extends AbstractController
{
    /**
     * @Route("/jardinew/stocks", name="app_stock_jardinew")
     */
    public function index(Request $request, StocksJardinewRepository $repo): Response
    {
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $stockProduits = $repo->getStocksJardinewRepository();

        return $this->render('stock_jardinew/index.html.twig', [
            'controller_name' => 'StockJardinewController',
            'title' => 'Stocks Jardinew',
            'stockProduits' => $stockProduits
        ]);
    }
}
