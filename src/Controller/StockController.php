<?php

namespace App\Controller;

use App\Form\RefDesFiltrerType;
use App\Repository\Divalto\StocksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class StockController extends AbstractController
{
    #[Route("/stocks", name: "app_stock")]

    public function index(StocksRepository $repo, Request $request): Response
    {

        $stockProduits = [];

        $form = $this->createForm(RefDesFiltrerType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $stockProduits = $repo->getStocks($form->getData()['ref'], $form->getData()['des'], $form->getData()['cmd'], $form->getData()['direct']);
        }
        //dd($stockProduits);

        return $this->render('stock/index.html.twig', [
            'title' => 'Stocks',
            'stockProduits' => $stockProduits,
            'form' => $form->createView(),
        ]);
    }
}
