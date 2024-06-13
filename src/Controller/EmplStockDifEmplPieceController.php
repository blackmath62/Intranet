<?php

namespace App\Controller;

use App\Repository\Divalto\EmplRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class EmplStockDifEmplPieceController extends AbstractController
{
    #[Route("/empl/stock/dif/empl/piece", name: "app_empl_stock_dif_empl_piece")]

    public function index(EmplRepository $repo): Response
    {

        return $this->render('empl_stock_dif_empl_piece/index.html.twig', [
            'controller_name' => 'EmplStockDifEmplPieceController',
            'pieces' => $repo->getBadPlaceProductOrder(),
            'stocks' => $repo->getBadPlaceProductStock(),
            'title' => 'Anomalie Emplacements',
        ]);
    }
}
