<?php

namespace App\Controller;

use App\Form\StatesDateFilterType;
use App\Repository\Divalto\StatesByTiersRepository;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class ResumeStatesController extends AbstractController
{
    #[Route("/resume/states/{dos}", name: "app_resume_states")]
    #[Route("/resume/states/{dos}/{dd}/{df}", name: "app_resume_states_dd_df")]

    public function index(LoggerInterface $logger, Request $request, StatesByTiersRepository $repoTiers, StatesParFamilleController $controlArticle, $dos, $dd = null, $df = null): Response
    {

        // pas de limite en temps d'execution
        ini_set('max_execution_time', 0);
        $total = '';
        $familleProduit = [];
        $colorProduit = [];
        $montantProduit = [];
        $familleClient = [];
        $colorClient = [];
        $montantClient = [];

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startN = $form->getData()['startDate']->format('Y-m-d');
            $endN = $form->getData()['endDate']->format('Y-m-d');

            $s1 = new DateTime($form->getData()['startDate']->format('Y-m-d'));
            $e1 = new DateTime($form->getData()['endDate']->format('Y-m-d'));

            $startN1 = $controlArticle->getDateDiff($s1, $s1, $e1);

            $s1 = new DateTime($form->getData()['startDate']->format('Y-m-d'));
            $e1 = new DateTime($form->getData()['endDate']->format('Y-m-d'));

            $endN1 = $controlArticle->getDateDiff($e1, $s1, $e1);

        } else {
            if ($dd == null | $df == null) {
                $startN = new DateTime(date("Y") . "-01-01");
                $endN = new DateTime('now');

                $startN1 = $controlArticle->getDateDiff($startN, $startN, $endN);

                $startN = new DateTime(date("Y") . "-01-01");
                $endN = new DateTime('now');

                $endN1 = $controlArticle->getDateDiff($endN, $startN, $endN);

                $startN = new DateTime(date("Y") . "-01-01");
                $endN = new DateTime('now');

                $startN = $controlArticle->getYmd($startN);
                $endN = $controlArticle->getYmd($endN);

            } else {
                $startN = $dd;
                $endN = $df;
                $startN1 = $controlArticle->getDateDiff(new DateTime($dd), new DateTime($dd), new DateTime($df));
                $endN1 = $controlArticle->getDateDiff(new DateTime($df), new DateTime($dd), new DateTime($df));
            }
        }

        // Données stats camenbert Produits
        $timeStart = microtime(true);
        $dataProduit = $repoTiers->getStatesParFamilleRobyTotauxParFamilleOneTrancheYear($dos, $startN, $endN, "produits");
        for ($ligFamProduit = 0; $ligFamProduit < count($dataProduit); $ligFamProduit++) {
            $familleProduit[] = $dataProduit[$ligFamProduit]['famille'];
            $montantProduit[] = $dataProduit[$ligFamProduit]['montantN'];
            $colorProduit[] = 'rgba(' . $this->listeCouleur($ligFamProduit) . ')';
        }
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataProduit : {$executionTime} secondes");
        $executionTime = 0;
        // Données stats camenbert Client
        $timeStart = microtime(true);
        $dataClient = $repoTiers->getStatesParFamilleRobyTotauxParFamilleOneTrancheYear($dos, $startN, $endN, "clients");
        for ($ligFamClient = 0; $ligFamClient < count($dataClient); $ligFamClient++) {
            $familleClient[] = $dataClient[$ligFamClient]['famille'];
            $montantClient[] = $dataClient[$ligFamClient]['montantN'];
            $colorClient[] = 'rgba(' . $this->listeCouleur($ligFamClient) . ')';
        }
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataClient : {$executionTime} secondes");
        $executionTime = 0;
        // Données stats diagramme mois
        $timeStart = microtime(true);
        $dataMois = $repoTiers->getStatesRobyTotalParClientArticle($dos, $startN, $endN, $startN1, $endN1, 'mois');
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataMoisRequete : {$executionTime} secondes");
        $executionTime = 0;

        $timeStart = microtime(true);
        for ($ligMois = 0; $ligMois < count($dataMois); $ligMois++) {
            $mois[] = $dataMois[$ligMois]['mois'];
            $moisMontantN[] = $dataMois[$ligMois]['montantN'];
            $moisMontantN1[] = $dataMois[$ligMois]['montantN1'];
        }
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataMoisBoucle : {$executionTime} secondes");
        $executionTime = 0;

        // totaux
        $timeStart = microtime(true);
        $total = $repoTiers->getStatesRobyTotal($dos, $startN, $endN);
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête getStatesRobyTotal : {$executionTime} secondes");
        $executionTime = 0;

        $timeStart = microtime(true);
        $nbClients = count($repoTiers->getStatesRobyTotalParClient($dos, $startN, $endN));
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête getStatesRobyTotalParClient : {$executionTime} secondes");
        $executionTime = 0;

        $timeStart = microtime(true);
        $nbProduits = count($repoTiers->getStatesRobyTotalParProduit($dos, $startN, $endN));
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête getStatesRobyTotalParProduit : {$executionTime} secondes");
        $executionTime = 0;

        $timeStart = microtime(true);
        $pourcTotaux = $repoTiers->getStatesParFamilleRobyTotaux($dos, $startN, $endN, $startN1, $endN1, 'produits');
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête getStatesParFamilleRobyTotaux : {$executionTime} secondes");
        $executionTime = 0;

        // données line sur 7 ans
        $timeStart = microtime(true);
        $dataSeven = $repoTiers->getStatesSevenYearsAgo($dos, 'annee');

        for ($ligSeven = 0; $ligSeven < count($dataSeven); $ligSeven++) {
            $sevenAnnee[] = $dataSeven[$ligSeven]['annee'];
            $sevenMontant[] = $dataSeven[$ligSeven]['montant'];
        }
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataSeven : {$executionTime} secondes");
        $executionTime = 0;

        // données diagramme famille et type article
        // j'ai retiré cette partie car elle est trop conssomatrice en temps de traitement.
        $timeStart = microtime(true);
        //$dataFamilleTypeArt = $repoTiers->getStatesParFamilleTypeArticle($dos, $startN, $endN);
        $artFamilleTypeTab = array();
        $ArtFamille = [];
        $ArtType = [];
        $ArtMontantType = [];
        $ArtTotalFamille = [];
        /*
        for ($ligTypeArt = 0; $ligTypeArt < count($dataFamilleTypeArt); $ligTypeArt++) {
        $varArtTotalFamille = $repoTiers->getStatesTotalParFamille($dos, $startN, $endN, $dataFamilleTypeArt[$ligTypeArt]['famille']);
        $ArtFamille[] = $dataFamilleTypeArt[$ligTypeArt]['famille'];
        $ArtType[] = $dataFamilleTypeArt[$ligTypeArt]['typeArt'];
        $ArtMontantType[] = $dataFamilleTypeArt[$ligTypeArt]['montant'];
        $ArtTotalFamille[] = $varArtTotalFamille;

        $artFamilleTypeTab[] = [$dataFamilleTypeArt[$ligTypeArt]['famille'],
        $dataFamilleTypeArt[$ligTypeArt]['typeArt'],
        $dataFamilleTypeArt[$ligTypeArt]['montant'],
        $varArtTotalFamille,
        $repoTiers->getStatesTotalParType($dos, $startN, $endN, $dataFamilleTypeArt[$ligTypeArt]['typeArt']),
        ];

        }*/
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataFamilleTypeArtTab : {$executionTime} secondes");
        $executionTime = 0;

        // données line par commerciaux sur 6 ans
        $timeStart = microtime(true);
        $nomCommerciaux = [];
        $donneesCommerciaux = [];
        $anneeCommerciaux = [];
        $startCommerciaux = new DateTime('now');
        $startyear = $startCommerciaux->format('Y');
        $dataCommerciaux = $repoTiers->getStatesSixYearsAgoCommerciaux($dos);
        $color = [$this->listeCouleur(0), $this->listeCouleur(4), $this->listeCouleur(10), $this->listeCouleur(6), $this->listeCouleur(9), $this->listeCouleur(2)];
        for ($i = 0; $i < count($dataCommerciaux); $i++) {

            $nomCommerciaux[] = $dataCommerciaux[$i]['commercial'];
            $donneesCommerciaux[] = [
                $dataCommerciaux[$i]['montantN5'],
                $dataCommerciaux[$i]['montantN4'],
                $dataCommerciaux[$i]['montantN3'],
                $dataCommerciaux[$i]['montantN2'],
                $dataCommerciaux[$i]['montantN1'],
                //$dataCommerciaux[$i]['montantN'],
            ]; //[[], [], []];
            $anneeCommerciaux = [
                $startyear - 5,
                $startyear - 4,
                $startyear - 3,
                $startyear - 2,
                $startyear - 1,
                //$startyear,
            ];
            $couleurCommercial[] = 'rgb(' . $color[$i] . ')';
        }
        $timeEnd = microtime(true);
        $executionTime = $timeEnd - $timeStart;
        $logger->info("Temps d'exécution de la requête dataCommerciaux : {$executionTime} secondes");
        $executionTime = 0;

        $trancheF = "du " . $startN . " au " . $endN;
        $trancheD = "du " . $startN1 . " au " . $endN1;

        return $this->render('resume_states/index.html.twig', [
            'title' => "Résumé des states",
            'form' => $form->createView(),
            'familleProduit' => json_encode($familleProduit),
            'colorProduit' => json_encode($colorProduit),
            'montantProduit' => json_encode($montantProduit),
            'familleClient' => json_encode($familleClient),
            'colorClient' => json_encode($colorClient),
            'montantClient' => json_encode($montantClient),
            'mois' => json_encode($mois),
            'moisMontantN' => json_encode($moisMontantN),
            'moisMontantN1' => json_encode($moisMontantN1),
            'dd' => $startN,
            'df' => $endN,
            'total' => $total,
            'nbClient' => $nbClients,
            'nbProduits' => $nbProduits,
            'pourcTotaux' => $pourcTotaux,
            'trancheDJson' => json_encode($trancheD),
            'trancheFJson' => json_encode($trancheF),
            'trancheD' => $trancheD,
            'trancheF' => $trancheF,
            'sevenAnnee' => json_encode($sevenAnnee),
            'sevenMontant' => json_encode($sevenMontant),
            'nomCommerciaux' => json_encode($nomCommerciaux),
            'anneeCommerciaux' => json_encode($anneeCommerciaux),
            'donneesCommerciaux' => json_encode($donneesCommerciaux),
            'couleurCommercial' => json_encode($couleurCommercial),
            'artFamille' => json_encode($ArtFamille),
            'artType' => json_encode($ArtType),
            'artMontantType' => json_encode($ArtMontantType),
            'artTotalFamille' => json_encode($ArtTotalFamille),
            'artFamilleTypetabs' => $artFamilleTypeTab,
        ]);
    }

    public function couleurAutomatique($mot)
    {
        $nombre = "";
        $arr = str_split($mot);
        foreach ($arr as $value) {
            $n = $this->alphabetNumérique($value);
            $nombre = $nombre . $n;
        }
        return $nombre;

    }

    public function alphabetNumérique($lettre)
    {
        switch ($lettre) {
            case 'A':
                $v = 1;
                break;
            case 'B':
                $v = 2;
                break;
            case 'C':
                $v = 3;
                break;
            case 'D':
                $v = 4;
                break;
            case 'E':
                $v = 5;
                break;
            case 'F':
                $v = 6;
                break;
            case 'G':
                $v = 7;
                break;
            case 'H':
                $v = 8;
                break;
            case 'I':
                $v = 9;
                break;
            case 'J':
                $v = 10;
                break;
            case 'K':
                $v = 11;
                break;
            case 'L':
                $v = 12;
                break;
            case 'M':
                $v = 13;
                break;
            case 'N':
                $v = 14;
                break;
            case 'O':
                $v = 15;
                break;
            case 'P':
                $v = 16;
                break;
            case 'Q':
                $v = 17;
                break;
            case 'R':
                $v = 18;
                break;
            case 'S':
                $v = 19;
                break;
            case 'T':
                $v = 20;
                break;
            case 'U':
                $v = 21;
                break;
            case 'V':
                $v = 22;
                break;
            case 'W':
                $v = 23;
                break;
            case 'X':
                $v = 24;
                break;
            case 'Y':
                $v = 25;
                break;
            case 'Z':
                $v = 26;
                break;
            default:
                $v = 0;
                break;
        }
        return $v;
    }

    public function listeCouleur($n)
    {
        switch ($n) {
            case '0':
                $couleur = '97, 177, 70,1'; // vert
                break;
            case '1':
                $couleur = "208, 221, 55,1"; //vert clair
                break;
            case '2':
                $couleur = "242, 235, 58"; // jaune
                break;
            case '3':
                $couleur = "248, 189, 24"; // orange
                break;
            case '4':
                $couleur = "249, 155, 31"; // orange foncé
                break;
            case '5':
                $couleur = "240, 83, 37"; // rouge clair
                break;
            case '6':
                $couleur = "240, 49, 36"; // rouge
                break;
            case '7':
                $couleur = "167, 30, 72"; // violet clair
                break;
            case '8':
                $couleur = "124, 54, 150"; // violet
                break;
            case '9':
                $couleur = "70, 50, 145"; // violet foncé
                break;
            case '10':
                $couleur = "62, 94, 171"; // bleu foncé
                break;
            case '11':
                $couleur = "20, 149, 207"; // bleu clair
                break;

            default:
                $couleur = "108, 117, 125,1"; //secondary
                break;

        }
        return $couleur;
    }
}
