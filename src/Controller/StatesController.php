<?php

namespace App\Controller;

use DateTime;
use App\Form\DateDebutFinType;
use PhpParser\Node\Stmt\Else_;
use App\Form\DateSecteurLhDebutFinType;
use App\Form\DateSecteurRbDebutFinType;
use DoctrineExtensions\Query\Mysql\Year;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\Divalto\MouvRepository;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\StatesByTiersRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatesController extends AbstractController
{
    /**
     * @Route("/Lhermitte/states", name="app_states_lhermitte")
     * @Route("/Roby/states", name="app_states_roby")
     */    

    public function states(StatesByTiersRepository $repo, Request $request):Response
    {
        // initialisation de mes variables
        if ($request->attributes->get('_route') == 'app_states_roby') {
            $form = $this->createForm(DateSecteurRbDebutFinType::class);
            $metier = 'RB';
        }
        elseif ($request->attributes->get('_route') == 'app_states_lhermitte') {
            $form = $this->createForm(DateSecteurLhDebutFinType::class);
            $metier = '';
        }

        $form->handleRequest($request);
            $dateDebutEtFin = '';
            $intervalN = 0;
            $intervalN1 = 0;
            $commercial = array();
            $stateCommerciaux = array();
            $statesBandeau = array();
            $statesParClient = array();
            $statesMetiers= array();
            $themeColor = '';
            $titre = '';
            $dateDebutN = "";
            $dateFinN = "";
            $dateDebutN1 = "";
            $dateFinN1 = "";
            $dossier = "";
            $sufixeMetier = '';
            
            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                
                // Rechercher les paramétres du métiers
                if ($request->attributes->get('_route') == 'app_states_roby') {
                    $metier = 'RB';
                }
                elseif ($request->attributes->get('_route') == 'app_states_lhermitte') {
                    $metier = $form->getData()['Metiers'];
                }    
                $secteur = $this->metierParameter($metier);
                $metiers = $secteur['metiers'];
                $themeColor = $secteur['themeColor'];
                $titre = $secteur['titre'];
                $dossier = $secteur['dossier'];
                $sufixeMetier = $secteur['sufixeRoute'];

                $dateDebutEtFin = $form->getData()['Periode'];
                //dd($dateDebutEtFin);
                $dateParam = $this->dateParameter($form->getData()['Periode']);
                $intervalN = $dateParam['intervalN'];
                $intervalN1 = $dateParam['intervalN1'];
                $dateDebutN = $dateParam['dateDebutN'];
                $dateDebutN1 = $dateParam['dateDebutN1'];
                $dateFinN = $dateParam['dateFinN'];
                $dateFinN1 = $dateParam['dateFinN1'];
                //$interval = date_diff(date_create($dateDebutN), date_create($dateFinN));
                $dateControle = date_create($dateDebutN);
                $dateControle = date_modify($dateControle, '+1 Year');
                $dateFin = date_create($dateFinN);

                // Bloquer l'extraction sur une année compléte maximum
                if ($dateFin >= $dateControle ) {
                    $this->addFlash('danger', 'L\'extraction est limité à une année compléte');
                    return $this->redirectToRoute($tracking, ['secteur' => $secteur]);
                }
                
                // Début Bandeau Détaillé avec Nombre de facture, Bl, CA Dépôt, CA Direct etc ...
                $statesTotauxParSecteur = $repo->getStatesTotauxParSecteur($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
                
                $statesBandeau['CATotalBandeauN1'] = 0;
                $statesBandeau['CADepotBandeauN1'] = 0;
                $statesBandeau['CADirectBandeauN1'] = 0;
                $statesBandeau['ClientBandeauN1'] = 0;
                $statesBandeau['BlBandeauN1'] = 0;
                $statesBandeau['FactureBandeauN1'] = 0;
                $statesBandeau['CATotalBandeauN'] = 0;
                $statesBandeau['CADepotBandeauN'] = 0;
                $statesBandeau['CADirectBandeauN'] = 0;
                $statesBandeau['ClientBandeauN'] = 0;
                $statesBandeau['BlBandeauN'] = 0;
                $statesBandeau['FactureBandeauN'] = 0;
                
                for ($ligStateDuMetier=0; $ligStateDuMetier <count($statesTotauxParSecteur) ; $ligStateDuMetier++) { 
                    if ($statesTotauxParSecteur[$ligStateDuMetier]['Periode'] == 'PeriodeN1' ) {
                        $statesBandeau['CATotalBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['CATotal'] ;
                        $statesBandeau['CADepotBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADepot'] ;
                        $statesBandeau['CADirectBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADirect'] ;
                        $statesBandeau['ClientBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbTiers'] ;
                        $statesBandeau['BlBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbBl'] ;
                        $statesBandeau['FactureBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbFacture'] ;
                    }elseif ($statesTotauxParSecteur[$ligStateDuMetier]['Periode'] == 'PeriodeN') {
                        $statesBandeau['CATotalBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['CATotal'] ;
                        $statesBandeau['CADepotBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADepot'] ;
                        $statesBandeau['CADirectBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADirect'] ;
                        $statesBandeau['ClientBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbTiers'] ;
                        $statesBandeau['BlBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbBl'] ;
                        $statesBandeau['FactureBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbFacture'] ;
                    }
                }
                // Pourcentage couleur et fléche du bandeau, il est possible de faire des beaux objet là, à réfléchir et améliorer !
                // Sur le Total
                $statesBandeau['DeltaTotalBandeau'] = $this->calcul_pourcentage($statesBandeau['CATotalBandeauN1'],$statesBandeau['CATotalBandeauN'])['pourc'];
                $statesBandeau['ColorTotalBandeau'] = $this->calcul_pourcentage($statesBandeau['CATotalBandeauN1'],$statesBandeau['CATotalBandeauN'])['color'];
                $statesBandeau['FlecheTotalBandeau'] = $this->calcul_pourcentage($statesBandeau['CATotalBandeauN1'],$statesBandeau['CATotalBandeauN'])['fleche'];

                // Sur le Direct
                $statesBandeau['DeltaDirectBandeau'] = $this->calcul_pourcentage($statesBandeau['CADirectBandeauN1'],$statesBandeau['CADirectBandeauN'])['pourc'];
                $statesBandeau['ColorDirectBandeau'] = $this->calcul_pourcentage($statesBandeau['CADirectBandeauN1'],$statesBandeau['CADirectBandeauN'])['color'];
                $statesBandeau['FlecheDirectBandeau'] = $this->calcul_pourcentage($statesBandeau['CADirectBandeauN1'],$statesBandeau['CADirectBandeauN'])['fleche'];

                // Sur le Depot
                $statesBandeau['DeltaDepotBandeau'] = $this->calcul_pourcentage($statesBandeau['CADepotBandeauN1'],$statesBandeau['CADepotBandeauN'])['pourc'];
                $statesBandeau['ColorDepotBandeau'] = $this->calcul_pourcentage($statesBandeau['CADepotBandeauN1'],$statesBandeau['CADepotBandeauN'])['color'];
                $statesBandeau['FlecheDepotBandeau'] = $this->calcul_pourcentage($statesBandeau['CADepotBandeauN1'],$statesBandeau['CADepotBandeauN'])['fleche'];

                // Sur le Client
                $statesBandeau['DeltaClientBandeau'] = $this->calcul_pourcentage($statesBandeau['ClientBandeauN1'],$statesBandeau['ClientBandeauN'])['pourc'];
                $statesBandeau['ColorClientBandeau'] = $this->calcul_pourcentage($statesBandeau['ClientBandeauN1'],$statesBandeau['ClientBandeauN'])['color'];
                $statesBandeau['FlecheClientBandeau'] = $this->calcul_pourcentage($statesBandeau['ClientBandeauN1'],$statesBandeau['ClientBandeauN'])['fleche'];

                // Sur le Bl
                $statesBandeau['DeltaBlBandeau'] = $this->calcul_pourcentage($statesBandeau['BlBandeauN1'],$statesBandeau['BlBandeauN'])['pourc'];
                $statesBandeau['ColorBlBandeau'] = $this->calcul_pourcentage($statesBandeau['BlBandeauN1'],$statesBandeau['BlBandeauN'])['color'];
                $statesBandeau['FlecheBlBandeau'] = $this->calcul_pourcentage($statesBandeau['BlBandeauN1'],$statesBandeau['BlBandeauN'])['fleche'];

                // Sur le Facture
                $statesBandeau['DeltaFactureBandeau'] = $this->calcul_pourcentage($statesBandeau['FactureBandeauN1'],$statesBandeau['FactureBandeauN'])['pourc'];
                $statesBandeau['ColorFactureBandeau'] = $this->calcul_pourcentage($statesBandeau['FactureBandeauN1'],$statesBandeau['FactureBandeauN'])['color'];
                $statesBandeau['FlecheFactureBandeau'] = $this->calcul_pourcentage($statesBandeau['FactureBandeauN1'],$statesBandeau['FactureBandeauN'])['fleche'];

                // Fin Bandeau Détaillé avec Nombre de facture, Bl, CA Dépôt, CA Direct etc ...
                
                // Début States par commerciaux
                $statesCommerciaux = $repo->getStatesTotauxParCommerciaux($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);

                for ($ligCom=0; $ligCom <count($statesCommerciaux) ; $ligCom++) {
                            $commercial[$ligCom]['Commercial'] = $statesCommerciaux[$ligCom]['Commercial'];
                            $commercial[$ligCom]['CommercialId'] = $statesCommerciaux[$ligCom]['CommercialId'];
                }
                if (isset($commercial)) {
                $listeCommerciaux = array_values(array_unique($commercial, SORT_REGULAR)); // faire une liste de commerciaux sans doublon
                    // pour chaque commercial dans le tableau

                    
                    for ($ligCommercial=0; $ligCommercial <count($listeCommerciaux) ; $ligCommercial++) {  
                        $Commercial = $listeCommerciaux[$ligCommercial]['Commercial'];                    
                        $stateCommerciaux[$ligCommercial]['Commercial'] = $listeCommerciaux[$ligCommercial]['Commercial'];
                        $stateCommerciaux[$ligCommercial]['CommercialId'] = $listeCommerciaux[$ligCommercial]['CommercialId'];
                        //$stateCommerciaux[$ligCommercial]['Periode'] = '';
                        $stateCommerciaux[$ligCommercial]['CATotalN'] = 0;
                        $stateCommerciaux[$ligCommercial]['CADepotN'] = 0;
                        $stateCommerciaux[$ligCommercial]['CADirectN'] = 0;
                        $stateCommerciaux[$ligCommercial]['ClientN'] = 0;
                        $stateCommerciaux[$ligCommercial]['FactureN'] = 0;
                        $stateCommerciaux[$ligCommercial]['BlN'] = 0;
                        $stateCommerciaux[$ligCommercial]['CATotalN1'] = 0;
                        $stateCommerciaux[$ligCommercial]['CADepotN1'] = 0;
                        $stateCommerciaux[$ligCommercial]['CADirectN1'] = 0;
                        $stateCommerciaux[$ligCommercial]['ClientN1'] = 0;
                        $stateCommerciaux[$ligCommercial]['FactureN1'] = 0;
                        $stateCommerciaux[$ligCommercial]['BlN1'] = 0;
                        for ($ligStateEntete=0; $ligStateEntete <count($statesCommerciaux) ; $ligStateEntete++) { 
                            if ($statesCommerciaux[$ligStateEntete]['Periode'] == 'PeriodeN' && $statesCommerciaux[$ligStateEntete]['Commercial'] == $Commercial) {
                                $stateCommerciaux[$ligCommercial]['CATotalN'] += $statesCommerciaux[$ligStateEntete]['CATotal'];
                                $stateCommerciaux[$ligCommercial]['CADepotN'] += $statesCommerciaux[$ligStateEntete]['CADepot'];
                                $stateCommerciaux[$ligCommercial]['CADirectN'] += $statesCommerciaux[$ligStateEntete]['CADirect'];
                                $stateCommerciaux[$ligCommercial]['ClientN'] += $statesCommerciaux[$ligStateEntete]['NbTiers'];
                                $stateCommerciaux[$ligCommercial]['FactureN'] += $statesCommerciaux[$ligStateEntete]['NbFacture'];
                                $stateCommerciaux[$ligCommercial]['BlN'] += $statesCommerciaux[$ligStateEntete]['NbBl'];
                            }elseif ($statesCommerciaux[$ligStateEntete]['Periode'] == 'PeriodeN1' && $statesCommerciaux[$ligStateEntete]['Commercial'] == $Commercial) {
                                $stateCommerciaux[$ligCommercial]['CATotalN1'] += $statesCommerciaux[$ligStateEntete]['CATotal'];
                                $stateCommerciaux[$ligCommercial]['CADepotN1'] += $statesCommerciaux[$ligStateEntete]['CADepot'];
                                $stateCommerciaux[$ligCommercial]['CADirectN1'] += $statesCommerciaux[$ligStateEntete]['CADirect'];
                                $stateCommerciaux[$ligCommercial]['ClientN1'] += $statesCommerciaux[$ligStateEntete]['NbTiers'];
                                $stateCommerciaux[$ligCommercial]['FactureN1'] += $statesCommerciaux[$ligStateEntete]['NbFacture'];
                                $stateCommerciaux[$ligCommercial]['BlN1'] += $statesCommerciaux[$ligStateEntete]['NbBl'];
                            }
                        }
                        
                        $stateCommerciaux[$ligCommercial]['DeltaTotal'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['CATotalN1'],$stateCommerciaux[$ligCommercial]['CATotalN'])['pourc'];
                        $stateCommerciaux[$ligCommercial]['ColorTotal'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['CATotalN1'],$stateCommerciaux[$ligCommercial]['CATotalN'])['color'];
                        $stateCommerciaux[$ligCommercial]['FlecheTotal'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['CATotalN1'],$stateCommerciaux[$ligCommercial]['CATotalN'])['fleche'];
                        
                        $stateCommerciaux[$ligCommercial]['DeltaClient'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['ClientN1'],$stateCommerciaux[$ligCommercial]['ClientN'])['pourc'];
                        $stateCommerciaux[$ligCommercial]['ColorClient'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['ClientN1'],$stateCommerciaux[$ligCommercial]['ClientN'])['color'];
                        $stateCommerciaux[$ligCommercial]['FlecheClient'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['ClientN1'],$stateCommerciaux[$ligCommercial]['ClientN'])['fleche'];
                        
                        // je dois récupérer via une requête SQL les states Globales pour les comparer avec les states par commerciaux, la requête semble préte, à contrôler

                                $stateCommerciaux[$ligCommercial]['deltaParTotalN1'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['CATotalN1'], $statesBandeau['CATotalBandeauN1']);
                                $stateCommerciaux[$ligCommercial]['deltaParClientN1'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['ClientN1'], $statesBandeau['ClientBandeauN1']);
                                $stateCommerciaux[$ligCommercial]['deltaParTotalN'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['CATotalN'], $statesBandeau['CATotalBandeauN']);
                                $stateCommerciaux[$ligCommercial]['deltaParClientN'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['ClientN'], $statesBandeau['ClientBandeauN']);
                        
                    }   
                    // Fin States par commerciaux

                    // Début States par client    
                        $statesParClient = $repo->getStatesDetailClient($metiers,$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier); 
                        
                        for ($ligClient=0; $ligClient <count($statesParClient) ; $ligClient++) { 
                            $statesParClient[$ligClient]['DeltaDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'],$statesParClient[$ligClient]['CATotalN'])['pourc'];
                            $statesParClient[$ligClient]['MontDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'],$statesParClient[$ligClient]['CATotalN'])['mont'];
                            $statesParClient[$ligClient]['ColorDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'],$statesParClient[$ligClient]['CATotalN'])['color'];
                            $statesParClient[$ligClient]['FlecheDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'],$statesParClient[$ligClient]['CATotalN'])['fleche'];
                        }
                     
                    // Fin States par client  

                    // Début States par Métier
                        $statesMetiers = $repo->getStatesMetier($dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
                        
                        for ($ligMetier=0; $ligMetier <count($statesMetiers) ; $ligMetier++) { 
                            $statesMetiers[$ligMetier]['DeltaMetier'] = $this->calcul_pourcentage($statesMetiers[$ligMetier]['CATotalN1'],$statesMetiers[$ligMetier]['CATotalN'])['pourc'];
                            $statesMetiers[$ligMetier]['ColorMetier'] = $this->calcul_pourcentage($statesMetiers[$ligMetier]['CATotalN1'],$statesMetiers[$ligMetier]['CATotalN'])['color'];
                            $statesMetiers[$ligMetier]['FlecheMetier'] = $this->calcul_pourcentage($statesMetiers[$ligMetier]['CATotalN1'],$statesMetiers[$ligMetier]['CATotalN'])['fleche'];
                        }
                    // Fin States par Métier

                }
                        
            }    
        return $this->render('states_lhermitte/index.html.twig', [
            'title' => 'States Lhermitte',
            'intervalN' => $intervalN,
            'dateDebutN' => $dateDebutN,
            'sufixeMetier' => $sufixeMetier,
            'dateFinN' => $dateFinN,
            'dateDebutN1' => $dateDebutN1,
            'dateFinN1' => $dateFinN1,
            'metier' => $metier,
            'dossier' => $dossier,
            'dateDebutEtFin' => $dateDebutEtFin,
            'intervalN1' => $intervalN1,
            'themeColor' => $themeColor,
            'statesMetiers' => $statesMetiers,
            'stateCommerciaux' => $stateCommerciaux,
            'statesBandeau' => $statesBandeau,
            'statesParClient' => $statesParClient,          
            'titre' => $titre,
            'dateDebutFinForm' => $form->createView()
            ]);
    }

    function cal_days_in_year($year){
        $days=0; 
        for($month=1;$month<=12;$month++){ 
            $days = $days + cal_days_in_month(CAL_GREGORIAN,$month,$year);
         }
     return $days;
    }

    public function metierParameter(string $metier){
        
        if ($metier == 'EV') {
            $secteur['metiers'] = '\'EV\'';
            $secteur['themeColor'] = 'success'; 
            $secteur['titre'] = ' Espaces Verts';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        }elseif ($metier == 'HP') {
            $secteur['metiers'] = '\'MA\', \'HP\'';
            $secteur['themeColor'] = 'danger'; 
            $secteur['titre'] = ' Horti - Pépi';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        }elseif ($metier == 'MA') {
            $secteur['metiers'] = '\'MA\'';
            $secteur['themeColor'] = 'orange'; 
            $secteur['titre'] = ' Maraîchage';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        }elseif ($metier == 'ME') {
            $secteur['metiers'] = '\'ME\'';
            $secteur['themeColor'] = 'warning'; 
            $secteur['titre'] = ' Matériel / Équipement';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        }elseif ($metier == 'Tous') {
            $secteur['metiers'] = '\'EV\', \'HP\', \'ME\', \'MA\'';
            $secteur['themeColor'] = 'info'; 
            $secteur['titre'] = ' Tous les métiers Lhermitte';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        }
        elseif ($metier == 'RB') {
            $secteur['metiers'] = '\'RB\'';
            $secteur['themeColor'] = 'primary'; 
            $secteur['titre'] = ' Roby';
            $secteur['sufixeRoute'] = 'Rb';
            $secteur['dossier'] = 3;
        }
        return $secteur;
    }

    // Export Excel par metier

    public function getDataMetier($metier, $dateDebutN, $dateFinN, $dossier, $repo): array
    {
        /**
         * @var $ticket Ticket[]
         */
        $list = [];
        $donnee = [];
        $donnees = $repo->getStatesExcelParMetier($metier, $dateDebutN, $dateFinN, $dossier);
        
        foreach ($donnees as $donnee) {
            
            $donnee['Delta'] = $this->calcul_pourcentage($donnee['MontantSignN1'],$donnee['MontantSignN'])['pourc']/100;
            $list[] = [
                $donnee['Commercial'],
                $donnee['Famille_Client'],
                $donnee['Client'],
                $donnee['Nom'],
                $donnee['Pays'],
                $donnee['Fam_Art'],
                $donnee['Ref'],
                $donnee['Designation'],
                $donnee['Sref1'],
                $donnee['Sref2'],
                $donnee['Uv'],
                $donnee['Mois'],
                $donnee['QteSignN1'],
                $donnee['MontantSignN1'],
                $donnee['Delta'],  
                $donnee['QteSignN'],  
                $donnee['MontantSignN'],                   
                
            ];
        }
        return $list;
    }
    
    
    /**
     * @Route("/Lhermitte/excel/{metier}/{dateDebutN}/{dateFinN}/{dossier}", name="app_states_excel_metier_Lh")
     * @Route("/Roby/excel/{metier}/{dateDebutN}/{dateFinN}/{dossier}", name="app_states_excel_metier_Rb")
     */  

    public function get_states_excel_metier($metier, $dateDebutN, $dateFinN, $dossier, StatesByTiersRepository $repo, Request $request){

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        //dd($tracking);
        
        $secteur = $this->metierParameter($metier);
        $metier = $secteur['metiers'];

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('States');
        // Entête de colonne
        $sheet->getCell('A5')->setValue('Commercial');
        $sheet->getCell('B5')->setValue('Famille_client');
        $sheet->getCell('C5')->setValue('Tiers');
        $sheet->getCell('D5')->setValue('Nom');
        $sheet->getCell('E5')->setValue('Pays');
        $sheet->getCell('F5')->setValue('Famille_article');
        $sheet->getCell('G5')->setValue('Ref');
        $sheet->getCell('H5')->setValue('Designation');
        $sheet->getCell('I5')->setValue('Sref1');
        $sheet->getCell('J5')->setValue('Sref2');
        $sheet->getCell('K5')->setValue('Uv');
        $sheet->getCell('L5')->setValue('Mois');
        $sheet->getCell('M5')->setValue('QteSignN1');
        $sheet->getCell('N5')->setValue('MontantSignN1');
        $sheet->getCell('O5')->setValue('Delta');
        $sheet->getCell('P5')->setValue('QteSignN');
        $sheet->getCell('Q5')->setValue('MontantSignN');

        // Increase row cursor after header write
        $sheet->fromArray($this->getDataMetier($metier, $dateDebutN, $dateFinN, $dossier, $repo),null, 'A6', true);
        $dernLign = count($this->getDataMetier($metier, $dateDebutN, $dateFinN, $dossier, $repo)) + 5;    
        //$NomCommercial = $repo->getCommercialName($commercialId, $dossier);
        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y') ;
        //$commercial = $NomCommercial[0]['SELCOD'];
        $nomFichier = 'States Métier =>' . $metier . ' du ' . $dateDebutN . ' au ' . $dateFinN . ' le '. $dateTime ;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);
        
        $styleArray = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle("A5:Q{$dernLign}")->applyFromArray($styleArray);
        
        $styleArrayEntete = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'F17A2B8',
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle("A5:Q5")->applyFromArray($styleArrayEntete);
        
        $sheet->getStyle("H1:Q{$dernLign}")
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setAutoFilter("A5:Q{$dernLign}");
        $sheet->getColumnDimension('A')->setWidth(30, 'pt');
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->setCellValue("N4", "=SUBTOTAL(9,N6:N{$dernLign})");
        $sheet->setCellValue("Q4", "=SUBTOTAL(9,Q6:Q{$dernLign})");
        $sheet->setCellValue("O4", "=(Q4/N4)-1");
        $sheet->getStyle("N6:N{$dernLign}")
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        $sheet->getStyle("Q6:Q{$dernLign}")
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        $sheet->getStyle("N4:Q4")
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        $sheet->getStyle('O4')
              ->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle("O6:O{$dernLign}")
              ->getNumberFormat()
              ->setFormatCode('[Green][>=0]#.##0%;[Red][<0]#.##0%;#.##0%');

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }

    // Export Excel par commercial

    public function getDataCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, $repo): array
    {
        /**
         * @var $ticket Ticket[]
         */
        $list = [];
        $donnee = [];
        $donnees = $repo->getStatesExcelParCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier);

        foreach ($donnees as $donnee) {
            $donnee['Delta'] = $this->calcul_pourcentage($donnee['MontantSignN1'],$donnee['MontantSignN'])['pourc']/100;
            $list[] = [
                $donnee['Famille_Client'],
                $donnee['Client'],
                $donnee['Nom'],
                $donnee['Pays'],
                $donnee['Fam_Art'],
                $donnee['Ref'],
                $donnee['Designation'],
                $donnee['Sref1'],
                $donnee['Sref2'],
                $donnee['Uv'],
                $donnee['Mois'],
                $donnee['QteSignN1'],
                $donnee['MontantSignN1'],
                $donnee['Delta'],  
                $donnee['QteSignN'],  
                $donnee['MontantSignN'],                   
                
            ];
        }
        return $list;
    }
    
    
    /**
     * @Route("/Lhermitte/excel/{metier}/{dateDebutN}/{dateFinN}/{commercialId}/{dossier}", name="app_states_excel_commercial_Lh")
     * @Route("/Roby/excel/{metier}/{dateDebutN}/{dateFinN}/{commercialId}/{dossier}", name="app_states_excel_commercial_Rb")
     */  

    public function get_states_excel_commercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, StatesByTiersRepository $repo, Request $request){

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        //dd($tracking);
        
        $secteur = $this->metierParameter($metier);
        $metier = $secteur['metiers'];

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('States');
        // Entête de colonne
        $sheet->getCell('A5')->setValue('Famille_client');
        $sheet->getCell('B5')->setValue('Tiers');
        $sheet->getCell('C5')->setValue('Nom');
        $sheet->getCell('D5')->setValue('Pays');
        $sheet->getCell('E5')->setValue('Famille_article');
        $sheet->getCell('F5')->setValue('Ref');
        $sheet->getCell('G5')->setValue('Designation');
        $sheet->getCell('H5')->setValue('Sref1');
        $sheet->getCell('I5')->setValue('Sref2');
        $sheet->getCell('J5')->setValue('Uv');
        $sheet->getCell('K5')->setValue('Mois');
        $sheet->getCell('L5')->setValue('QteSignN1');
        $sheet->getCell('M5')->setValue('MontantSignN1');
        $sheet->getCell('N5')->setValue('Delta');
        $sheet->getCell('O5')->setValue('QteSignN');
        $sheet->getCell('P5')->setValue('MontantSignN');

        // Increase row cursor after header write
        $sheet->fromArray($this->getDataCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, $repo),null, 'A6', true);
        $dernLign = count($this->getDataCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, $repo)) + 5;    
        $NomCommercial = $repo->getCommercialName($commercialId, $dossier);
        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y') ;
        $commercial = $NomCommercial[0]['SELCOD'];
        $nomFichier = 'States ' . $commercial  . ' Métier =>' . $metier . ' du ' . $dateDebutN . ' au ' . $dateFinN . ' le '. $dateTime ;
        $sheet->getCell('A1')->setValue($nomFichier);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);
        
        $styleArray = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];

        $styleArrayEntete = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'F17A2B8',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("A5:P{$dernLign}")->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle("A5:P5")->applyFromArray($styleArrayEntete);
        $sheet->getStyle("H1:P{$dernLign}")
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setAutoFilter("A5:P{$dernLign}");
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->setCellValue("M4", "=SUBTOTAL(9,M6:M{$dernLign})");
        $sheet->setCellValue("P4", "=SUBTOTAL(9,P6:P{$dernLign})");
        $sheet->setCellValue("N4", "=(P4/M4)-1");
        $sheet->getStyle("M6:M{$dernLign}")
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        $sheet->getStyle("P6:P{$dernLign}")
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        $sheet->getStyle("M4:P4")
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        $sheet->getStyle('N4')
              ->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle("N6:N{$dernLign}")
              ->getNumberFormat()
              ->setFormatCode('[Green][>=0]#.##0%;[Red][<0]#.##0%;#.##0%');


        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
    
    public function dateParameter($Param){
        
        // faire un objet avec la gestion des intervals de date
        $dateDebut = substr($Param, 0 , -13);
        $anneeDebut = substr($dateDebut, 6, 4);
        $moisDebut = substr($dateDebut, 0, 2);
        $jourDebut = substr($dateDebut, 3, 2);
   
        $dateFin = substr($Param, 13 , 10);
        $anneeFin = substr($dateFin, 6, 4);
        $moisFin = substr($dateFin, 0, 2);
        $jourFin = substr($dateFin, 3, 2);

        // Calculer correctement le comparatif de date 
        $intervalAnnee = ($anneeFin - $anneeDebut) + 1;
        $yearStartN1 = $anneeDebut - $intervalAnnee ;
        $yearEndN1 = $anneeFin - $intervalAnnee ;

        $dateParam['dateDebutN'] = $anneeDebut . '-' . $moisDebut . '-' . $jourDebut;
        $dateParam['dateDebutN1'] = $yearStartN1 . '-' . $moisDebut . '-' . $jourDebut;
        $dateParam['dateFinN'] = $anneeFin . '-' . $moisFin . '-' . $jourFin;
        $dateParam['dateFinN1'] = $yearEndN1 . '-' . $moisFin . '-' . $jourFin;
        $dateParam['intervalN'] = $jourDebut . '-' . $moisDebut . '-' . $anneeDebut . ' - ' . $jourFin . '-' . $moisFin . '-' . $anneeFin;
        $dateParam['intervalN1'] = $jourDebut . '-' . $moisDebut . '-' . $yearStartN1 . ' - ' . $jourFin . '-' . $moisFin . '-' . $yearEndN1;

        return $dateParam;
    }



    /**
     * @Route("/Lhermitte/states/archive/inutiliser", name="app_states_lhermitte_archive")
     */    
    public function statesInutiliser(string $secteur, StatesByTiersRepository $repo, Request $request):Response
    {
                    
        $secteur = $request->attributes->get('secteur');
        $themeColor = '';

        if ($secteur == 'EV') {
            $themeColor = 'success'; 
            $titre = ' Espaces Verts'; 
        } 
        if ($secteur == 'HP') {
            $themeColor = 'danger';
            $titre = ' Horti - Pépi';
        }
        if ($secteur == 'ME') {
            $themeColor = 'warning';
            $titre = ' Matériel équipement';
        }
        if ($secteur == 'LH') {
            $themeColor = 'info';
            $titre = ' tous secteurs';
        }
        if ($secteur == 'MA') {
            $themeColor = 'orange';
            $titre = 'Maraîchage';
        }    

            $secteurRecherche = $secteur;
            $form = $this->createForm(DateDebutFinType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';
            $state = array();
            $clientFilter = array();
            $intervalN = 0;
            $intervalN1 = 0;
            $commercial = array();

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $dateDebutEtFin = $form->getData()['dateFin'];
            
                $dateDebut = substr($dateDebutEtFin, 0 , -13);
                $anneeDebut = substr($dateDebut, 6, 4);
                $moisDebut = substr($dateDebut, 0, 2);
                $jourDebut = substr($dateDebut, 3, 2);
           
                $dateFin = substr($dateDebutEtFin, 13 , 10);
                $anneeFin = substr($dateFin, 6, 4);
                $moisFin = substr($dateFin, 0, 2);
                $jourFin = substr($dateFin, 3, 2);

                if ($anneeDebut != $anneeFin) {
                    $this->addFlash('danger', 'les Années de Début et de fin doivent être identiques');
                    return $this->redirectToRoute($tracking, ['secteur' => $secteur]);
                }

                $dateDebutN = $anneeDebut . '-' . $moisDebut . '-' . $jourDebut;
                $dateDebutN1 = $anneeDebut -1 . '-' . $moisDebut . '-' . $jourDebut;

                $dateFinN = $anneeFin . '-' . $moisFin . '-' . $jourFin;
                $dateFinN1 = $anneeFin -1 . '-' . $moisFin . '-' . $jourFin;

                $yearStartN1 = $anneeDebut - 1 ;
                $yearEndN1 = $anneeFin - 1 ;

                $intervalN = $jourDebut . '-' . $moisDebut . '-' . $anneeDebut . ' au ' . $jourFin . '-' . $moisFin . '-' . $anneeFin;
                $intervalN1 = $jourDebut . '-' . $moisDebut . '-' . $yearStartN1 . ' au ' . $jourFin . '-' . $moisFin . '-' . $yearEndN1;

                $statesGlobal = $repo->getStatesLhermitteGlobalesByMonth($dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1);
                $annee = $anneeDebut;
                // States Globales du secteur
                // Nbre de client du secteur N et N-1 ainsi que le Delta
                for ($ligClient=0; $ligClient <count($statesGlobal) ; $ligClient++) {
                    if ($secteur == 'LH') {
                        $client[$ligClient]['annee'] = $statesGlobal[$ligClient]['Annee'];
                        $client[$ligClient]['tiers'] = $statesGlobal[$ligClient]['Tiers'];
                        $client[$ligClient]['commercial'] = $statesGlobal[$ligClient]['Commercial'];
                        $client[$ligClient]['commercialId'] = $statesGlobal[$ligClient]['CommercialId'];
                    }else {
                        if ($secteur == 'MA') {
                            if ($statesGlobal[$ligClient]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligClient]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligClient]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligClient]['FamClient'] == 'ASSO' ) ) {
                                $client[$ligClient]['annee'] = $statesGlobal[$ligClient]['Annee'];
                                $client[$ligClient]['tiers'] = $statesGlobal[$ligClient]['Tiers'];
                                $client[$ligClient]['commercial'] = $statesGlobal[$ligClient]['Commercial'];
                                $client[$ligClient]['commercialId'] = $statesGlobal[$ligClient]['CommercialId'];
                            }
                        }else {
                            if ($statesGlobal[$ligClient]['SecteurMouvement'] == $secteurRecherche) {
                                $client[$ligClient]['annee'] = $statesGlobal[$ligClient]['Annee'];
                                $client[$ligClient]['tiers'] = $statesGlobal[$ligClient]['Tiers'];
                                $client[$ligClient]['commercial'] = $statesGlobal[$ligClient]['Commercial'];
                                $client[$ligClient]['commercialId'] = $statesGlobal[$ligClient]['CommercialId'];
                            } 
                        }
                    }                    
                }
                if (isset($client)) {
                    $nbreclient = array_values(array_unique($client, SORT_REGULAR));
                    $state['count']['client']['anneeN'] = 0; 
                    $state['count']['client']['anneeN1'] = 0;
                    for ($nbcli=0; $nbcli <count($nbreclient) ; $nbcli++) { 
                        if ($nbreclient[$nbcli]['annee'] == $annee) {
                            $state['count']['client']['anneeN']++;
                        }
                        if ($nbreclient[$nbcli]['annee'] == $annee -1) {
                            $state['count']['client']['anneeN1']++;
                        }
                    }
                     // Delta client
                     $state['count']['client']['delta'] = $this->calcul_pourcentage($state['count']['client']['anneeN1'],$state['count']['client']['anneeN'])['pourc'];
                     $state['count']['client']['color'] = $this->calcul_pourcentage($state['count']['client']['anneeN1'],$state['count']['client']['anneeN'])['color'];
                     $state['count']['client']['fleche'] = $this->calcul_pourcentage($state['count']['client']['anneeN1'],$state['count']['client']['anneeN'])['fleche'];      
                }
                // Nbre de BL du secteur N et N-1 ainsi que le Delta
                for ($ligBl=0; $ligBl <count($statesGlobal) ; $ligBl++) {
                    if ($secteur == 'LH') {
                            $bLivraison[$ligBl]['annee'] = $statesGlobal[$ligBl]['Annee'];
                            $bLivraison[$ligBl]['bl'] = $statesGlobal[$ligBl]['Bl'];
                    }else {
                        if ($secteur == 'MA') {
                            if ($statesGlobal[$ligBl]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligBl]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligBl]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligBl]['FamClient'] == 'ASSO' )) {
                                $bLivraison[$ligBl]['annee'] = $statesGlobal[$ligBl]['Annee'];
                                $bLivraison[$ligBl]['bl'] = $statesGlobal[$ligBl]['Bl'];
                            }
                        }else {
                            if ($statesGlobal[$ligBl]['SecteurMouvement'] == $secteurRecherche) {
                                $bLivraison[$ligBl]['annee'] = $statesGlobal[$ligBl]['Annee'];
                                $bLivraison[$ligBl]['bl'] = $statesGlobal[$ligBl]['Bl'];
                            }
                        } 
                    }
                }
                if (isset($bLivraison)) {
                    $nbreBl = array_values(array_unique($bLivraison, SORT_REGULAR));
                    $state['count']['bl']['anneeN'] = 0;
                    $state['count']['bl']['anneeN1'] = 0;
                    for ($nbBl=0; $nbBl <count($nbreBl) ; $nbBl++) { 
                        if ($nbreBl[$nbBl]['annee'] == $annee) {
                            $state['count']['bl']['anneeN']++;
                        } 
                        if ($nbreBl[$nbBl]['annee'] == $annee -1) {
                            $state['count']['bl']['anneeN1']++;
                        }
                    }
                     // Delta BL
                     $state['count']['bl']['delta'] = $this->calcul_pourcentage($state['count']['bl']['anneeN1'],$state['count']['bl']['anneeN'])['pourc'];
                     $state['count']['bl']['color'] = $this->calcul_pourcentage($state['count']['bl']['anneeN1'],$state['count']['bl']['anneeN'])['color'];
                     $state['count']['bl']['fleche'] = $this->calcul_pourcentage($state['count']['bl']['anneeN1'],$state['count']['bl']['anneeN'])['fleche'];                    
                }

                // Nbre de Facture du secteur N et N-1 ainsi que le Delta
                for ($ligFacture=0; $ligFacture <count($statesGlobal) ; $ligFacture++) {
                    if ($secteur == 'LH') {
                            $facture[$ligFacture]['annee'] = $statesGlobal[$ligFacture]['Annee'];
                            $facture[$ligFacture]['facture'] = $statesGlobal[$ligFacture]['Facture'];
                    }else{
                        if ($secteur == 'MA') {
                            if ($statesGlobal[$ligFacture]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligFacture]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligFacture]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligFacture]['FamClient'] == 'ASSO' ) ) {
                                $facture[$ligFacture]['annee'] = $statesGlobal[$ligFacture]['Annee'];
                                $facture[$ligFacture]['facture'] = $statesGlobal[$ligFacture]['Facture'];
                            }
                        }else {
                            if ($statesGlobal[$ligFacture]['SecteurMouvement'] == $secteurRecherche) {
                                $facture[$ligFacture]['annee'] = $statesGlobal[$ligFacture]['Annee'];
                                $facture[$ligFacture]['facture'] = $statesGlobal[$ligFacture]['Facture'];
                            }
                        } 
                    }
                }
                if (isset($facture)) {
                    $nbreFact = array_values(array_unique($facture, SORT_REGULAR));
                    $state['count']['facture']['anneeN'] = 0;
                    $state['count']['facture']['anneeN1'] = 0;
                    for ($facture=0; $facture <count($nbreFact) ; $facture++) { 
                        if ($nbreFact[$facture]['annee'] == $annee) {
                            $state['count']['facture']['anneeN']++;
                        }
                        if ($nbreFact[$facture]['annee'] == $annee -1) {
                            $state['count']['facture']['anneeN1']++;
                        }
                    }
                     // Delta Facture
                     $state['count']['facture']['delta'] = $this->calcul_pourcentage($state['count']['facture']['anneeN1'],$state['count']['facture']['anneeN'])['pourc'];
                     $state['count']['facture']['color'] = $this->calcul_pourcentage($state['count']['facture']['anneeN1'],$state['count']['facture']['anneeN'])['color'];
                     $state['count']['facture']['fleche'] = $this->calcul_pourcentage($state['count']['facture']['anneeN1'],$state['count']['facture']['anneeN'])['fleche'];                    
                }

                // Sum du secteur Dépôt N et N-1 ainsi que le Delta
                // Sum du secteur Direct N et N-1 ainsi que le Delta
                // Sum Total du secteur N et N-1 ainsi que le Delta
                $state['depot']['montantN'] = 0;
                $state['depot']['montantN1'] = 0;
                $state['direct']['montantN'] = 0;
                $state['direct']['montantN1'] = 0;
                $state['secteur']['montantN'] = 0;
                $state['secteur']['montantN1'] = 0;

                for ($ligne=0; $ligne <count($statesGlobal) ; $ligne++) { 
                    // si states globales
                    if ($secteur == 'LH') {
                        if ($statesGlobal[$ligne]['Annee'] == $annee ) 
                        {
                             if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                                 $state['depot']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                             } 
                             if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                                 $state['direct']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                             }
                             $state['secteur']['montantN'] += $statesGlobal[$ligne]['MontantSign'];  
                        }
                        
                        if ($statesGlobal[$ligne]['Annee'] == $annee -1 ) 
                        {
                             if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                                 $state['depot']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                             } 
                             if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                                 $state['direct']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                             }
                             $state['secteur']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                        }
                    // si states par secteur
                    }else {
                    if ($secteur == 'MA') {
                        if ($statesGlobal[$ligne]['Annee'] == $annee && $statesGlobal[$ligne]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligne]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligne]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligne]['FamClient'] == 'ASSO' )  ) 
                        {
                             if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                                 $state['depot']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                             } 
                             if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                                 $state['direct']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                             }
                             $state['secteur']['montantN'] += $statesGlobal[$ligne]['MontantSign'];  
                        }
                        
                        if ($statesGlobal[$ligne]['Annee'] == $annee -1 && $statesGlobal[$ligne]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligne]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligne]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligne]['FamClient'] == 'ASSO' )   ) 
                        {
                             if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                                 $state['depot']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                             } 
                             if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                                 $state['direct']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                             }
                             $state['secteur']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                        }
                    }else {
                        if ($statesGlobal[$ligne]['Annee'] == $annee && $statesGlobal[$ligne]['SecteurMouvement'] == $secteurRecherche) 
                        {
                             if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                                 $state['depot']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                             } 
                             if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                                 $state['direct']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                             }
                             $state['secteur']['montantN'] += $statesGlobal[$ligne]['MontantSign'];  
                        }
                        
                        if ($statesGlobal[$ligne]['Annee'] == $annee -1 && $statesGlobal[$ligne]['SecteurMouvement'] == $secteurRecherche) 
                        {
                             if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                                 $state['depot']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                             } 
                             if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                                 $state['direct']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                             }
                             $state['secteur']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                        }
                    }
                    }
                }
                
                // Delta Dépôt
                    $state['depot']['delta'] = $this->calcul_pourcentage($state['depot']['montantN1'],$state['depot']['montantN'])['pourc'];
                    $state['depot']['color'] = $this->calcul_pourcentage($state['depot']['montantN1'],$state['depot']['montantN'])['color'];
                    $state['depot']['fleche'] = $this->calcul_pourcentage($state['depot']['montantN1'],$state['depot']['montantN'])['fleche'];

                // Delta Direct
                    $state['direct']['delta'] = $this->calcul_pourcentage($state['direct']['montantN1'],$state['direct']['montantN'])['pourc'];
                    $state['direct']['color'] = $this->calcul_pourcentage($state['direct']['montantN1'],$state['direct']['montantN'])['color'];
                    $state['direct']['fleche'] = $this->calcul_pourcentage($state['direct']['montantN1'],$state['direct']['montantN'])['fleche'];
    
                // Delta Secteur
                    $state['secteur']['delta'] = $this->calcul_pourcentage($state['secteur']['montantN1'],$state['secteur']['montantN'])['pourc'];
                    $state['secteur']['color'] = $this->calcul_pourcentage($state['secteur']['montantN1'],$state['secteur']['montantN'])['color'];
                    $state['secteur']['fleche'] = $this->calcul_pourcentage($state['secteur']['montantN1'],$state['secteur']['montantN'])['fleche'];                             
                
                
                // States par commercial du secteur
                // Sum par commercial du secteur N et N-1 ainsi que le Delta
                for ($ligCom=0; $ligCom <count($statesGlobal) ; $ligCom++) {
                    if ($secteur == 'LH') {
                            $commercial[$ligCom]['commercial'] = $statesGlobal[$ligCom]['Commercial'];
                    }else {
                    if ($secteur == 'MA') {
                        if ($statesGlobal[$ligCom]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligCom]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligCom]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligCom]['FamClient'] == 'ASSO' ) ) {
                            $commercial[$ligCom]['commercial'] = $statesGlobal[$ligCom]['Commercial'];
                        }
                    }else {
                        if ($statesGlobal[$ligCom]['SecteurMouvement'] == $secteurRecherche) {
                            $commercial[$ligCom]['commercial'] = $statesGlobal[$ligCom]['Commercial'];
                        }
                    }
                    }
                }
                if (isset($commercial)) {
                $listeCommercial = array_values(array_unique($commercial, SORT_REGULAR));
                    // pour chaque commercial dans le tableau
                    for ($tabCommercial=0; $tabCommercial <count($listeCommercial) ; $tabCommercial++) {
                        $ceCommercial = $listeCommercial[$tabCommercial]['commercial'];
                        
                        $state['commercial'][$ceCommercial] = array();
                        $state['commercial'][$ceCommercial]['nom'] = $ceCommercial;
                        $state['commercial'][$ceCommercial]['montantN'] = 0;
                        $state['commercial'][$ceCommercial]['montantN1'] = 0;
                        
                        // pour chaque ligne des states
                        for ($ligStatesGlobales=0; $ligStatesGlobales<count($statesGlobal) ; $ligStatesGlobales++){ 
                            if ($statesGlobal[$ligStatesGlobales]['Commercial'] == $ceCommercial) {
                                if ($secteur == 'MA') {
                                    if ($statesGlobal[$ligStatesGlobales]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ligStatesGlobales]['FamClient'] == 'MARAICHE' || $statesGlobal[$ligStatesGlobales]['FamClient'] == 'AGRICULT' || $statesGlobal[$ligStatesGlobales]['FamClient'] == 'ASSO' )) {
                                        if ($statesGlobal[$ligStatesGlobales]['Annee'] == $annee) {
                                            $state['commercial'][$ceCommercial]['montantN'] += $statesGlobal[$ligStatesGlobales]['MontantSign'];
                                        }
                                        if ($statesGlobal[$ligStatesGlobales]['Annee'] == $annee -1) {
                                            $state['commercial'][$ceCommercial]['montantN1'] += $statesGlobal[$ligStatesGlobales]['MontantSign'];
                                        }
                                    }
                                }
                                else{
                                    if ($statesGlobal[$ligStatesGlobales]['Annee'] == $annee) {
                                        $state['commercial'][$ceCommercial]['montantN'] += $statesGlobal[$ligStatesGlobales]['MontantSign'];
                                    }
                                    if ($statesGlobal[$ligStatesGlobales]['Annee'] == $annee -1) {
                                        $state['commercial'][$ceCommercial]['montantN1'] += $statesGlobal[$ligStatesGlobales]['MontantSign'];
                                    }
                                }
                            }
                        }
                        $state['commercial'][$ceCommercial]['deltaTotalN'] = $this->calcul_pourcentage_total($state['commercial'][$ceCommercial]['montantN'], $state['secteur']['montantN']);
                        $state['commercial'][$ceCommercial]['deltaTotalN1'] = $this->calcul_pourcentage_total($state['commercial'][$ceCommercial]['montantN1'], $state['secteur']['montantN1']);
                        $state['commercial'][$ceCommercial]['deltaMontant'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['montantN1'],$state['commercial'][$ceCommercial]['montantN'])['pourc'];
                        $state['commercial'][$ceCommercial]['colorMontant'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['montantN1'],$state['commercial'][$ceCommercial]['montantN'])['color'];
                        $state['commercial'][$ceCommercial]['flecheMontant'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['montantN1'],$state['commercial'][$ceCommercial]['montantN'])['fleche']; 
                    }
                    // Nbre de client par commercial du secteur N et N-1 ainsi que le Delta
                    
                    for ($tabCommercial=0; $tabCommercial <count($listeCommercial) ; $tabCommercial++) {
                        $ceCommercial = $listeCommercial[$tabCommercial]['commercial'];
                        
                        $state['commercial'][$ceCommercial]['clientN'] = 0;
                        $state['commercial'][$ceCommercial]['clientN1'] = 0;
                        
                        // pour chaque ligne des states
                    for ($ligListeClient=0; $ligListeClient<count($nbreclient) ; $ligListeClient++){ 
                        if ($nbreclient[$ligListeClient]['commercial'] == $ceCommercial) {
                            if ($nbreclient[$ligListeClient]['annee'] == $annee) {
                                $state['commercial'][$ceCommercial]['clientN']++;
                            }
                            if ($nbreclient[$ligListeClient]['annee'] == $annee -1) {
                                $state['commercial'][$ceCommercial]['clientN1']++;
                            }
                        }
                    }
                    $state['commercial'][$ceCommercial]['deltaTotalClientN'] = $this->calcul_pourcentage_total($state['commercial'][$ceCommercial]['clientN'], $state['count']['client']['anneeN']);
                    $state['commercial'][$ceCommercial]['deltaTotalClientN1'] = $this->calcul_pourcentage_total($state['commercial'][$ceCommercial]['clientN1'], $state['count']['client']['anneeN1']);
                    $state['commercial'][$ceCommercial]['deltaClient'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['clientN1'],$state['commercial'][$ceCommercial]['clientN'])['pourc'];
                    $state['commercial'][$ceCommercial]['colorClient'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['clientN1'],$state['commercial'][$ceCommercial]['clientN'])['color'];
                    $state['commercial'][$ceCommercial]['flecheClient'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['clientN1'],$state['commercial'][$ceCommercial]['clientN'])['fleche']; 
                }
                }else{
                    $commercial = '';
                }
               
                // Sum par client du Secteur N et N-1 ainsi que le Delta
                for ($ListeClients=0; $ListeClients <count($statesGlobal) ; $ListeClients++) {
                    if ($secteur == 'LH') {
                        $clientFilter[$ListeClients]['commercial'] = $statesGlobal[$ListeClients]['Commercial'];
                        $clientFilter[$ListeClients]['commercialId'] = $statesGlobal[$ListeClients]['CommercialId'];
                        $clientFilter[$ListeClients]['tiers'] = $statesGlobal[$ListeClients]['Tiers'];
                        $clientFilter[$ListeClients]['nom'] = $statesGlobal[$ListeClients]['Nom'];
                        $clientFilter[$ListeClients]['montantN'] = 0;
                        $clientFilter[$ListeClients]['montantN1'] = 0;
                    }else {
                    if ($secteur == 'MA') {
                        if ($statesGlobal[$ListeClients]['SecteurMouvement'] == 'HP' && ($statesGlobal[$ListeClients]['FamClient'] == 'MARAICHE' || $statesGlobal[$ListeClients]['FamClient'] == 'AGRICULT' || $statesGlobal[$ListeClients]['FamClient'] == 'ASSO' )) {
                            $clientFilter[$ListeClients]['commercial'] = $statesGlobal[$ListeClients]['Commercial'];
                            $clientFilter[$ListeClients]['commercialId'] = $statesGlobal[$ListeClients]['CommercialId'];
                            $clientFilter[$ListeClients]['tiers'] = $statesGlobal[$ListeClients]['Tiers'];
                            $clientFilter[$ListeClients]['nom'] = $statesGlobal[$ListeClients]['Nom'];
                            $clientFilter[$ListeClients]['montantN'] = 0;
                            $clientFilter[$ListeClients]['montantN1'] = 0;
                        }
                    }else {
                        if ($statesGlobal[$ListeClients]['SecteurMouvement'] == $secteurRecherche) {
                            $clientFilter[$ListeClients]['commercial'] = $statesGlobal[$ListeClients]['Commercial'];
                            $clientFilter[$ListeClients]['commercialId'] = $statesGlobal[$ListeClients]['CommercialId'];
                            $clientFilter[$ListeClients]['tiers'] = $statesGlobal[$ListeClients]['Tiers'];
                            $clientFilter[$ListeClients]['nom'] = $statesGlobal[$ListeClients]['Nom'];
                            $clientFilter[$ListeClients]['montantN'] = 0;
                            $clientFilter[$ListeClients]['montantN1'] = 0;
                        }
                    }
                    }
                }
                $clientFilter = array_values(array_unique($clientFilter, SORT_REGULAR));
                // pour chaque client
                for ($ligClient=0; $ligClient <count($clientFilter) ; $ligClient++) { 
                    
                    for ($statesClients=0; $statesClients<count($statesGlobal) ; $statesClients++) { 
                        if ($secteur == 'LH') {
                            if ($statesGlobal[$statesClients]['Annee'] == $annee -1 && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                                $clientFilter[$ligClient]['montantN1'] += $statesGlobal[$statesClients]['MontantSign'];
                            }
                            if ($statesGlobal[$statesClients]['Annee'] == $annee && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                                $clientFilter[$ligClient]['montantN'] += $statesGlobal[$statesClients]['MontantSign'];
                            }
                        }else {
                        if ($secteur == 'MA') {
                            if ($statesGlobal[$statesClients]['Annee'] == $annee -1 && $statesGlobal[$statesClients]['SecteurMouvement'] == 'HP'  && ($statesGlobal[$statesClients]['FamClient'] == 'MARAICHE' || $statesGlobal[$statesClients]['FamClient'] == 'AGRICULT' || $statesGlobal[$statesClients]['FamClient'] == 'ASSO' )    && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                                $clientFilter[$ligClient]['montantN1'] += $statesGlobal[$statesClients]['MontantSign'];
                            }
                            if ($statesGlobal[$statesClients]['Annee'] == $annee && $statesGlobal[$statesClients]['SecteurMouvement'] == 'HP'    && ($statesGlobal[$statesClients]['FamClient'] == 'MARAICHE' || $statesGlobal[$statesClients]['FamClient'] == 'AGRICULT' || $statesGlobal[$statesClients]['FamClient'] == 'ASSO' )     && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                                $clientFilter[$ligClient]['montantN'] += $statesGlobal[$statesClients]['MontantSign'];
                            }
                        }else {
                            if ($statesGlobal[$statesClients]['Annee'] == $annee -1 && $statesGlobal[$statesClients]['SecteurMouvement'] == $secteurRecherche && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                                $clientFilter[$ligClient]['montantN1'] += $statesGlobal[$statesClients]['MontantSign'];
                            }
                            if ($statesGlobal[$statesClients]['Annee'] == $annee && $statesGlobal[$statesClients]['SecteurMouvement'] == $secteurRecherche && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                                $clientFilter[$ligClient]['montantN'] += $statesGlobal[$statesClients]['MontantSign'];
                            }
                        }
                        }
                    }
                    $clientFilter[$ligClient]['delta'] = $this->calcul_pourcentage($clientFilter[$ligClient]['montantN1'] ,$clientFilter[$ligClient]['montantN'] )['pourc'];
                    $clientFilter[$ligClient]['color'] = $this->calcul_pourcentage($clientFilter[$ligClient]['montantN1'] ,$clientFilter[$ligClient]['montantN'] )['color'];
                    $clientFilter[$ligClient]['fleche'] = $this->calcul_pourcentage($clientFilter[$ligClient]['montantN1'] ,$clientFilter[$ligClient]['montantN'] )['fleche'];
                }
               
                // States par article page séparé
                // Liste des articles par client du secteur N et N-1 ainsi que le Delta

            }

            return $this->render('states_lhermitte/index.html.twig', [
                'controller_name' => 'StatesLhermitteController',
                'title' => 'States Lhermitte',
                'mois' => $mois,
                'annee' => $annee,
                'intervalN' => $intervalN,
                'commercial' => $commercial,
                'intervalN1' => $intervalN1,
                'themeColor' => $themeColor,
                'state' => $state,
                'clients' => $clientFilter,
                'titre' => $titre,
                'dateDebutFinForm' => $form->createView()
                ]);

        }

        // fonction utile pour le cacul de pourcentage et éviter le calcul est impossible
        function calcul_pourcentage_total($nombreParCom, $nombreTotal)
        {
            if ($nombreParCom > 0 && $nombreTotal > 0) {
               $resultat = ($nombreParCom*100)/$nombreTotal;
            }else{
                $resultat = 0;    
            }
            return $resultat;
        }

        function calcul_pourcentage($nombreN1,$nombreN)
        { 
            
            if ($nombreN1 <> 0 && $nombreN <> 0) {
                $resultat['pourc'] = (($nombreN/$nombreN1) -1)*100;
                $resultat['mont'] = $nombreN - $nombreN1;
                if ($resultat['pourc'] < 0) {
                    $resultat['color'] = 'danger';
                    $resultat['fleche'] = 'down';
                }
                if ($resultat['pourc'] > 0) {
                    $resultat['color'] = 'success';
                    $resultat['fleche'] = 'up';
                }
                if ($nombreN1 == $nombreN) {
                    $resultat['pourc'] = 0;
                    $resultat['color'] = 'warning';
                    $resultat['fleche'] = 'left';
                }
            }
            else{
                    $resultat['mont'] = 0;    
                    $resultat['pourc'] = 0;
                    $resultat['color'] = 'warning';
                    $resultat['fleche'] = 'left';
            }
            if (($nombreN1 == 0 && $nombreN > 0) || ($nombreN1 < 0 && $nombreN == 0)) {
                    $resultat['mont'] = $nombreN;
                    $resultat['pourc'] = 100;
                    $resultat['color'] = 'success';
                    $resultat['fleche'] = 'up';
            }
            if (($nombreN1 == 0 && $nombreN < 0) || ($nombreN1 > 0 && $nombreN == 0)) {
                    $resultat['mont'] = -1 * $nombreN1;    
                    $resultat['pourc'] = -100;
                    $resultat['color'] = 'danger';
                    $resultat['fleche'] = 'down';
            }
                return $resultat; 
        } 

    // Export Excel

    private function getData(): array
    {
        /**
         * @var $ticket Ticket[]
         */
        $list = [];
        $tickets = $this->entityManager->getRepository(Tickets::class)->findAll();

        foreach ($tickets as $ticket) {
            $list[] = [
                $ticket->getCreatedAt(),
                'Ticket' . $ticket->getId() . ' : ' . $ticket->getTitle(),
                $ticket->getService()->getTitle(),
                $ticket->getStatu()->getTitle(),
                $ticket->getPrestataire()->getNom(),
                
                
            ];
        }
        return $list;
    }

    /**
     * @Route("/export/statesGlobales",  name="app_export_states_globales")
     */
    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('States_Globales');
        // Entête de colonne
        $sheet->getCell('A1')->setValue('Commercial');
        $sheet->getCell('B1')->setValue('SecteurMouvement');
        $sheet->getCell('C1')->setValue('FamClient');
        $sheet->getCell('D1')->setValue('Tiers');
        $sheet->getCell('E1')->setValue('Nom');
        $sheet->getCell('F1')->setValue('TypeArticle');
        $sheet->getCell('G1')->setValue('FamArticle');
        $sheet->getCell('H1')->setValue('Ref');
        $sheet->getCell('I1')->setValue('Designation');
        $sheet->getCell('J1')->setValue('Sref1');
        $sheet->getCell('K1')->setValue('Sref2');
        $sheet->getCell('L1')->setValue('UV');
        $sheet->getCell('M1')->setValue('OP');
        $sheet->getCell('N1')->setValue('QteSignN1');
        $sheet->getCell('O1')->setValue('MontantSignN1');
        $sheet->getCell('P1')->setValue('QteSignN');
        $sheet->getCell('Q1')->setValue('MontantSignN');

        // Increase row cursor after header write
            $sheet->fromArray($this->getData(),null, 'A2', true);
        

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $d = new DateTime('NOW');
        $dateTime = $d->format('Ymd-His') ;
        $fileName = 'States_Globales' . $dateTime . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
  
        return $this->redirectToRoute('app_tickets');
    }
    

    /**
     * @Route("/Lhermitte/DetailArticle/{tiers}/{metier}/{dateDebutN}/{dateFinN}/{dateDebutN1}/{dateFinN1}/{commercialId}/{dossier}", name="app_states_par_article_Lh")
     * @Route("/Roby/DetailArticle/{tiers}/{metier}/{dateDebutN}/{dateFinN}/{dateDebutN1}/{dateFinN1}/{commercialId}/{dossier}", name="app_states_par_article_Rb")
     */
    public function statesByArticle(string $tiers, string $metier, $commercialId, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier, StatesByTiersRepository $repo, Request $request, UserInterface $user): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        //dd($user);
        
        $tiers = $tiers; 
       
        // Rechercher les paramétres du métiers
        $secteur = $this->metierParameter($metier);
        $metiers = $secteur['metiers'];
        $themeColor = $secteur['themeColor'];

        $statesTotauxCommercial = $repo->getStatesBandeauClientCaTotalCommercial($commercialId,$metiers,$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
        // Début Bandeau Détaillé avec Nombre de facture, Bl, CA Dépôt, CA Direct etc ...
        $statesClientParArticle = $repo->getStatesLhermitteByArticles($tiers,$metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
        for ($ligArticle=0; $ligArticle <count($statesClientParArticle) ; $ligArticle++) { 
            $statesClientParArticle[$ligArticle]['DeltaN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'],$statesClientParArticle[$ligArticle]['CATotalN1'])['pourc'];
            $statesClientParArticle[$ligArticle]['MontN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'],$statesClientParArticle[$ligArticle]['CATotalN1'])['mont'];
            $statesClientParArticle[$ligArticle]['ColorN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'],$statesClientParArticle[$ligArticle]['CATotalN1'])['color'];
            $statesClientParArticle[$ligArticle]['FlecheN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'],$statesClientParArticle[$ligArticle]['CATotalN1'])['fleche'];          
            
            $statesClientParArticle[$ligArticle]['DeltaN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'],$statesClientParArticle[$ligArticle]['CATotalN'])['pourc'];
            $statesClientParArticle[$ligArticle]['MontN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'],$statesClientParArticle[$ligArticle]['CATotalN'])['mont'];
            $statesClientParArticle[$ligArticle]['ColorN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'],$statesClientParArticle[$ligArticle]['CATotalN'])['color'];
            $statesClientParArticle[$ligArticle]['FlecheN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'],$statesClientParArticle[$ligArticle]['CATotalN'])['fleche'];          
        }

       $FamilleArticles = $repo->getStatesBandeauClientFamilleProduit($tiers,$metiers,$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
       // Début States Globales du client
       $CATotalClient = array();
       $CATotalClient['CATotalN2'] = 0;
       $CATotalClient['CATotalN1'] = 0;
       $CATotalClient['CATotalN'] = 0;
       for ($ligFam=0; $ligFam <count($FamilleArticles) ; $ligFam++) { 
           $CATotalClient['CATotalN2'] += $FamilleArticles[$ligFam]['CATotalN2'];
           $CATotalClient['CATotalN1'] += $FamilleArticles[$ligFam]['CATotalN1'];
           $CATotalClient['CATotalN'] += $FamilleArticles[$ligFam]['CATotalN'];
       }
       
       for ($ligFamArticle=0; $ligFamArticle <count($CATotalClient) ; $ligFamArticle++) { 
        $CATotalClient['DeltaN2'] = $this->calcul_pourcentage($CATotalClient['CATotalN2'],$CATotalClient['CATotalN1'])['pourc'];
        $CATotalClient['DeltaN1'] = $this->calcul_pourcentage($CATotalClient['CATotalN1'],$CATotalClient['CATotalN'])['pourc'];
        $CATotalClient['ColorN2'] = $this->calcul_pourcentage($CATotalClient['CATotalN2'],$CATotalClient['CATotalN1'])['color'];
        $CATotalClient['ColorN1'] = $this->calcul_pourcentage($CATotalClient['CATotalN1'],$CATotalClient['CATotalN'])['color'];
        $CATotalClient['FlecheN2'] = $this->calcul_pourcentage($CATotalClient['CATotalN2'],$CATotalClient['CATotalN1'])['fleche'];
        $CATotalClient['FlecheN1'] = $this->calcul_pourcentage($CATotalClient['CATotalN1'],$CATotalClient['CATotalN'])['fleche'];
        $CATotalClient['deltaTotalClientComN2'] = $this->calcul_pourcentage_total($CATotalClient['CATotalN2'], $statesTotauxCommercial[0]['CATotalN2']);
        $CATotalClient['deltaTotalClientComN1'] = $this->calcul_pourcentage_total($CATotalClient['CATotalN1'], $statesTotauxCommercial[0]['CATotalN1']);
        $CATotalClient['deltaTotalClientComN'] = $this->calcul_pourcentage_total($CATotalClient['CATotalN'], $statesTotauxCommercial[0]['CATotalN']);

       }
       // Fin States Globales du client

       for ($ligFamArt=0; $ligFamArt <count($FamilleArticles) ; $ligFamArt++) { 
        $FamilleArticles[$ligFamArt]['deltaTotalFamilleArtN'] = $this->calcul_pourcentage_total($FamilleArticles[$ligFamArt]['CATotalN'], $CATotalClient['CATotalN']);
        $FamilleArticles[$ligFamArt]['deltaTotalFamilleArtN1'] = $this->calcul_pourcentage_total($FamilleArticles[$ligFamArt]['CATotalN1'], $CATotalClient['CATotalN1']);
        $FamilleArticles[$ligFamArt]['deltaTotalFamilleArtN2'] = $this->calcul_pourcentage_total($FamilleArticles[$ligFamArt]['CATotalN2'], $CATotalClient['CATotalN2']);        
       }
       //dd($FamilleArticles);
        return $this->render('states_lhermitte/statesByArticle.html.twig', [
            'title' => 'States par Articles',
            'statesClientParArticle' => $statesClientParArticle,
            'FamilleArticles' => $FamilleArticles,
            'CATotalClient' => $CATotalClient,
            'themeColor' => $themeColor,
            'statesTotauxCommercial' => $statesTotauxCommercial

        ]);
    }
    
}
