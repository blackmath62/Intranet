<?php

namespace App\Controller;

use App\Entity\Divalto\Cli;
use App\Entity\Divalto\Vrp;
use App\Form\YearMonthType;
use App\Form\StatesDateFilterType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\StatesLhermitteByTiersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_LHERMITTE")
 */

class StatesLhermitteController extends AbstractController
{
    /**
     * @Route("/Lhermitte/states/EV", name="app_states_lhermitte_ev")
     */
    public function statesEv(StatesLhermitteByTiersRepository $repo, Request $request)
    {
                    
            $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';
            $states = '';
            $sectArt1 = 'EV';
            $sectArt2 = 'HP' ;
            $sectCli1 = 'EV';
            $sectCli2 = 'EV';
            $result = array();
            $values =  array();

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                $mois = $form->getData()['month'];
                $states = $repo->getStatesLhermitteTiersByMonth($annee,$mois,$sectArt1, $sectArt2,$sectCli1,$sectCli2);

                
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
                            $values[$s]['Balance'] = '-right';
                        }else{
                            $values[$s]['Color'] = 'warning';
                            $values[$s]['Balance'] = '-left';
                        } 
                    }else{
                        $values[$s]['Color'] = 'secondary';
                        $values[$s]['Balance'] = '';
                        $values[$s]['Delta_montant'] ='';
                    }

                    // Client (nombre)
                    $values[$s]['Client_Annee_N'] = $clientN;
                    $values[$s]['Client_Annee_N1'] = $clientN1;
                    if ($clientN1 > 0 && $clientN > 0) {
                        $values[$s]['Delta_client'] = (($clientN/$clientN1)-1)*100;
                    }else
                    {
                        $values[$s]['Delta_client'] = '';
                    }

                }

            }
                return $this->render('states_lhermitte/index.html.twig', [
                'states' => $result,
                'values' => $values,
                'title' => 'States Roby',
                'mois' => $mois,
                'annee' => $annee,
                'monthYear' => $form->createView()
                ]);

        }
    /**
     * @Route("/Lhermitte/states/HP", name="app_states_lhermitte_hp")
     */
    public function statesHp(StatesLhermitteByTiersRepository $repo, Request $request): Response
    {
        $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';
            $states = '';
            $sectArt1 = 'EV';
            $sectArt2 = 'HP' ;
            $sectCli1 = 'HP';
            $sectCli2 = 'HP';
            $result = array();
            $values =  array();

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                $mois = $form->getData()['month'];
                $states = $repo->getStatesLhermitteTiersByMonth($annee,$mois,$sectArt1, $sectArt2,$sectCli1,$sectCli2);

                
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
                            $values[$s]['Balance'] = '-right';
                        }else{
                            $values[$s]['Color'] = 'warning';
                            $values[$s]['Balance'] = '-left';
                        } 
                    }else{
                        $values[$s]['Color'] = 'secondary';
                        $values[$s]['Balance'] = '';
                        $values[$s]['Delta_montant'] ='';
                    }

                    // Client (nombre)
                    $values[$s]['Client_Annee_N'] = $clientN;
                    $values[$s]['Client_Annee_N1'] = $clientN1;
                    if ($clientN1 > 0 && $clientN > 0) {
                        $values[$s]['Delta_client'] = (($clientN/$clientN1)-1)*100;
                    }else
                    {
                        $values[$s]['Delta_client'] = '';
                    }
                }

            }
                return $this->render('states_lhermitte/index.html.twig', [
                'states' => $result,
                'values' => $values,
                'title' => 'States Roby',
                'mois' => $mois,
                'annee' => $annee,
                'monthYear' => $form->createView()
                ]);

    }
    /**
     * @Route("/Lhermitte/states/ME", name="app_states_lhermitte_me")
     */
    public function statesMe(StatesLhermitteByTiersRepository $repo, Request $request): Response
    {
        $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';
            $states = '';
            $sectArt1 = 'ME';
            $sectArt2 = 'MO' ;
            $sectCli1 = 'HP';
            $sectCli2 = 'EV';
            $result = array();
            $values =  array();

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                $mois = $form->getData()['month'];
                $states = $repo->getStatesLhermitteTiersByMonth($annee,$mois,$sectArt1, $sectArt2,$sectCli1,$sectCli2);

                
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
                            $result[$j]['Commercial'] = 'DESCHODT ALEX Port: 06.20.63.40.97';
                        }
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
    
                
                    $values[0]['Commercial'] = 'DESCHODT ALEX Port: 06.20.63.40.97';
                    // Piéces
                    $clientN = 0;
                    $clientN1 = 0;
                    $montantN = 0;
                    $montantN1 = 0;

                    for ($fact=0; $fact <count($result) ; $fact++) { 
                        //dd($result);
                        if ($result[$fact]['Commercial'] == 'DESCHODT ALEX Port: 06.20.63.40.97' ) {
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

                   
                    //  Montant
                    $values[0]['AnneeN'] = $montantN;
                    $values[0]['AnneeN1'] = $montantN1;
                    if ($montantN1 > 0 && $montantN > 0) {
                        $values[0]['Delta_montant'] = (($montantN/$montantN1)-1)*100;
                        if ((($montantN/$montantN1)-1)*100 > 0) {
                            $values[0]['Color'] = 'success';
                            $values[0]['Balance'] = '-right';
                        }else{
                            $values[0]['Color'] = 'warning';
                            $values[0]['Balance'] = '-left';
                        } 
                    }else{
                        $values[0]['Color'] = 'secondary';
                        $values[0]['Balance'] = '';
                        $values[0]['Delta_montant'] ='';
                    }

                    // Client (nombre)
                    $values[0]['Client_Annee_N'] = $clientN;
                    $values[0]['Client_Annee_N1'] = $clientN1;
                    if ($clientN1 > 0 && $clientN > 0) {
                        $values[0]['Delta_client'] = (($clientN/$clientN1)-1)*100;
                    }else
                    {
                        $values[0]['Delta_client'] = '';
                    }
                }

            }
                return $this->render('states_lhermitte/index.html.twig', [
                'states' => $result,
                'values' => $values,
                'title' => 'States Roby',
                'mois' => $mois,
                'annee' => $annee,
                'monthYear' => $form->createView()
                ]);
    }
    /**
     * @Route("/Lhermitte/states/MA", name="app_states_lhermitte_ma")
     */
    public function statesMa(): Response
    {
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Ma'
        ]);
    }
    /**
     * @Route("/Lhermitte/states/LH", name="app_states_lhermitte_lh")
     */
    public function statesLh(): Response
    {
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Lh'
        ]);
    }
}
