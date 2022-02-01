<?php

namespace App\Controller;

use App\Repository\Divalto\MouvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DernierePieceClientController extends AbstractController
{
    /**
     * @Route("/Roby/dernieres/pieces/client", name="app_dernieres_pieces_ par_client")
     */
    public function index(MouvRepository $repo): Response
    {
        //dd($repo->getLastMouvCli());

        return $this->render('derniere_piece_client/index.html.twig', [
            'controller_name' => 'DernierePieceClientController',
            'lastOrders' => $repo->getLastMouvCli(),
            'title' => 'Derniéres piéces par client'
        ]);
    }
}
