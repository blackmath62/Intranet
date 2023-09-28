<?php

namespace App\Controller;

use App\Form\StockDirectType;
use App\Repository\Divalto\ArtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class StockDirectController extends AbstractController
{
    #[Route("/stock/direct/{metier}", name: "app_stock_direct")]

    public function index(Request $request, ArtRepository $repo, $metier = null): Response
    {

        $form = $this->createForm(StockDirectType::class);
        $form->handleRequest($request);
        $dos = 1;
        $stockDirect = "";

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()['metier']) {
                if (strstr($form->getData()['metier'], 'RB')) {
                    $dos = 3;
                }
            }
            $stockDirect = $repo->getControleStockDirectFiltre($form->getData()['metier'], $dos);
        }

        //dd($stockDirect);
        return $this->render('stock_direct/index.html.twig', [
            'controller_name' => 'StockDirectController',
            'stocks' => $stockDirect,
            'title' => 'Stock Direct',
            'form' => $form->createView(),
        ]);
    }
}
