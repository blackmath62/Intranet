<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]

class ControleProduitStockMouvEfController extends AbstractController
{
    #[Route("/controle/produit/stock/mouv/ef", name: "app_controle_produit_stock_mouv_ef")]

    public function index(ControleArtStockMouvEfRepository $repo, Request $request): Response
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $controleProduits = "";

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $controleProduits = $repo->getControleArtStockMouvEfRepository($search);
        }

        return $this->render('controle_produit_stock_mouv_ef/index.html.twig', [
            'controller_name' => 'ControleProduitStockMouvEfController',
            'title' => 'Contrôle Produits Fermetures',
            'controleproduits' => $controleProduits,
            'search' => $form->createView(),
        ]);
    }

    #[Route("/controle/anomalies/produits/fermes", name: "app_anomalies_produits_fermes")]

    public function getAnomaliesProduitsFermes(ControleArtStockMouvEfRepository $repo, SearchTiersController $dos): Response
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $dossier = $dos->getDossierUser();

        return $this->render('controle_produit_stock_mouv_ef/anomaliesProduitsFermes.html.twig', [
            'title' => 'Contrôle Anomalies Produits Fermes',
            'controleproduits' => $repo->getControleAnomaliesArticlesFermes($dossier),
        ]);
    }

}
