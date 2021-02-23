<?php

namespace App\Controller;

use App\Form\ClientsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\Divalto\ClientLhermitteByCommercialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class ClientsParSecteurController extends AbstractController
{
    /**
     * @Route("/Lhermitte/clients", name="app_lhermitte_clients_secteur")
     */
    public function index(Request $request, ClientLhermitteByCommercialRepository $clients=null): Response
    {
        $form = $this->createForm(ClientsType::class);
        $form->handleRequest($request);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            //$secteur = $form->getdata()->getStat0002();
            $commercial = $form->getdata()['commercial'];
            //dd($slug);
            $clients = $clients->getClientLhermitteByCommercial($commercial);
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
