<?php

namespace App\Controller;

use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\Divalto\ClientLhermitteByCommercialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class SearchTiersController extends AbstractController
{
    /**
     * @Route("/search/tiers", name="app_search_tiers")
     */
    public function index(Request $request, ClientLhermitteByCommercialRepository $repo): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $dossier = $this->getDossierUser();
        
        $clients = null;

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $search = $form->getData()['search'];
            $clients = $repo->getClientByContactName($search, $dossier);
        }

        return $this->render('search_tiers/index.html.twig', [
            'title' => 'Rechercher client',
            'clients' => $clients,
            'search' => $form->createView()
        ]);
    }

    public function getDossierUser()
    {
        // mettre un dossier en fonction de l'utilisateur
        if ($this->getUser()->getSociete()->getId() == 1) {
            $dossier = 1;
        }
        if ($this->getUser()->getSociete()->getId() == 15) {
            $dossier = 3;
        }
        if ($this->getUser()->getSociete()->getId() == 20) {
            $dossier = '1,3';
        }
        if ($this->getUser()->getSociete()->getId() == 16 ) {
            $dossier = '1,3';
        }
        return $dossier;
    }
}
