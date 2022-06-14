<?php

namespace App\Controller;

use App\Repository\Divalto\CliRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesClientsController extends AbstractController
{
    /**
     * @Route("/mes/clients/{commercial}", name="app_mes_clients")
     */
    public function index($commercial, CliRepository $repo): Response
    {
        return $this->render('mes_clients/index.html.twig', [
            'clients' => $repo->MesClients($commercial),
            'title' => "Mes Clients"
        ]);
    }
}
