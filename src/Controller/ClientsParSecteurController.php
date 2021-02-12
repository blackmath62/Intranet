<?php

namespace App\Controller;

use App\Form\ClientsType;
use App\Entity\Divalto\Cli;
use App\Repository\Divalto\CliRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientsParSecteurController extends AbstractController
{
    /**
     * @Route("/Lhermitte/clients", name="app_lhermitte_clients_secteur")
     */
    public function index(Request $request, CliRepository $clients=null): Response
    {
        $form = $this->createForm(ClientsType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //$secteur = $form->getdata()->getStat0002();
            $commercial = $form->getdata()->getRepr0001();
            //dd($slug);
            $clients = $this->getDoctrine()->getRepository(Cli::class, 'divaltoreel')->findBy(['hsdt' => null, 'repr0001' => $commercial]);
            //dd($clients);
        }
        return $this->render('clients_par_secteur/index.html.twig', [
            'title' => 'Clients',
            'clients' => $clients,
            'clientForm' => $form->createView(),
            //'vrps' => $vrp
        ]);
    }
}
