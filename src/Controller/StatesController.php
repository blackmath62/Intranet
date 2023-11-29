<?php

namespace App\Controller;

use App\Form\ActivitesMetierType;
use App\Form\DateSecteurLhDebutFinType;
use App\Form\DateSecteurRbDebutFinType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Divalto\StatesByTiersRepository;
use App\Service\ProgressManager;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class StatesController extends AbstractController
{
    private $progressManager;

    public function __construct(ProgressManager $progressManager)
    {
        $this->progressManager = $progressManager;
    }

    #[Route("/Lhermitte/states", name: "app_states_lhermitte")]
    #[Route("/Roby/states", name: "app_states_roby")]

    public function states(StatesByTiersRepository $repo, Request $request): Response
    {
        // initialisation de mes variables
        if ($request->attributes->get('_route') == 'app_states_roby') {
            $form = $this->createForm(DateSecteurRbDebutFinType::class);
            $metier = 'RB';
        } elseif ($request->attributes->get('_route') == 'app_states_lhermitte') {
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
        $statesMetiers = array();
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
        //  $this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {

            // Rechercher les paramétres du métiers
            if ($request->attributes->get('_route') == 'app_states_roby') {
                $metier = 'RB';
            } elseif ($request->attributes->get('_route') == 'app_states_lhermitte') {
                $metier = $form->getData()['Metiers'];
            }
            $secteur = $this->metierParameter($metier);
            $metiers = $secteur['metiers'];
            $themeColor = $secteur['themeColor'];
            $titre = $secteur['titre'];
            $dossier = $secteur['dossier'];
            $sufixeMetier = $secteur['sufixeRoute'];

            $dateDebutEtFin = $form->getData()['Periode'];
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
            if ($dateFin >= $dateControle) {
                $this->addFlash('danger', 'L\'extraction est limité à une année compléte');
                return $this->redirectToRoute($tracking, ['secteur' => $secteur]);
            }
            // Bloquer l'extraction pour que l'année début et de fin soit la même
            $anneeD = date("Y", strtotime($dateDebutN));
            $anneeF = date("Y", strtotime($dateFinN));
            if ($anneeD != $anneeF) {
                $this->addFlash('danger', 'Les Années de début et de fin doivent être identiques, l\'extration sort 3 années automatiquement');
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

            for ($ligStateDuMetier = 0; $ligStateDuMetier < count($statesTotauxParSecteur); $ligStateDuMetier++) {
                if ($statesTotauxParSecteur[$ligStateDuMetier]['Periode'] == 'PeriodeN1') {
                    $statesBandeau['CATotalBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['CATotal'];
                    $statesBandeau['CADepotBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADepot'];
                    $statesBandeau['CADirectBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADirect'];
                    $statesBandeau['ClientBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbTiers'];
                    $statesBandeau['BlBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbBl'];
                    $statesBandeau['FactureBandeauN1'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbFacture'];
                } elseif ($statesTotauxParSecteur[$ligStateDuMetier]['Periode'] == 'PeriodeN') {
                    $statesBandeau['CATotalBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['CATotal'];
                    $statesBandeau['CADepotBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADepot'];
                    $statesBandeau['CADirectBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['CADirect'];
                    $statesBandeau['ClientBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbTiers'];
                    $statesBandeau['BlBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbBl'];
                    $statesBandeau['FactureBandeauN'] += $statesTotauxParSecteur[$ligStateDuMetier]['NbFacture'];
                }
            }
            // Pourcentage couleur et fléche du bandeau, il est possible de faire des beaux objet là, à réfléchir et améliorer !
            // Sur le Total
            $statesBandeau['DeltaTotalBandeau'] = $this->calcul_pourcentage($statesBandeau['CATotalBandeauN1'], $statesBandeau['CATotalBandeauN'])['pourc'];
            $statesBandeau['ColorTotalBandeau'] = $this->calcul_pourcentage($statesBandeau['CATotalBandeauN1'], $statesBandeau['CATotalBandeauN'])['color'];
            $statesBandeau['FlecheTotalBandeau'] = $this->calcul_pourcentage($statesBandeau['CATotalBandeauN1'], $statesBandeau['CATotalBandeauN'])['fleche'];

            // Sur le Direct
            $statesBandeau['DeltaDirectBandeau'] = $this->calcul_pourcentage($statesBandeau['CADirectBandeauN1'], $statesBandeau['CADirectBandeauN'])['pourc'];
            $statesBandeau['ColorDirectBandeau'] = $this->calcul_pourcentage($statesBandeau['CADirectBandeauN1'], $statesBandeau['CADirectBandeauN'])['color'];
            $statesBandeau['FlecheDirectBandeau'] = $this->calcul_pourcentage($statesBandeau['CADirectBandeauN1'], $statesBandeau['CADirectBandeauN'])['fleche'];

            // Sur le Depot
            $statesBandeau['DeltaDepotBandeau'] = $this->calcul_pourcentage($statesBandeau['CADepotBandeauN1'], $statesBandeau['CADepotBandeauN'])['pourc'];
            $statesBandeau['ColorDepotBandeau'] = $this->calcul_pourcentage($statesBandeau['CADepotBandeauN1'], $statesBandeau['CADepotBandeauN'])['color'];
            $statesBandeau['FlecheDepotBandeau'] = $this->calcul_pourcentage($statesBandeau['CADepotBandeauN1'], $statesBandeau['CADepotBandeauN'])['fleche'];

            // Sur le Client
            $statesBandeau['DeltaClientBandeau'] = $this->calcul_pourcentage($statesBandeau['ClientBandeauN1'], $statesBandeau['ClientBandeauN'])['pourc'];
            $statesBandeau['ColorClientBandeau'] = $this->calcul_pourcentage($statesBandeau['ClientBandeauN1'], $statesBandeau['ClientBandeauN'])['color'];
            $statesBandeau['FlecheClientBandeau'] = $this->calcul_pourcentage($statesBandeau['ClientBandeauN1'], $statesBandeau['ClientBandeauN'])['fleche'];

            // Sur le Bl
            $statesBandeau['DeltaBlBandeau'] = $this->calcul_pourcentage($statesBandeau['BlBandeauN1'], $statesBandeau['BlBandeauN'])['pourc'];
            $statesBandeau['ColorBlBandeau'] = $this->calcul_pourcentage($statesBandeau['BlBandeauN1'], $statesBandeau['BlBandeauN'])['color'];
            $statesBandeau['FlecheBlBandeau'] = $this->calcul_pourcentage($statesBandeau['BlBandeauN1'], $statesBandeau['BlBandeauN'])['fleche'];

            // Sur le Facture
            $statesBandeau['DeltaFactureBandeau'] = $this->calcul_pourcentage($statesBandeau['FactureBandeauN1'], $statesBandeau['FactureBandeauN'])['pourc'];
            $statesBandeau['ColorFactureBandeau'] = $this->calcul_pourcentage($statesBandeau['FactureBandeauN1'], $statesBandeau['FactureBandeauN'])['color'];
            $statesBandeau['FlecheFactureBandeau'] = $this->calcul_pourcentage($statesBandeau['FactureBandeauN1'], $statesBandeau['FactureBandeauN'])['fleche'];

            // Fin Bandeau Détaillé avec Nombre de facture, Bl, CA Dépôt, CA Direct etc ...

            // Début States par commerciaux
            $statesCommerciaux = $repo->getStatesTotauxParCommerciaux($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);

            for ($ligCom = 0; $ligCom < count($statesCommerciaux); $ligCom++) {
                $commercial[$ligCom]['Commercial'] = $statesCommerciaux[$ligCom]['Commercial'];
                $commercial[$ligCom]['CommercialId'] = $statesCommerciaux[$ligCom]['CommercialId'];
            }
            if (isset($commercial)) {
                $listeCommerciaux = array_values(array_unique($commercial, SORT_REGULAR)); // faire une liste de commerciaux sans doublon
                // pour chaque commercial dans le tableau

                for ($ligCommercial = 0; $ligCommercial < count($listeCommerciaux); $ligCommercial++) {
                    $Commercial = $listeCommerciaux[$ligCommercial]['Commercial'];
                    $stateCommerciaux[$ligCommercial]['Commercial'] = $listeCommerciaux[$ligCommercial]['Commercial'];
                    $stateCommerciaux[$ligCommercial]['CommercialId'] = $listeCommerciaux[$ligCommercial]['CommercialId'];
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
                    for ($ligStateEntete = 0; $ligStateEntete < count($statesCommerciaux); $ligStateEntete++) {
                        if ($statesCommerciaux[$ligStateEntete]['Periode'] == 'PeriodeN' && $statesCommerciaux[$ligStateEntete]['Commercial'] == $Commercial) {
                            $stateCommerciaux[$ligCommercial]['CATotalN'] += $statesCommerciaux[$ligStateEntete]['CATotal'];
                            $stateCommerciaux[$ligCommercial]['CADepotN'] += $statesCommerciaux[$ligStateEntete]['CADepot'];
                            $stateCommerciaux[$ligCommercial]['CADirectN'] += $statesCommerciaux[$ligStateEntete]['CADirect'];
                            $stateCommerciaux[$ligCommercial]['ClientN'] += $statesCommerciaux[$ligStateEntete]['NbTiers'];
                            $stateCommerciaux[$ligCommercial]['FactureN'] += $statesCommerciaux[$ligStateEntete]['NbFacture'];
                            $stateCommerciaux[$ligCommercial]['BlN'] += $statesCommerciaux[$ligStateEntete]['NbBl'];
                        } elseif ($statesCommerciaux[$ligStateEntete]['Periode'] == 'PeriodeN1' && $statesCommerciaux[$ligStateEntete]['Commercial'] == $Commercial) {
                            $stateCommerciaux[$ligCommercial]['CATotalN1'] += $statesCommerciaux[$ligStateEntete]['CATotal'];
                            $stateCommerciaux[$ligCommercial]['CADepotN1'] += $statesCommerciaux[$ligStateEntete]['CADepot'];
                            $stateCommerciaux[$ligCommercial]['CADirectN1'] += $statesCommerciaux[$ligStateEntete]['CADirect'];
                            $stateCommerciaux[$ligCommercial]['ClientN1'] += $statesCommerciaux[$ligStateEntete]['NbTiers'];
                            $stateCommerciaux[$ligCommercial]['FactureN1'] += $statesCommerciaux[$ligStateEntete]['NbFacture'];
                            $stateCommerciaux[$ligCommercial]['BlN1'] += $statesCommerciaux[$ligStateEntete]['NbBl'];
                        }
                    }

                    $stateCommerciaux[$ligCommercial]['DeltaTotal'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['CATotalN1'], $stateCommerciaux[$ligCommercial]['CATotalN'])['pourc'];
                    $stateCommerciaux[$ligCommercial]['ColorTotal'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['CATotalN1'], $stateCommerciaux[$ligCommercial]['CATotalN'])['color'];
                    $stateCommerciaux[$ligCommercial]['FlecheTotal'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['CATotalN1'], $stateCommerciaux[$ligCommercial]['CATotalN'])['fleche'];

                    $stateCommerciaux[$ligCommercial]['DeltaClient'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['ClientN1'], $stateCommerciaux[$ligCommercial]['ClientN'])['pourc'];
                    $stateCommerciaux[$ligCommercial]['ColorClient'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['ClientN1'], $stateCommerciaux[$ligCommercial]['ClientN'])['color'];
                    $stateCommerciaux[$ligCommercial]['FlecheClient'] = $this->calcul_pourcentage($stateCommerciaux[$ligCommercial]['ClientN1'], $stateCommerciaux[$ligCommercial]['ClientN'])['fleche'];

                    // je dois récupérer via une requête SQL les states Globales pour les comparer avec les states par commerciaux, la requête semble préte, à contrôler

                    $stateCommerciaux[$ligCommercial]['deltaParTotalN1'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['CATotalN1'], $statesBandeau['CATotalBandeauN1']);
                    $stateCommerciaux[$ligCommercial]['deltaParClientN1'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['ClientN1'], $statesBandeau['ClientBandeauN1']);
                    $stateCommerciaux[$ligCommercial]['deltaParTotalN'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['CATotalN'], $statesBandeau['CATotalBandeauN']);
                    $stateCommerciaux[$ligCommercial]['deltaParClientN'] = $this->calcul_pourcentage_total($stateCommerciaux[$ligCommercial]['ClientN'], $statesBandeau['ClientBandeauN']);

                }
                // Fin States par commerciaux

                // Début States par client
                $statesParClient = $repo->getStatesDetailClient($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);

                for ($ligClient = 0; $ligClient < count($statesParClient); $ligClient++) {
                    $statesParClient[$ligClient]['DeltaDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'], $statesParClient[$ligClient]['CATotalN'])['pourc'];
                    $statesParClient[$ligClient]['MontDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'], $statesParClient[$ligClient]['CATotalN'])['mont'];
                    $statesParClient[$ligClient]['ColorDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'], $statesParClient[$ligClient]['CATotalN'])['color'];
                    $statesParClient[$ligClient]['FlecheDetailClient'] = $this->calcul_pourcentage($statesParClient[$ligClient]['CATotalN1'], $statesParClient[$ligClient]['CATotalN'])['fleche'];
                }

                // Fin States par client

                // Début States par Métier
                $statesMetiers = $repo->getStatesMetier($dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);

                for ($ligMetier = 0; $ligMetier < count($statesMetiers); $ligMetier++) {
                    $statesMetiers[$ligMetier]['DeltaMetier'] = $this->calcul_pourcentage($statesMetiers[$ligMetier]['CATotalN1'], $statesMetiers[$ligMetier]['CATotalN'])['pourc'];
                    $statesMetiers[$ligMetier]['ColorMetier'] = $this->calcul_pourcentage($statesMetiers[$ligMetier]['CATotalN1'], $statesMetiers[$ligMetier]['CATotalN'])['color'];
                    $statesMetiers[$ligMetier]['FlecheMetier'] = $this->calcul_pourcentage($statesMetiers[$ligMetier]['CATotalN1'], $statesMetiers[$ligMetier]['CATotalN'])['fleche'];
                }
                // Fin States par Métier

                // Début Top 10 Familles produits
                // TODO à voir dés que possible
                if ($dateDebutN) {
                    $Top10 = $repo->getTop10FamillesProduits($dateDebutN, $dateFinN, $dossier);
                    $Top10Famille = [];
                    $Top10N = [];
                    $Top10N1 = [];

                    for ($topLig = 0; $topLig < count($Top10); $topLig++) {
                        $Top10Famille[] = $Top10[$topLig]['Fam_Article'];
                        $Top10N[] = $Top10[$topLig]['MontantSignN'];
                        $Top10N1[] = $Top10[$topLig]['MontantSignN1'];
                    }
                    $Top10Famille = json_encode($Top10Famille);
                    $Top10N = json_encode($Top10N);
                    $Top10N1 = json_encode($Top10N1);
                } else {
                    $Top10Famille = "";
                    $Top10N = "";
                    $Top10N1 = "";
                }
                // Fin Top 10 Familles produits

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
            'dateDebutFinForm' => $form->createView(),
        ]);
    }

    public function cal_days_in_year($year)
    {
        $days = 0;
        for ($month = 1; $month <= 12; $month++) {
            $days = $days + cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        return $days;
    }

    public function metierParameter(string $metier)
    {

        if ($metier == 'EV') {
            $secteur['metiers'] = '\'EV\'';
            $secteur['themeColor'] = 'success';
            $secteur['titre'] = ' Espaces Verts';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        } elseif ($metier == 'HP') {
            $secteur['metiers'] = '\'MA\', \'HP\'';
            $secteur['themeColor'] = 'danger';
            $secteur['titre'] = ' Horti - Pépi';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        } elseif ($metier == 'MA') {
            $secteur['metiers'] = '\'MA\'';
            $secteur['themeColor'] = 'orange';
            $secteur['titre'] = ' Maraîchage';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        } elseif ($metier == 'ME') {
            $secteur['metiers'] = '\'ME\'';
            $secteur['themeColor'] = 'warning';
            $secteur['titre'] = ' Matériel / Équipement';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        } elseif ($metier == 'Tous') {
            $secteur['metiers'] = '\'EV\', \'HP\', \'ME\', \'MA\'';
            $secteur['themeColor'] = 'info';
            $secteur['titre'] = ' Tous les métiers Lhermitte';
            $secteur['sufixeRoute'] = 'Lh';
            $secteur['dossier'] = 1;
        } elseif ($metier == 'RB') {
            $secteur['metiers'] = '\'RB\'';
            $secteur['themeColor'] = 'primary';
            $secteur['titre'] = ' Roby';
            $secteur['sufixeRoute'] = 'Rb';
            $secteur['dossier'] = 3;
        }
        return $secteur;
    }

    public function calcul_pu($montant, $qte)
    {
        if (isset($montant) && $montant != 0 && isset($qte) && $qte != 0) {
            return $montant / $qte;
        }
        return null;
    }
    public function calcul_delta($montantN, $montantN1)
    {
        if (isset($montantN) && isset($montantN1)) {
            return $this->calcul_pourcentage($montantN1, $montantN)['pourc'] / 100;
        }
        return null;
    }

    // Export Excel par metier

    public function getDataMetier($metier, $dateDebutN, $dateFinN, $dossier, $repo): array
    {
        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', 0);
        $file = 'progress.txt';
        $list = [];
        $donnees = [];

        $donnees = $repo->getStatesExcelParMetier($metier, $dateDebutN, $dateFinN, $dossier);
        // désactiver le Garbage Collector
        gc_disable();
        file_put_contents($file, $this->progressManager->initializeProgress(count($donnees)));

        // Calculez combien de données vous pouvez traiter en un lot
        $batchSize = 100; // Ajustez cette valeur en fonction de vos besoins

        for ($i = 0; $i < count($donnees); $i += $batchSize) {
            $batch = array_slice($donnees, $i, $batchSize);

            // Initialisez le lot de données pour cette itération
            $batchList = [];

            foreach ($batch as $donnee) {
                $batchList[] = [
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
                    $donnee['QteSignN'],
                    $this->calcul_pu($donnee['MontantSignN'], $donnee['QteSignN']),
                    $donnee['MontantSignN'],
                    $this->calcul_delta($donnee['MontantSignN'], $donnee['MontantSignN1']),
                    $donnee['QteSignN1'],
                    $this->calcul_pu($donnee['MontantSignN1'], $donnee['QteSignN1']),
                    $donnee['MontantSignN1'],
                    $this->calcul_delta($donnee['MontantSignN1'], $donnee['MontantSignN2']),
                    $donnee['QteSignN2'],
                    $this->calcul_pu($donnee['MontantSignN2'], $donnee['QteSignN2']),
                    $donnee['MontantSignN2'],
                ];

                // Lancer le Garbage Collector après chaque itération
                gc_collect_cycles();
            }

            // Ajoutez le lot de données à la liste principale
            $list = array_merge($list, $batchList);

            // Mettez à jour la progression
            $this->progressManager->incrementIteration();
            file_put_contents($file, $this->progressManager->updateProgress());
        }

        // Finalisez la barre de progression
        $this->progressManager->finalizeProgress();

        return $list;
    }

    #[Route("/Lhermitte/excel/{metier}/{dateDebutN}/{dateFinN}/{dossier}", name: "app_states_excel_metier_Lh")]
    #[Route("/Roby/excel/{metier}/{dateDebutN}/{dateFinN}/{dossier}", name: "app_states_excel_metier_Rb")]

    public function get_states_excel_metier($metier, $dateDebutN, $dateFinN, $dossier, StatesByTiersRepository $repo)
    {
        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', 0);

        $secteur = $this->metierParameter($metier);
        $file = 'progress.txt';
        $fileText = 'progress-text.txt';
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
        $sheet->getCell('M5')->setValue('QteSignN');
        $sheet->getCell('N5')->setValue('Pu_N');
        $sheet->getCell('O5')->setValue('MontantSignN');
        $sheet->getCell('P5')->setValue('Delta_N-1_N');
        $sheet->getCell('Q5')->setValue('QteSignN-1');
        $sheet->getCell('R5')->setValue('Pu_N-1');
        $sheet->getCell('S5')->setValue('MontantSignN-1');
        $sheet->getCell('T5')->setValue('Delta_N-2_N-1');
        $sheet->getCell('U5')->setValue('QteSignN-2');
        $sheet->getCell('V5')->setValue('Pu_N-2');
        $sheet->getCell('W5')->setValue('MontantSignN-2');
        file_put_contents($file, 10);
        file_put_contents($fileText, 'Extraction des données de Divalto');
        // Increase row cursor after header write
        $dataMetier = $this->getDataMetier($metier, $dateDebutN, $dateFinN, $dossier, $repo);
        $sheet->fromArray($dataMetier, null, 'A6', true);
        $dernLign = count($dataMetier) + 5;

        file_put_contents($file, 30);
        file_put_contents($fileText, 'Cosmétique des colonnes années du fichier excel');
        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = 'States Métier =>' . $metier . ' du ' . $dateDebutN . ' au ' . $dateFinN . ' le ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);
        // Le style du tableau
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
        $spreadsheet->getActiveSheet()->getStyle("A5:W{$dernLign}")->applyFromArray($styleArray);
        // Le style N en vert
        $spreadsheet->getActiveSheet()->getStyle("M1:O{$dernLign}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('F92D050');
        // Le style N-1 en orange
        $spreadsheet->getActiveSheet()->getStyle("Q1:S{$dernLign}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFC000');
        // Le style N-2 en jaune
        $spreadsheet->getActiveSheet()->getStyle("U1:W{$dernLign}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFF00');
        file_put_contents($file, 40);
        file_put_contents($fileText, 'Alignement des données dans les cellules');
        // Le style de l'entête
        $styleEntete = [
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

        file_put_contents($file, 50);
        $spreadsheet->getActiveSheet()->getStyle("A5:W5")->applyFromArray($styleEntete);

        $sheet->getStyle("H1:W{$dernLign}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Espacement automatique sur toutes les colonnes sauf la A
        $sheet->setAutoFilter("A5:W{$dernLign}");
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
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        file_put_contents($file, 60);
        file_put_contents($fileText, 'Mise en place des formules dans l\'entête');
        // Sous Total des colonnes pour les colonnes Quantités et montants de chaque années
        $sheet->setCellValue("M4", "=SUBTOTAL(9,M6:M{$dernLign})"); // Quantité N
        $sheet->setCellValue("O4", "=SUBTOTAL(9,O6:O{$dernLign})"); // Montant N
        $sheet->setCellValue("Q4", "=SUBTOTAL(9,Q6:Q{$dernLign})"); // Quantité N-1
        $sheet->setCellValue("S4", "=SUBTOTAL(9,S6:S{$dernLign})"); // Montant N-1
        $sheet->setCellValue("U4", "=SUBTOTAL(9,U6:U{$dernLign})"); // Quantité N-2
        $sheet->setCellValue("W4", "=SUBTOTAL(9,W6:W{$dernLign})"); // Montant N-2
        $sheet->setCellValue("P4", "=(O4/S4)-1"); // Delta n-1 / n
        $sheet->setCellValue("T4", "=(S4/W4)-1"); // Delta n-2 / n-1
        file_put_contents($file, 70);
        file_put_contents($fileText, 'Mise en place des formats de données de pourcentages et monétaires');
        // Format nombre € colonne PU et montant N
        $sheet->getStyle("N1:O{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_INTEGER);
        // Format nombre € colonne PU et montant N-1
        $sheet->getStyle("R1:S{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_INTEGER);
        // Format nombre € colonne PU et montant N-2
        $sheet->getStyle("V1:W{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_INTEGER);
        // Format Pourcentage CELLULE montant N-1/ montant N
        $sheet->getStyle('P4')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        // Format Pourcentage colonne montant N-1/ montant N
        $sheet->getStyle("P6:P{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode('[Green][>=0]#.##0%;[Red][<0]#.##0%;#.##0%');
        // Format Pourcentage CELLULE montant N-2/ montant N-1
        $sheet->getStyle('T4')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        // Format Pourcentage colonne montant N-2/ montant N-1
        $sheet->getStyle("T6:T{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode('[Green][>=0]#.##0%;[Red][<0]#.##0%;#.##0%');

        file_put_contents($file, 80);
        file_put_contents($fileText, 'Création d\'un classeur excel');
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        file_put_contents($file, 90);
        file_put_contents($fileText, 'Enregistrement du fichier Excel');
        $writer->save($temp_file);
        file_put_contents($fileText, 'Traitement terminé ! votre fichier est dans vos Téléchargements');
        file_put_contents($file, 100);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
        die();

    }

    // Export Excel par commercial

    public function getDataCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, $repo): array
    {
        /**
         * @var $ticket Ticket[]
         */
        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', 0);
        $file = 'progress.txt';

        $list = [];
        $donnee = [];
        $donnees = $repo->getStatesExcelParCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier);
        foreach ($donnees as $donnee) {
            $donnee['Delta_N-1_N'] = $this->calcul_pourcentage($donnee['MontantSignN1'], $donnee['MontantSignN'])['pourc'] / 100;
            $donnee['Delta_N-2_N-1'] = $this->calcul_pourcentage($donnee['MontantSignN2'], $donnee['MontantSignN1'])['pourc'] / 100;
            $donnee['Pu_N'] = "";
            $donnee['Pu_N-1'] = "";
            $donnee['Pu_N-2'] = "";
            if ($donnee['MontantSignN'] != 0 && $donnee['QteSignN'] != 0) {$donnee['Pu_N'] = $donnee['MontantSignN'] / $donnee['QteSignN'];}
            if ($donnee['MontantSignN1'] != 0 && $donnee['QteSignN1'] != 0) {$donnee['Pu_N-1'] = $donnee['MontantSignN1'] / $donnee['QteSignN1'];}
            if ($donnee['MontantSignN2'] != 0 && $donnee['QteSignN2'] != 0) {$donnee['Pu_N-2'] = $donnee['MontantSignN2'] / $donnee['QteSignN2'];}
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
                $donnee['QteSignN'],
                $donnee['Pu_N'],
                $donnee['MontantSignN'],
                $donnee['Delta_N-1_N'],
                $donnee['QteSignN1'],
                $donnee['Pu_N-1'],
                $donnee['MontantSignN1'],
                $donnee['Delta_N-2_N-1'],
                $donnee['QteSignN2'],
                $donnee['Pu_N-2'],
                $donnee['MontantSignN2'],

            ];
        }
        file_put_contents($file, 20);
        return $list;
    }

    #[Route("/Lhermitte/excel/{metier}/{dateDebutN}/{dateFinN}/{commercialId}/{dossier}", name: "app_states_excel_commercial_Lh")]
    #[Route("/Roby/excel/{metier}/{dateDebutN}/{dateFinN}/{commercialId}/{dossier}", name: "app_states_excel_commercial_Rb")]

    public function get_states_excel_commercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, StatesByTiersRepository $repo)
    {

        // tracking user page for stats
        // $tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);

        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', 0);

        $file = 'progress.txt';
        $fileText = 'progress-text.txt';

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
        $sheet->getCell('M5')->setValue('QteSignN');
        $sheet->getCell('N5')->setValue('Pu_N');
        $sheet->getCell('O5')->setValue('MontantSignN');
        $sheet->getCell('P5')->setValue('Delta_N-1_N');
        $sheet->getCell('Q5')->setValue('QteSignN-1');
        $sheet->getCell('R5')->setValue('Pu_N-1');
        $sheet->getCell('S5')->setValue('MontantSignN-1');
        $sheet->getCell('T5')->setValue('Delta_N-2_N-1');
        $sheet->getCell('U5')->setValue('QteSignN-2');
        $sheet->getCell('V5')->setValue('Pu_N-2');
        $sheet->getCell('W5')->setValue('MontantSignN-2');
        file_put_contents($file, 10);
        file_put_contents($fileText, 'Extraction des données de Divalto');
        // Increase row cursor after header write
        $sheet->fromArray($this->getDataCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, $repo), null, 'A6', true);
        $dernLign = count($this->getDataCommercial($metier, $dateDebutN, $dateFinN, $commercialId, $dossier, $repo)) + 5;
        file_put_contents($file, 30);
        file_put_contents($fileText, 'Cosmétique des colonnes années du fichier excel');
        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = 'States Métier =>' . $metier . ' du ' . $dateDebutN . ' au ' . $dateFinN . ' le ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);
        // Le style du tableau
        file_put_contents($file, 40);
        file_put_contents($fileText, 'Alignement des données dans les cellules');
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
        file_put_contents($file, 50);
        $spreadsheet->getActiveSheet()->getStyle("A5:W{$dernLign}")->applyFromArray($styleArray);
        // Le style N en vert
        $spreadsheet->getActiveSheet()->getStyle("M1:O{$dernLign}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('F92D050');
        // Le style N-1 en orange
        $spreadsheet->getActiveSheet()->getStyle("Q1:S{$dernLign}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFC000');
        // Le style N-2 en jaune
        $spreadsheet->getActiveSheet()->getStyle("U1:W{$dernLign}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFF00');

        // Le style de l'entête
        $styleEntete = [
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
        $spreadsheet->getActiveSheet()->getStyle("A5:W5")->applyFromArray($styleEntete);

        $sheet->getStyle("H1:W{$dernLign}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Espacement automatique sur toutes les colonnes sauf la A
        $sheet->setAutoFilter("A5:W{$dernLign}");
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
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        // Sous Total des colonnes pour les colonnes Quantités et montants de chaque années
        file_put_contents($file, 60);
        file_put_contents($fileText, 'Mise en place des formules dans l\'entête');
        $sheet->setCellValue("M4", "=SUBTOTAL(9,M6:M{$dernLign})"); // Quantité N
        $sheet->setCellValue("O4", "=SUBTOTAL(9,O6:O{$dernLign})"); // Montant N
        $sheet->setCellValue("Q4", "=SUBTOTAL(9,Q6:Q{$dernLign})"); // Quantité N-1
        $sheet->setCellValue("S4", "=SUBTOTAL(9,S6:S{$dernLign})"); // Montant N-1
        $sheet->setCellValue("U4", "=SUBTOTAL(9,U6:U{$dernLign})"); // Quantité N-2
        $sheet->setCellValue("W4", "=SUBTOTAL(9,W6:W{$dernLign})"); // Montant N-2
        $sheet->setCellValue("P4", "=(O4/S4)-1"); // Delta n-1 / n
        $sheet->setCellValue("T4", "=(S4/W4)-1"); // Delta n-2 / n-1
        // Format nombre € colonne PU et montant N
        file_put_contents($file, 70);
        file_put_contents($fileText, 'Mise en place des formats de données de pourcentages et monétaires');
        $sheet->getStyle("N1:O{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_INTEGER);
        // Format nombre € colonne PU et montant N-1
        $sheet->getStyle("R1:S{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_INTEGER);
        // Format nombre € colonne PU et montant N-2
        $sheet->getStyle("V1:W{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_INTEGER);
        // Format Pourcentage CELLULE montant N-1/ montant N
        $sheet->getStyle('P4')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        // Format Pourcentage colonne montant N-1/ montant N
        $sheet->getStyle("P6:P{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode('[Green][>=0]#.##0%;[Red][<0]#.##0%;#.##0%');
        // Format Pourcentage CELLULE montant N-2/ montant N-1
        $sheet->getStyle('T4')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        // Format Pourcentage colonne montant N-2/ montant N-1
        $sheet->getStyle("T6:T{$dernLign}")
            ->getNumberFormat()
            ->setFormatCode('[Green][>=0]#.##0%;[Red][<0]#.##0%;#.##0%');

        file_put_contents($file, 80);
        file_put_contents($fileText, 'Création d\'un classeur excel');
        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        file_put_contents($file, 90);
        file_put_contents($fileText, 'Enregistrement du fichier Excel');
        $writer->save($temp_file);
        file_put_contents($fileText, 'Traitement terminé ! votre fichier est dans vos Téléchargements');
        file_put_contents($file, 100);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }

    public function dateParameter($Param)
    {

        // faire un objet avec la gestion des intervals de date
        $dateDebut = substr($Param, 0, -13);
        $anneeDebut = substr($dateDebut, 6, 4);
        $moisDebut = substr($dateDebut, 0, 2);
        $jourDebut = substr($dateDebut, 3, 2);

        $dateFin = substr($Param, 13, 10);
        $anneeFin = substr($dateFin, 6, 4);
        $moisFin = substr($dateFin, 0, 2);
        $jourFin = substr($dateFin, 3, 2);

        // Calculer correctement le comparatif de date
        $intervalAnnee = ($anneeFin - $anneeDebut) + 1;
        $yearStartN1 = $anneeDebut - $intervalAnnee;
        $yearEndN1 = $anneeFin - $intervalAnnee;

        $dateParam['dateDebutN'] = $anneeDebut . '-' . $moisDebut . '-' . $jourDebut;
        $dateParam['dateDebutN1'] = $yearStartN1 . '-' . $moisDebut . '-' . $jourDebut;
        $dateParam['dateFinN'] = $anneeFin . '-' . $moisFin . '-' . $jourFin;
        $dateParam['dateFinN1'] = $yearEndN1 . '-' . $moisFin . '-' . $jourFin;
        $dateParam['intervalN'] = $jourDebut . '-' . $moisDebut . '-' . $anneeDebut . ' - ' . $jourFin . '-' . $moisFin . '-' . $anneeFin;
        $dateParam['intervalN1'] = $jourDebut . '-' . $moisDebut . '-' . $yearStartN1 . ' - ' . $jourFin . '-' . $moisFin . '-' . $yearEndN1;

        return $dateParam;
    }

    // fonction utile pour le cacul de pourcentage et éviter le calcul est impossible
    public function calcul_pourcentage_total($nombreParCom, $nombreTotal)
    {
        if ($nombreParCom > 0 && $nombreTotal > 0) {
            $resultat = ($nombreParCom * 100) / $nombreTotal;
        } else {
            $resultat = 0;
        }
        return $resultat;
    }

    public function calcul_pourcentage($nombreN1, $nombreN)
    {

        if ($nombreN1 != 0 && $nombreN != 0) {
            $resultat['pourc'] = (($nombreN / $nombreN1) - 1) * 100;
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
        } else {
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

    #[Route("/Lhermitte/DetailArticle/{tiers}/{metier}/{dateDebutN}/{dateFinN}/{dateDebutN1}/{dateFinN1}/{commercialId}/{dossier}", name: "app_states_par_article_Lh")]
    #[Route("/Roby/DetailArticle/{tiers}/{metier}/{dateDebutN}/{dateFinN}/{dateDebutN1}/{dateFinN1}/{commercialId}/{dossier}", name: "app_states_par_article_Rb")]

    public function statesByArticle(string $tiers, string $metier, $commercialId, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier, StatesByTiersRepository $repo): Response
    {
        // tracking user page for stats
        // $tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);
        //dd($user);

        $tiers = $tiers;

        // Rechercher les paramétres du métiers
        $secteur = $this->metierParameter($metier);
        $metiers = $secteur['metiers'];
        $themeColor = $secteur['themeColor'];

        $statesTotauxCommercial = $repo->getStatesBandeauClientCaTotalCommercial($commercialId, $metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
        // Début Bandeau Détaillé avec Nombre de facture, Bl, CA Dépôt, CA Direct etc ...
        $statesClientParArticle = $repo->getStatesLhermitteByArticles($tiers, $metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
        for ($ligArticle = 0; $ligArticle < count($statesClientParArticle); $ligArticle++) {
            $statesClientParArticle[$ligArticle]['DeltaN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'], $statesClientParArticle[$ligArticle]['CATotalN1'])['pourc'];
            $statesClientParArticle[$ligArticle]['MontN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'], $statesClientParArticle[$ligArticle]['CATotalN1'])['mont'];
            $statesClientParArticle[$ligArticle]['ColorN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'], $statesClientParArticle[$ligArticle]['CATotalN1'])['color'];
            $statesClientParArticle[$ligArticle]['FlecheN2'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN2'], $statesClientParArticle[$ligArticle]['CATotalN1'])['fleche'];

            $statesClientParArticle[$ligArticle]['DeltaN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'], $statesClientParArticle[$ligArticle]['CATotalN'])['pourc'];
            $statesClientParArticle[$ligArticle]['MontN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'], $statesClientParArticle[$ligArticle]['CATotalN'])['mont'];
            $statesClientParArticle[$ligArticle]['ColorN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'], $statesClientParArticle[$ligArticle]['CATotalN'])['color'];
            $statesClientParArticle[$ligArticle]['FlecheN1'] = $this->calcul_pourcentage($statesClientParArticle[$ligArticle]['CATotalN1'], $statesClientParArticle[$ligArticle]['CATotalN'])['fleche'];
        }

        $FamilleArticles = $repo->getStatesBandeauClientFamilleProduit($tiers, $metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier);
        // Début States Globales du client
        $CATotalClient = array();
        $CATotalClient['CATotalN2'] = 0;
        $CATotalClient['CATotalN1'] = 0;
        $CATotalClient['CATotalN'] = 0;
        for ($ligFam = 0; $ligFam < count($FamilleArticles); $ligFam++) {
            $CATotalClient['CATotalN2'] += $FamilleArticles[$ligFam]['CATotalN2'];
            $CATotalClient['CATotalN1'] += $FamilleArticles[$ligFam]['CATotalN1'];
            $CATotalClient['CATotalN'] += $FamilleArticles[$ligFam]['CATotalN'];
        }

        for ($ligFamArticle = 0; $ligFamArticle < count($CATotalClient); $ligFamArticle++) {
            $CATotalClient['DeltaN2'] = $this->calcul_pourcentage($CATotalClient['CATotalN2'], $CATotalClient['CATotalN1'])['pourc'];
            $CATotalClient['DeltaN1'] = $this->calcul_pourcentage($CATotalClient['CATotalN1'], $CATotalClient['CATotalN'])['pourc'];
            $CATotalClient['ColorN2'] = $this->calcul_pourcentage($CATotalClient['CATotalN2'], $CATotalClient['CATotalN1'])['color'];
            $CATotalClient['ColorN1'] = $this->calcul_pourcentage($CATotalClient['CATotalN1'], $CATotalClient['CATotalN'])['color'];
            $CATotalClient['FlecheN2'] = $this->calcul_pourcentage($CATotalClient['CATotalN2'], $CATotalClient['CATotalN1'])['fleche'];
            $CATotalClient['FlecheN1'] = $this->calcul_pourcentage($CATotalClient['CATotalN1'], $CATotalClient['CATotalN'])['fleche'];
            $CATotalClient['deltaTotalClientComN2'] = $this->calcul_pourcentage_total($CATotalClient['CATotalN2'], $statesTotauxCommercial[0]['CATotalN2']);
            $CATotalClient['deltaTotalClientComN1'] = $this->calcul_pourcentage_total($CATotalClient['CATotalN1'], $statesTotauxCommercial[0]['CATotalN1']);
            $CATotalClient['deltaTotalClientComN'] = $this->calcul_pourcentage_total($CATotalClient['CATotalN'], $statesTotauxCommercial[0]['CATotalN']);

        }
        // Fin States Globales du client

        for ($ligFamArt = 0; $ligFamArt < count($FamilleArticles); $ligFamArt++) {
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
            'statesTotauxCommercial' => $statesTotauxCommercial,

        ]);
    }

    #[Route("/Lhermitte/states/clients/3/ans/lhermitte", name: "app_states_par_client_Lh")]

    public function statesParClientTroisAns(MouvRepository $repo, Request $request): Response
    {
        $clients = '';
        $familles = '';
        $form = $this->createForm(ActivitesMetierType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dd = $form->getData()['start']->format('Y-m-d');
            $df = $form->getData()['end']->format('Y-m-d');
            $metier = $form->getData()['Metiers'];

            $clients = $repo->getVenteClientSur3Ans($dd, $df, 'CLIENT', $metier);

            foreach ($clients as $value) {
                $document = '';
                $formatter = new HtmlFormatter();
                try {
                    $document = new Document($value['blob']);
                } catch (\Throwable $th) {
                }
                if (!$document) {
                    $blob = "";
                } else {
                    $blob = $formatter->Format($document);
                }
                $clis[] = [
                    'tiers' => $value['tiers'],
                    'nom' => $value['nom'],
                    'cp' => $value['cp'],
                    'tel' => $value['tel'],
                    'famille' => $value['famille'],
                    'siret' => $value['siret'],
                    'intra' => $value['intra'],
                    'blob' => $blob,
                    'montantN' => $value['montantN'],
                    'montantN1' => $value['montantN1'],
                    'montantN2' => $value['montantN2'],
                ];
            }

            $familles = $repo->getVenteClientSur3Ans($dd, $df, 'FAMILLE', $metier);
        }

        return $this->render('states_lhermitte/client3Ans.html.twig', [
            'form' => $form->createView(),
            'title' => 'States client 3 ans tous métiers',
            'clients' => $clis,
            'familles' => $familles,
        ]);
    }

}
