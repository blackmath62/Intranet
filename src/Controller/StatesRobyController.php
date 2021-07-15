<?php

namespace App\Controller;

use App\Form\YearMonthType;
use Symfony\Flex\Unpack\Result;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\StatesRobyByTiersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ROBY")
 */

class StatesRobyController extends AbstractController
{
    /**
     * @Route("/Roby/states/inutile", name="app_states_roby_inutile")
     */
    public function index(StatesRobyByTiersRepository $repo, Request $request)
    {
                    
            $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';
            $states = '';
            $result = array();
            $values =  array();

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                $mois = $form->getData()['month'];
                $states = $repo->getStatesRobyTiersByMonth($annee,$mois);

                // on retire les doublons dans les clients
                for($i=0;$i<count($states);$i++){
                    $tableau = $states[$i];    
                    $tab[$i] = $tableau['Tiers'];
                }
                $tab = array_values(array_unique($tab)); // rendre une liste de tiers Unique
                //dd($states);
                // boucler pour leurs assigner le nom, commercial, pays, Devise les montants des 2 années, on reconstruit le tableau
                
                for($j=0;$j<count($tab);$j++){
                    $result[$j]['Tiers'] = $tab[$j];
                    $key = array_search($tab[$j], array_column($states, 'Tiers'));
                        
                        $result[$j]['Nom'] = $states[$key]['Nom'];
                        if (!$states[$key]['Commercial']) {
                            $result[$j]['Commercial'] = 'Aie ! Pas de commercial assigné';
                        }else {
                            $result[$j]['Commercial'] = $states[$key]['Commercial'];
                        }
                        $result[$j]['Pays'] = $states[$key]['Pays'];
                        $result[$j]['Devise'] = $states[$key]['Devise'];
                        $result[$j]['Delta'] = '';
                        $result[$j]['AnneeN'] = '';
                        $result[$j]['AnneeN1'] = '';
                        
                        for ($p=0; $p <count($states) ; $p++) { 
                            if ($states[$p]['Tiers'] == $tab[$j] && $states[$p]['Annee'] == $annee ) {
                                $result[$j]['AnneeN'] = $states[$p]['MontantSign']; // Année demandée
                            }                            
                            if ($states[$p]['Tiers'] == $tab[$j] && $states[$p]['Annee'] == $annee -1) {
                                $result[$j]['AnneeN1'] = $states[$p]['MontantSign']; // Année N-1
                            }
                            if ($result[$j]['AnneeN'] <> 0 && $result[$j]['AnneeN1'] <> 0) {
                                $result[$j]['Delta'] = (($result[$j]['AnneeN']/$result[$j]['AnneeN1']-1)*100);
                            }
                        }

                }
                
                // le compte rendu par commercial

                // on retire les doublons dans les commerciaux
                for($i=0;$i<count($states);$i++){
                    $tableau = $states[$i];    
                    $tab[$i] = $tableau['Commercial'];
                }
                $commercial = array_values(array_unique($tab)); // rendre une liste de commerciaux sans doublon
    
                for ($s=0; $s<count($commercial) ; $s++) { 
                    $values[$s]['Commercial'] = $commercial[$s];
                    // Piéces
                    $clientN = 0;
                    $clientN1 = 0;
                    $montantN = 0;
                    $montantN1 = 0;

                    for ($fact=0; $fact <count($result) ; $fact++) { 
                        //dd($result);
                        if ($result[$fact]['Commercial'] == $commercial[$s] ) {
                           // Client (nombre)
                            if ( $result[$fact]['AnneeN'] ) {
                            $clientN =  $clientN + 1;
                           }
                           if ($result[$fact]['AnneeN1'] ) {
                            $clientN1 =  $clientN1 + 1;
                           }
                           // montant
                           if ( $result[$fact]['AnneeN'] ) {
                            $montantN =  $montantN + $result[$fact]['AnneeN'];
                           }
                           if ($result[$fact]['AnneeN1'] ) {
                            $montantN1 =  $montantN1 + $result[$fact]['AnneeN1'];
                           }
                        }

                    }
                    //  Montant
                    $values[$s]['AnneeN'] = $montantN;
                    $values[$s]['AnneeN1'] = $montantN1;
                    if ($montantN1 > 0 && $montantN > 0) {
                        $values[$s]['Delta_montant'] = (($montantN/$montantN1)-1)*100;
                        if ((($montantN/$montantN1)-1)*100 > 0) {
                            $values[$s]['Color'] = 'success';
                            $values[$s]['Balance'] = 'right';
                        }else{
                            $values[$s]['Color'] = 'warning';
                            $values[$s]['Balance'] = 'left';
                        } 
                    }

                    // Client (nombre)
                    $values[$s]['Client_Annee_N'] = $clientN;
                    $values[$s]['Client_Annee_N1'] = $clientN1;
                    if ($clientN1 > 0 && $clientN > 0) {
                        $values[$s]['Delta_client'] = (($clientN/$clientN1)-1)*100;
                    }

                }

            }
                return $this->render('states_roby/index.html.twig', [
                'states' => $result,
                'values' => $values,
                'title' => 'States Roby',
                'mois' => $mois,
                'annee' => $annee,
                'monthYear' => $form->createView()
                ]);

        }
}
