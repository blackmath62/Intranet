<?php

namespace App\Controller;

use Exception;
use App\Form\YearMonthType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\ComptaAnalytiqueRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_COMPTA")
*/

class ComptaAnalytiqueController extends AbstractController
{
    /**
     * @Route("compta/compta_analytique", name="app_compta_analytique")
     */
    public function getSaleList(Request $request, ComptaAnalytiqueRepository $repo): Response
    {
        $achat = [];
        $ventes = [];
        $estimation = '';
        $estimationTotal = '';
        $transport = '';
        $form = $this->createForm(YearMonthType::class);
                    $form->handleRequest($request);
 
                    if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        $regime = "";
                        $exportVentes = $repo->getRapportClient($annee, $mois);
                        // exportation des ventes
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
                            $ventes[$lig]['QteSign'] = $exportVentes[$lig]['qteVtl'];
                            $ventes[$lig]['CoutRevient'] = $exportVentes[$lig]['CoutRevient'];
                            $ventes[$lig]['CoutMoyenPondere'] = $exportVentes[$lig]['CoutMoyenPondere'];
                            // rapprocher les achats
                            $achat = $repo->getRapportFournisseurAvecSref(
                                        $exportVentes[$lig]['VentAss'], 
                                        $exportVentes[$lig]['Ref'], 
                                        $exportVentes[$lig]['Sref1'], 
                                        $exportVentes[$lig]['Sref2']);
                            $pa = 0;
                            if ($achat)
                                {$pa = $achat['pa'];}
                            $ventes[$lig]['Cma'] = $pa;
                            $crt = 0;
                            $cmpt = 0;
                            $cmat = 0;
                            if ($exportVentes[$lig]['CoutRevient'] <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $crt  = $exportVentes[$lig]['qteVtl'] * $exportVentes[$lig]['CoutRevient']; }
                            $ventes[$lig]['TotalCoutRevient'] = $crt;
                            if ($exportVentes[$lig]['CoutMoyenPondere'] <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $cmpt  = $exportVentes[$lig]['qteVtl'] * $exportVentes[$lig]['CoutMoyenPondere']; }
                            $ventes[$lig]['TotalCoutMoyenPondere'] = $cmpt;
                            if ($pa <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $cmat  = $exportVentes[$lig]['qteVtl'] * $pa; }
                            $ventes[$lig]['TotalCoutCma'] = $cmat;
                            if ($achat['regimePiece']) {
                                $regime = $achat['regimePiece'];
                            }else {
                                $regime = $exportVentes[$lig]['regimeFou'];
                            }
                            if ($regime == 0) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'];
                            }elseif ($regime == 1) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'] + 10000;
                            }elseif ($regime == 2) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'] + 20000;
                            }
                            $ventes[$lig]['CompteAchat'] = $compteAchat;
                            $ventes[$lig]['estimation'] = '';
                            $ventes[$lig]['estimationTotal'] = '';
                            // TODO REVOIR CETTE PARTIE QUI NE FONCTIONNE PAS DU TOUT DEPUIS L'AJOUT DES ESTIMATIONS, J'AI DU PETER QUELQUE CHOSE
                            if ($achat['pinoFou']) {
                                // ramener la somme des montants du transport sur cette piéce
                                $port = $repo->getTransportFournisseur($achat['pinoFou']);
                                if ($port <> NULL) {
                                    // ramener le détail de la piéce fournisseur
                                    $transport = $repo->getDetailPieceFournisseur($achat['pinoFou']);
                                    // La quantité pour les produits qui ne sont pas des articles de transport
                                    $estim = $repo->getQteHorsPortFournisseur($achat['pinoFou']);
                                    if ($estim['qte'] > 0 && $port['montant'] > 0) {
                                        try {
                                            $ventes[$lig]['estimation'] = ($port['montant'] / $estim['qte']);
                                        } catch (Exception $e) {
                                            echo 'Exception reçue : ',  $e->getMessage() . $port['montant'] . ' - ' . $estim['qte'], "\n";
                                        }
                                        if ($exportVentes[$lig]['qteVtl'] ) {
                                            $ventes[$lig]['estimationTotal'] = $exportVentes[$lig]['qteVtl'] * ($port['montant']/$estim['qte']);
                                        }
                                    }
                                }

                            }
                            $ventes[$lig]['DetailFacture'] = [];
                            $ventes[$lig]['DetailFacture'] = $transport;
                        }
                    }
        return $this->render('compta_analytique/index.html.twig', [
            'ventes' => $ventes,
            'title' => 'Compta Analytique par mois',
            'monthYear' => $form->createView()
            ]);
    }
}
