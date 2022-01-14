<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\ComptaAnalytiqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_COMPTA")
*/

class ComptaAnalytiqueController extends AbstractController
{
    /**
     * @Route("/compta_analytique/test", name="app_compta_analytique_test")
     */
    public function index(Request $request, ComptaAnalytiqueRepository $repo): Response
    {
        $form = $this->createForm(YearMonthType::class);
                    $form->handleRequest($request);
 
                    // tracking user page for stats
                    $tracking = $request->attributes->get('_route');
                    $this->setTracking($tracking);

                    if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        $achat = [];
                        // On interroge la requête
                        $comptaAnalytiques = $repo->getSaleByMonth($annee,$mois);
                        for ($lig=0; $lig <count($comptaAnalytiques) ; $lig++) {
                            $ventes[$lig]['VentAssMax'] = $comptaAnalytiques[$lig]['VentAssMax'];
                            $ventes[$lig]['Dos'] = $comptaAnalytiques[$lig]['Dos'];
                            $ventes[$lig]['Ref'] = $comptaAnalytiques[$lig]['Ref'];
                            $ventes[$lig]['Sref1'] = $comptaAnalytiques[$lig]['Sref1'];
                            $ventes[$lig]['Sref2'] = $comptaAnalytiques[$lig]['Sref2'];
                            $ventes[$lig]['Designation'] = $comptaAnalytiques[$lig]['Designation'];
                            $ventes[$lig]['Uv'] = $comptaAnalytiques[$lig]['Uv'];
                            $ventes[$lig]['CoutRevient'] = $comptaAnalytiques[$lig]['CoutRevient'];
                            $ventes[$lig]['CoutMoyenPondere'] = $comptaAnalytiques[$lig]['CoutMoyenPondere'];
                            $ventes[$lig]['Article'] = $comptaAnalytiques[$lig]['Article'];
                            $ventes[$lig]['Client'] = $comptaAnalytiques[$lig]['Client'];
                            $ventes[$lig]['CompteAchat'] = $comptaAnalytiques[$lig]['CompteAchat'];
                            $ventes[$lig]['RegimeTva'] = $comptaAnalytiques[$lig]['RegimeTva'];
                            $ventes[$lig]['QteSigne'] = $comptaAnalytiques[$lig]['QteSigne'];
                            if ($comptaAnalytiques[$lig]['CoutRevient'] != 0 && $comptaAnalytiques[$lig]['QteSigne'] != 0) {
                                $ventes[$lig]['TotalCr'] = $comptaAnalytiques[$lig]['QteSigne'] * $comptaAnalytiques[$lig]['CoutRevient'];
                            }else {
                                $ventes[$lig]['TotalCr'] = 0;
                            }
                            if ($comptaAnalytiques[$lig]['CoutMoyenPondere'] != 0 && $comptaAnalytiques[$lig]['QteSigne'] != 0) {
                                $ventes[$lig]['TotalCmp'] = $comptaAnalytiques[$lig]['QteSigne'] * $comptaAnalytiques[$lig]['CoutMoyenPondere'];
                            }else {
                                $ventes[$lig]['TotalCmp'] = 0;
                            }
                            $achat = $repo->getAveragePurchasePrice($comptaAnalytiques[$lig]['Ref'], $comptaAnalytiques[$lig]['VentAssMax']);
                            if ($achat) {
                                    $ventes[$lig]['PuAchat'] = $achat['Pu'];
                                if ($ventes[$lig]['PuAchat'] != 0 && $ventes[$lig]['QteSigne'] != 0) {
                                    $ventes[$lig]['MontAchat'] = $ventes[$lig]['QteSigne'] * $ventes[$lig]['PuAchat'];
                                }else {
                                    $ventes[$lig]['MontAchat'] = 0;
                                }
                            }else {
                                $ventes[$lig]['MontAchat'] = 0;
                            }
                            $ventes[$lig]['btnAchat'] = 'text-dark';
                            $ventes[$lig]['btnCmp'] = 'text-dark';
                            $ventes[$lig]['btnCr'] = 'text-dark'; 
                            if ($ventes[$lig]['MontAchat'] != 0 && $ventes[$lig]['TotalCmp'] != 0 ) {
                                if ($ventes[$lig]['MontAchat'] == $ventes[$lig]['TotalCmp'] ) {
                                    $ventes[$lig]['btnAchat'] = 'btn btn-success text-light';
                                    $ventes[$lig]['btnCmp'] = 'btn btn-success text-light';
                                }
                            }

                            if ($ventes[$lig]['MontAchat'] != 0 && $ventes[$lig]['TotalCr'] != 0 ) {
                                if ($ventes[$lig]['MontAchat'] == $ventes[$lig]['TotalCr'] ) {
                                    $ventes[$lig]['btnAchat'] = 'btn btn-success text-light';
                                    $ventes[$lig]['btnCr'] = 'btn btn-success text-light';
                                }
                            }

                            if ($ventes[$lig]['TotalCmp'] != 0 && $ventes[$lig]['TotalCr'] != 0 ) {
                                if ($ventes[$lig]['TotalCmp'] == $ventes[$lig]['TotalCr'] ) {
                                    $ventes[$lig]['btnCmp'] = 'btn btn-success text-light';
                                    $ventes[$lig]['btnCr'] = 'btn btn-success text-light';
                                }
                            }
                        }
                    }
                
