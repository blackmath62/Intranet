<?php

namespace App\Controller;

use App\Form\YearMonthType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\ControleComptabiliteAchatRepository;
use App\Repository\Divalto\ControleComptabiliteVenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ControleComptabiliteController extends AbstractController
{
    /**
     * @Route("/compta/controle/comptabilite/{slug}", name="app_controle_comptabilite")
     */
    public function controleComptabilite($slug, Request $request, ControleComptabiliteRepository $repo, ControleComptabiliteAchatRepository $repoAchat, ControleComptabiliteVenteRepository $repoVente): Response
    {
        
        $typeTiers = $slug;
        if ($slug == 'F') {
            $tiers = 'Fournisseurs';
        }
        if ($slug == 'C') {
            $tiers = 'Clients';
        }
        $controleTaxes = "";
        $controleRegimeTiers = "";
        $controleRegimeTransport = "";
        $controleTrousFactures = "";
            $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                $mois = $form->getData()['month'];

                $controleTaxes = $repo->getControleTaxesComptabilite($annee,$mois,$typeTiers);
                if ($typeTiers == 'F') {
                    $controleRegimeTiers = $repoAchat->getControleRegimeTiersAchat($annee,$mois);
                }
                if ($typeTiers == 'C') {
                    $controleRegimeTiers = $repoVente->getControleRegimeTiersVente($annee,$mois);
                }


                $controleRegimeTransport = $repo->getControleRegimeTransport($annee,$mois,$typeTiers);
                $controleTrousFactures = $repo->getControleTrousFactures($annee,$mois,$typeTiers);
                
                $factureSansIndex = array();
                for ($i=0; $i <count($controleTrousFactures) ; $i++) { 
                    $factureSansIndex[] = $controleTrousFactures[$i]['fano'];
                }
                //dd($factureSansIndex);
                for ($ligTrouFacture=0; $ligTrouFacture <count($controleTrousFactures) ; $ligTrouFacture++) {
                    if ($ligTrouFacture !== count($controleTrousFactures)) {
                        $facture = $controleTrousFactures[$ligTrouFacture]['fano'];
                            $key = array_search($facture + 1, $factureSansIndex);
                            if (empty($key)) {
                                $factureManquante[] = $facture + 1;   
                            }
                    }
                }
                // pas si simple de déterminer les trous dans les factures, exemple trous dans les factures 19006871 => 6898
                //dd($factureManquante);
                

            }
        
        return $this->render('controle_comptabilite/index.html.twig', [
            'title' => 'Contrôle Comptabilité',
            'annee' => $annee,
            'mois' => $mois,
            'typeTiers' => $tiers,
            'controleTaxes' => $controleTaxes,
            'controleRegimesTiers' => $controleRegimeTiers,
            'controleRegimesTransports' => $controleRegimeTransport,
            'controleTrousFactures' => $controleTrousFactures,
            'monthYear' => $form->createView()
        ]);
    }
}
