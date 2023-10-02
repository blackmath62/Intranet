<?php

namespace App\Controller;

use App\Repository\Divalto\StocksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class StockController extends AbstractController
{
    #[Route("/stocks", name: "app_stock")]

    public function index(StocksRepository $repo): Response
    {

        // tracking user page for stats
        // $tracking = $request->attributes->get('_route');
        //  $this->setTracking($tracking);

        $stockProduits = $repo->getStocks();

        return $this->render('stock/index.html.twig', [
            'title' => 'Stocks',
            'stockProduits' => $stockProduits,
        ]);
    }
}