                    return $this->render('compta_analytique/index.html.twig', [
                        'ventes' => $ventes,
                        'title' => 'Compta Analytique par mois',
                        'monthYear' => $form->createView()
                        ]);

    }

    /**
     * @Route("compta_analytique", name="app_compta_analytique")
     */
    public function getSaleList(Request $request, ComptaAnalytiqueRepository $repo): Response
    {
        $form = $this->createForm(YearMonthType::class);
                    $form->handleRequest($request);
                    $ventes = [];
 
                    if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        $exportVentes = $repo->getSaleList($annee, $mois);
                        //dd($exportVentes);
                        for ($lig=0; $lig <count($exportVentes) ; $lig++) { 
                        $ventes[$lig]['Facture'] = $exportVentes[$lig]['Facture'];
                        $ventes[$lig]['Ref'] = $exportVentes[$lig]['Ref'];
                        $ventes[$lig]['Sref1'] = $exportVentes[$lig]['Sref1'];
                        $ventes[$lig]['Sref2'] = $exportVentes[$lig]['Sref2'];
                        $ventes[$lig]['Designation'] = $exportVentes[$lig]['Designation'];
                        $ventes[$lig]['Uv'] = $exportVentes[$lig]['Uv'];
                        $ventes[$lig]['Op'] = $exportVentes[$lig]['Op'];
                        $ventes[$lig]['Article'] = $exportVentes[$lig]['Article'];
                        $ventes[$lig]['Client'] = $exportVentes[$lig]['Client'];
                        $ventes[$lig]['CompteAchat'] = $exportVentes[$lig]['CompteAchat'];
                        $ventes[$lig]['QteSign'] = $exportVentes[$lig]['QteSign'];
                        $ventes[$lig]['CoutRevient'] = $exportVentes[$lig]['CoutRevient'];
                        $ventes[$lig]['CoutMoyenPondere'] = $exportVentes[$lig]['CoutMoyenPondere'];
                        $ventes[$lig]['Cma'] = 0;
                        $ventes[$lig]['TotalCoutRevient'] = 0;
                        $ventes[$lig]['TotalCoutMoyenPondere'] = 0;
                        $ventes[$lig]['TotalCoutCma'] = 0;
                        $ventes[$lig]['DetailFacture'] = [];
                            
                            $VentilationVente = $repo->getSaleVentilationByFactAndRef($ventes[$lig]['Facture'], $ventes[$lig]['Ref']);
                            $vent = NULL;
                            for ($ligVentVente=0; $ligVentVente <count($VentilationVente) ; $ligVentVente++) { 
                                $ventilAss  = $VentilationVente[$ligVentVente]['Vtl'];
                                if ($ligVentVente == 0) {
                                   $vent = intval($ventilAss) ;
                                }else {
                                    $vent = $vent . ',' . intval($ventilAss);
                                }
                                $ventes[$lig]['Ventilation'] = $vent;
                            }
                            // TODO s'il y a des Sref, il faut que je compare aussi avec ces Sref

                            if ($ventes[$lig]['Ventilation'] ) {
                                $factures = '';
                                $purchase = $repo->getPurchase($ventes[$lig]['Ref'], $ventes[$lig]['Ventilation']);
                                if ($purchase) {
                                    for ($ligPurchase=0; $ligPurchase <count($purchase) ; $ligPurchase++) { 
                                        $fact = $purchase[$ligPurchase]['Facture'];
                                        if ($ligPurchase == 0) {
                                            $factures = intval($fact) ;
                                        }else {
                                            $factures = $factures . ',' . intval($fact);
                                        }
                                    }
                                    $cam = $repo->getCma($factures, $ventes[$lig]['Ref']);
                                    $ventes[$lig]['Cma'] = $cam['Pu'];
                                    if ($factures) {
                                        $ligne = 0;
                                        $facts = $repo->getDetailPurchase($factures);
                                        //dd($facts);
                                        for ($ligFactures=0; $ligFactures <count($facts) ; $ligFactures++) { 
                                            $ventes[$lig]['DetailFacture'][$ligne]['Facture'] = $facts[$ligFactures]['Facture'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Ref'] = $facts[$ligFactures]['Ref'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Sref1'] = $facts[$ligFactures]['Sref1'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Sref2'] = $facts[$ligFactures]['Sref2'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Designation'] = $facts[$ligFactures]['Designation'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Uv'] = $facts[$ligFactures]['Uv'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Qte'] = $facts[$ligFactures]['Qte'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['Op'] = $facts[$ligFactures]['Op'];
                                            $ventes[$lig]['DetailFacture'][$ligne]['MontantSign'] = $facts[$ligFactures]['MontantSign'];
                                            $ligne++ ;
                                        }
                                        //dd($ventes[$lig]['DetailFacture']);
                                    }
                                }
                            }
                            if ($ventes[$lig]['QteSign']) {
                                if ($ventes[$lig]['CoutRevient']) {
                                    $ventes[$lig]['TotalCoutRevient'] = $ventes[$lig]['QteSign'] * $ventes[$lig]['CoutRevient'];
                                }
                                if ($ventes[$lig]['CoutMoyenPondere']) {
                                    $ventes[$lig]['TotalCoutMoyenPondere'] = $ventes[$lig]['QteSign'] * $ventes[$lig]['CoutMoyenPondere'];
                                }
                                if ($ventes[$lig]['Cma']) {
                                    $ventes[$lig]['TotalCoutCma'] = $ventes[$lig]['QteSign'] * $ventes[$lig]['Cma'];
                                }
                            }

                        }
                    }
        return $this->render('compta_analytique/index.html.twig', [
            'ventes' => $ventes,
            'title' => 'Compta Analytique par mois',
            'monthYear' => $form->createView()
            ]);
    }
}
