<?php

namespace App\Controller;

use App\Form\StatesDateFilterType;
use App\Repository\Divalto\StatesByTiersRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class StatesParFamilleController extends AbstractController
{
    #[Route("/Roby/states/par/famille/produits/{dos}/{metier}", name: "app_states_par_famille")]
    #[Route("/Roby/states/par/famille/produits/{dos}/{metier}/{dd}/{df}", name: "app_states_par_famille_dd_df")]
    #[Route("/Roby/states/par/famille/clients/{dos}/{metier}", name: "app_states_par_famille_clients")]
    #[Route("/Roby/states/par/famille/clients/{dos}/{metier}/{dd}/{df}", name: "app_states_par_famille_clients_dd_df")]
    #[Route("/Lhermitte/states/par/famille/produits/{dos}/{metier}", name: "app_states_par_familleLh")]
    #[Route("/Lhermitte/states/par/famille/produits/{dos}/{metier}/{dd}/{df}", name: "app_states_par_famille_dd_dfLh")]
    #[Route("/Lhermitte/states/par/famille/clients/{dos}/{metier}", name: "app_states_par_famille_clientsLh")]
    #[Route("/Lhermitte/states/par/famille/clients/{dos}/{metier}/{dd}/{df}", name: "app_states_par_famille_clients_dd_dfLh")]

    public function index(Request $request, StatesByTiersRepository $repo, $dos, $metier, $dd = null, $df = null): Response
    {
        $states = "";
        $start = "";
        $end = "";
        $startN1 = "";
        $endN1 = "";
        $totaux = "";
        $familles = "";

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->getData()["startDate"];
            $end = $form->getData()["endDate"];

            $d = new DateTime($start->format('Y-m-d'));
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, $start, $end);

            $d = new DateTime($end->format('Y-m-d'));
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, $start, $end);

        } else {
            $d = new DateTime($dd);
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
            $d = new DateTime($df);
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
        }
        $trancheD = "du " . $startN . " au " . $endN;
        $trancheF = "du " . $startN1 . " au " . $endN1;

        $dd = $startN;
        $df = $endN;

        // initialisation de mes variables
        if ($request->attributes->get('_route') == 'app_states_par_famille' | $request->attributes->get('_route') == 'app_states_par_familleLh' | $request->attributes->get('_route') == 'app_states_par_famille_dd_df' | $request->attributes->get('_route') == 'app_states_par_famille_dd_dfLh') {
            $famille = "produits";
        } elseif ($request->attributes->get('_route') == 'app_states_par_famille_clients' | $request->attributes->get('_route') == 'app_states_par_famille_clients_dd_df' | $request->attributes->get('_route') == 'app_states_par_famille_clientsLh' | $request->attributes->get('_route') == 'app_states_par_famille_clients_dd_dfLh') {
            $famille = "clients";
        }

        $states = $repo->getStatesParFamilleRoby($dos, $metier, $startN, $endN, $startN1, $endN1, $famille);
        $totaux = $repo->getStatesParFamilleRobyTotaux($dos, $metier, $startN, $endN, $startN1, $endN1, $famille);
        $familles = $repo->getStatesParFamilleRobyTotauxParFamille($dos, $metier, $startN, $endN, $startN1, $endN1, $famille);

        // Données stats diagramme famille
        $dataFamille = $familles;
        for ($ligFamille = 0; $ligFamille < count($dataFamille); $ligFamille++) {
            $familleNom[] = $dataFamille[$ligFamille]['famille'];
            $familleMontantN[] = $dataFamille[$ligFamille]['montantN'];
            $familleMontantN1[] = $dataFamille[$ligFamille]['montantN1'];
            //dd($famille);
        }

        return $this->render('states_par_famille/index.html.twig', [
            'states' => $states,
            'title' => 'States par famille',
            'form' => $form->createView(),
            'dd' => $dd,
            'df' => $df,
            'trancheD' => $trancheD,
            'trancheF' => $trancheF,
            'totaux' => $totaux,
            'familles' => $familles,
            'familleNom' => json_encode($familleNom),
            'familleMontantN' => json_encode($familleMontantN),
            'familleMontantN1' => json_encode($familleMontantN1),
            'trancheDJson' => json_encode($trancheD),
            'trancheFJson' => json_encode($trancheF),
        ]);
    }

    #[Route("/Roby/states/produits/{dos}/{metier}", name: "app_states_par_produits")]
    #[Route("/Roby/states/produits/{dos}/{metier}/{dd}/{df}", name: "app_states_par_produits_dd_df")]
    #[Route("/Roby/states/clients/{dos}/{metier}", name: "app_states_par_clients")]
    #[Route("/Roby/states/clients/{dos}/{metier}/{dd}/{df}", name: "app_states_par_clients_dd_df")]
    #[Route("/Lhermitte/states/produits/{dos}/{metier}", name: "app_states_par_produitsLh")]
    #[Route("/Lhermitte/states/produits/{dos}/{metier}/{dd}/{df}", name: "app_states_par_produits_dd_dfLh")]
    #[Route("/Lhermitte/states/clients/{dos}/{metier}", name: "app_states_par_clientsLh")]
    #[Route("/Lhermitte/states/clients/{dos}/{metier}/{dd}/{df}", name: "app_states_par_clients_dd_dfLh")]

    public function getClientArticle(Request $request, StatesByTiersRepository $repo, $dos, $metier, $dd = null, $df = null): Response
    {
        $states = "";
        $start = "";
        $end = "";
        $startN1 = "";
        $endN1 = "";
        $totaux = "";

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->getData()["startDate"];
            $end = $form->getData()["endDate"];

            $d = new DateTime($start->format('Y-m-d'));
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, $start, $end);

            $d = new DateTime($end->format('Y-m-d'));
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, $start, $end);

        } else {
            $d = new DateTime($dd);
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
            $d = new DateTime($df);
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
        }
        $trancheD = "du " . $startN . " au " . $endN;
        $trancheF = "du " . $startN1 . " au " . $endN1;

        $dd = $startN;
        $df = $endN;

        // initialisation de mes variables
        if ($request->attributes->get('_route') == 'app_states_par_produits' | $request->attributes->get('_route') == 'app_states_par_produits_dd_df' | $request->attributes->get('_route') == 'app_states_par_produitsLh' | $request->attributes->get('_route') == 'app_states_par_produits_dd_dfLh') {
            $famille = "produits";
        } elseif ($request->attributes->get('_route') == 'app_states_par_clients' | $request->attributes->get('_route') == 'app_states_par_clients_dd_df' | $request->attributes->get('_route') == 'app_states_par_clientsLh' | $request->attributes->get('_route') == 'app_states_par_clients_dd_dfLh') {
            $famille = "clients";
        }

        $states = $repo->getStatesRobyTotalParClientArticle($dos, $metier, $startN, $endN, $startN1, $endN1, $famille);
        $totaux = $repo->getStatesParFamilleRobyTotaux($dos, $metier, $startN, $endN, $startN1, $endN1, $famille);
        //dd($totaux);
        return $this->render('states_par_famille/clientArticle.html.twig', [
            'states' => $states,
            'title' => 'States par ' . $famille,
            'form' => $form->createView(),
            'dd' => $dd,
            'df' => $df,
            'trancheD' => $trancheD,
            'trancheF' => $trancheF,
            'totaux' => $totaux,

        ]);
    }

    #[Route("/Roby/states/commerciaux/{dos}/{metier}/{dd}/{df}", name: "app_states_commerciaux_dd_df")]
    #[Route("/Lhermitte/states/commerciaux/{dos}/{metier}/{dd}/{df}", name: "app_states_commerciaux_dd_dfLh")]

    public function commerciaux(Request $request, StatesByTiersRepository $repo, ResumeStatesController $resume, $dos, $metier, $dd = null, $df = null): Response
    {

        $start = "";
        $end = "";
        $startN1 = "";
        $endN1 = "";
        $totaux = "";

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->getData()["startDate"];
            $end = $form->getData()["endDate"];
            $d = new DateTime($start->format('Y-m-d'));
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, $start, $end);

            $d = new DateTime($end->format('Y-m-d'));
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, $start, $end);

            return $this->redirectToRoute($request->attributes->get('_route'), [
                'dos' => $dos,
                'metier' => $metier,
                'dd' => $startN,
                'df' => $endN,
            ]);

        } else {
            $d = new DateTime($dd);
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
            $d = new DateTime($df);
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
        }
        $trancheD = "du " . $startN . " au " . $endN;
        $trancheF = "du " . $startN1 . " au " . $endN1;

        $dd = $startN;
        $df = $endN;

        // données line par commerciaux sur 6 ans
        $nomCommerciaux = [];
        $donneesCommerciaux = [];
        $anneeCommerciaux = [];
        $startCommerciaux = new DateTime('now');
        $dataCommerciaux = $repo->getStatesSixYearsAgoCommerciauxPeriode($dos, $metier, $dd, $df);
        $color = [$resume->listeCouleur(0), $resume->listeCouleur(4), $resume->listeCouleur(10), $resume->listeCouleur(6), $resume->listeCouleur(9), $resume->listeCouleur(2)];
        $datesDiff = $this->generateDateDiffs($dd, $df, 5);
        for ($i = 0; $i < count($dataCommerciaux); $i++) {

            $nomCommerciaux[] = $dataCommerciaux[$i]['commercial'];
            $donneesCommerciaux[] = [
                $dataCommerciaux[$i]['montantN5'],
                $dataCommerciaux[$i]['montantN4'],
                $dataCommerciaux[$i]['montantN3'],
                $dataCommerciaux[$i]['montantN2'],
                $dataCommerciaux[$i]['montantN1'],
                $dataCommerciaux[$i]['montantN'],
            ]; //[[], [], []];
            $anneeCommerciaux = [
                $datesDiff[4]['dd'] . ' => ' . $datesDiff[4]['df'],
                $datesDiff[3]['dd'] . ' => ' . $datesDiff[3]['df'],
                $datesDiff[2]['dd'] . ' => ' . $datesDiff[2]['df'],
                $datesDiff[1]['dd'] . ' => ' . $datesDiff[1]['df'],
                $datesDiff[0]['dd'] . ' => ' . $datesDiff[0]['df'],
                $dd . ' => ' . $df,
            ];
            $couleurCommercial[] = 'rgb(' . $color[$i] . ')';
        }

        // Données Produits
        $topProduit = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topProduitResume');
        $topFamilleProduit = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topFamilleProduit');

        // données ligne par commercial sur 5 ans
        $nomCommercialFamilleProduit = [];
        $donneesCommercialFamilleProduit = [];
        $anneeCommercialFamilleProduit = [];
        $dataCommercialFamilleProduit = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topFamilleProduit');

        //dd($dataCommercialFamilleProduit);

        for ($i = 0; $i < count($dataCommercialFamilleProduit); $i++) {

            $nomCommercialFamilleProduit[] = $dataCommercialFamilleProduit[$i]['familleProduit'];
            $donneesCommercialFamilleProduit[] = [
                $dataCommercialFamilleProduit[$i]['montantN3'],
                $dataCommercialFamilleProduit[$i]['montantN2'],
                $dataCommercialFamilleProduit[$i]['montantN1'],
                $dataCommercialFamilleProduit[$i]['montantN'],
            ]; //[[], [], []];
            $anneeCommercialFamilleProduit = [
                $datesDiff[2]['dd'] . ' => ' . $datesDiff[2]['df'],
                $datesDiff[1]['dd'] . ' => ' . $datesDiff[1]['df'],
                $datesDiff[0]['dd'] . ' => ' . $datesDiff[0]['df'],
                $dd . ' => ' . $df,
            ];
            $couleurCommercialFamilleProduit[] = 'rgb(' . $resume->listeCouleur($i) . ')';
        }

        // Données Clients
        $topClient = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topClient');
        $topFournisseur = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topFournisseur');
        $topFamilleClient = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topFamilleClient');

        // données line par commercial sur 5 ans
        $nomCommercialFamilleClient = [];
        $donneesCommercialFamilleClient = [];
        $anneeCommercialFamilleClient = [];
        $dataCommercialFamilleClient = $repo->StatesCommercial($dos, $metier, $startN, $endN, null, 'topFamilleClient');

        for ($i = 0; $i < count($dataCommercialFamilleClient); $i++) {

            $nomCommercialFamilleClient[] = $dataCommercialFamilleClient[$i]['familleClient'];
            $donneesCommercialFamilleClient[] = [
                $dataCommercialFamilleClient[$i]['montantN3'],
                $dataCommercialFamilleClient[$i]['montantN2'],
                $dataCommercialFamilleClient[$i]['montantN1'],
                $dataCommercialFamilleClient[$i]['montantN'],
            ]; //[[], [], []];
            $anneeCommercialFamilleClient = [
                $datesDiff[2]['dd'] . ' => ' . $datesDiff[2]['df'],
                $datesDiff[1]['dd'] . ' => ' . $datesDiff[1]['df'],
                $datesDiff[0]['dd'] . ' => ' . $datesDiff[0]['df'],
                $dd . ' => ' . $df,
            ];
            $couleurCommercialFamilleClient[] = 'rgb(' . $resume->listeCouleur($i) . ')';
        }
        $totaux = $repo->totauxStatesCommerciaux($dos, $metier, $dd, $df, null);
        //dd($totaux);
        return $this->render('states_par_famille/commerciaux.html.twig', [
            'title' => 'States par commerciaux',
            'form' => $form->createView(),
            'dd' => $dd,
            'df' => $df,
            'trancheD' => $trancheD,
            'trancheF' => $trancheF,
            'nomCommerciaux' => json_encode($nomCommerciaux),
            'anneeCommerciaux' => json_encode($anneeCommerciaux),
            'donneesCommerciaux' => json_encode($donneesCommerciaux),
            'couleurCommercial' => json_encode($couleurCommercial),
            "dataCommerciaux" => $dataCommerciaux,
            'topProduits' => $topProduit,
            'topFamilleProduits' => $topFamilleProduit,
            'nomCommercialFamilleProduit' => json_encode($nomCommercialFamilleProduit),
            'anneeCommercialFamilleProduit' => json_encode($anneeCommercialFamilleProduit),
            'donneesCommercialFamilleProduit' => json_encode($donneesCommercialFamilleProduit),
            'couleurCommercialFamilleProduit' => json_encode($couleurCommercialFamilleProduit),
            'topClients' => $topClient,
            'topFournisseurs' => $topFournisseur,
            'topFamilleClients' => $topFamilleClient,
            'nomCommercialFamilleClient' => json_encode($nomCommercialFamilleClient),
            'anneeCommercialFamilleClient' => json_encode($anneeCommercialFamilleClient),
            'donneesCommercialFamilleClient' => json_encode($donneesCommercialFamilleClient),
            'couleurCommercialFamilleClient' => json_encode($couleurCommercialFamilleClient),
            'totaux' => $totaux,
        ]);
    }

    #[Route("/Roby/states/commercial/{dos}/{metier}/{commercial}/{dd}/{df}", name: "app_states_commercial_dd_df")]
    #[Route("/Lhermitte/states/commercial/{dos}/{metier}/{commercial}/{dd}/{df}", name: "app_states_commercial_dd_dfLh")]

    public function commercial(Request $request, StatesByTiersRepository $repo, ResumeStatesController $resume, $dos, $metier, $commercial = null, $dd = null, $df = null): Response
    {

        $start = "";
        $end = "";
        $startN1 = "";
        $endN1 = "";

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->getData()["startDate"];
            $end = $form->getData()["endDate"];

            $d = new DateTime($start->format('Y-m-d'));
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, $start, $end);

            $d = new DateTime($end->format('Y-m-d'));
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, $start, $end);

        } else {
            $d = new DateTime($dd);
            $startN = $this->getYmd($d);
            $startN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
            $d = new DateTime($df);
            $endN = $this->getYmd($d);
            $endN1 = $this->getDateDiff($d, new DateTime($dd), new DateTime($df));
        }

        // Données Clients
        $topClient = $repo->StatesCommercial($dos, $metier, $startN, $endN, $commercial, 'topClient');
        $topFamilleClient = $repo->StatesCommercial($dos, $metier, $startN, $endN, $commercial, 'topFamilleClient');

        // données ligne par commercial sur 5 ans
        $nomCommercialFamilleClient = [];
        $donneesCommercialFamilleClient = [];
        $anneeCommercialFamilleClient = [];
        $dataCommercialFamilleClient = $repo->StatesCommercial($dos, $metier, $startN, $endN, $commercial, 'topFamilleClient');
        $datesDiff = $this->generateDateDiffs($dd, $df, 5);

        //dd($dataCommercialFamilleClient);

        for ($i = 0; $i < count($dataCommercialFamilleClient); $i++) {

            $nomCommercialFamilleClient[] = $dataCommercialFamilleClient[$i]['familleClient'];
            $donneesCommercialFamilleClient[] = [
                $dataCommercialFamilleClient[$i]['montantN3'],
                $dataCommercialFamilleClient[$i]['montantN2'],
                $dataCommercialFamilleClient[$i]['montantN1'],
                $dataCommercialFamilleClient[$i]['montantN'],
            ]; //[[], [], []];
            $anneeCommercialFamilleClient = [
                $datesDiff[2]['dd'] . ' => ' . $datesDiff[2]['df'],
                $datesDiff[1]['dd'] . ' => ' . $datesDiff[1]['df'],
                $datesDiff[0]['dd'] . ' => ' . $datesDiff[0]['df'],
                $dd . ' => ' . $df,
            ];
            $couleurCommercialFamilleClient[] = 'rgb(' . $resume->listeCouleur($i) . ')';
        }

        // Données Produits
        $topProduit = $repo->StatesCommercial($dos, $metier, $startN, $endN, $commercial, 'topProduit');
        $topFamilleProduit = $repo->StatesCommercial($dos, $metier, $startN, $endN, $commercial, 'topFamilleProduit');

        // données line par commercial sur 5 ans
        $nomCommercialFamilleProduit = [];
        $donneesCommercialFamilleProduit = [];
        $anneeCommercialFamilleProduit = [];
        $dataCommercialFamilleProduit = $repo->StatesCommercial($dos, $metier, $startN, $endN, $commercial, 'topFamilleProduit');

        for ($i = 0; $i < count($dataCommercialFamilleProduit); $i++) {

            $nomCommercialFamilleProduit[] = $dataCommercialFamilleProduit[$i]['familleProduit'];
            $donneesCommercialFamilleProduit[] = [
                $dataCommercialFamilleProduit[$i]['montantN3'],
                $dataCommercialFamilleProduit[$i]['montantN2'],
                $dataCommercialFamilleProduit[$i]['montantN1'],
                $dataCommercialFamilleProduit[$i]['montantN'],
            ]; //[[], [], []];
            $anneeCommercialFamilleProduit = [
                $datesDiff[2]['dd'] . ' => ' . $datesDiff[2]['df'],
                $datesDiff[1]['dd'] . ' => ' . $datesDiff[1]['df'],
                $datesDiff[0]['dd'] . ' => ' . $datesDiff[0]['df'],
                $dd . ' => ' . $df,
            ];
            $couleurCommercialFamilleProduit[] = 'rgb(' . $resume->listeCouleur($i) . ')';
        }

        $trancheD = "du " . $startN . " au " . $endN;
        $trancheF = "du " . $startN1 . " au " . $endN1;

        $dd = $startN;
        $df = $endN;

        $totaux = $repo->totauxStatesCommerciaux($dos, $metier, $dd, $df, $commercial);
        return $this->render('states_par_famille/commercial.html.twig', [
            'title' => 'States ' . $commercial,
            'form' => $form->createView(),
            'dd' => $dd,
            'df' => $df,
            'trancheD' => $trancheD,
            'trancheF' => $trancheF,
            'topClients' => $topClient,
            'topFamilleClients' => $topFamilleClient,
            'nomCommercialFamilleClient' => json_encode($nomCommercialFamilleClient),
            'anneeCommercialFamilleClient' => json_encode($anneeCommercialFamilleClient),
            'donneesCommercialFamilleClient' => json_encode($donneesCommercialFamilleClient),
            'couleurCommercialFamilleClient' => json_encode($couleurCommercialFamilleClient),
            'topProduits' => $topProduit,
            'topFamilleProduits' => $topFamilleProduit,
            'nomCommercialFamilleProduit' => json_encode($nomCommercialFamilleProduit),
            'anneeCommercialFamilleProduit' => json_encode($anneeCommercialFamilleProduit),
            'donneesCommercialFamilleProduit' => json_encode($donneesCommercialFamilleProduit),
            'couleurCommercialFamilleProduit' => json_encode($couleurCommercialFamilleProduit),
            'totaux' => $totaux,
        ]);
    }

    // date à modifier / date de début de référence / date de fin de référence
    public function getDateDiff($d, $dd, $df)
    {
        // Vérifiez et convertissez les paramètres en objets DateTime si nécessaire
        $d = $this->ensureDateTime($d);
        $dd = $this->ensureDateTime($dd);
        $df = $this->ensureDateTime($df);

        // Si une des dates n'a pas pu être convertie, retournez un message d'erreur
        if (!$d || !$dd || !$df) {
            return "Une des dates fournies est invalide.";
        }

        $yearStart = $dd->format('Y');
        $yearEnd = $df->format('Y');
        $yearDiff = $yearEnd - $yearStart;
        $yearDiff = $yearDiff + 1;
        $maDate = date_modify($d, '-' . $yearDiff . ' Year');
        $ymd = $this->getYmd($maDate);
        return $ymd;
    }

    public function getYmd($d)
    {
        return $d->format('Y') . '-' . $d->format('m') . '-' . $d->format('d');
    }

    private function ensureDateTime($date)
    {
        if ($date instanceof \DateTime) {
            return $date;
        }

        try {
            return new \DateTime($date);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateDateDiffs($startN, $endN, $iterations): array
    {
        $results = [];

        $dd = $startN;
        $df = $endN;

        for ($i = 1; $i <= $iterations; $i++) {
            $ddN = $this->getDateDiff($dd, $startN, $endN);
            $dfN = $this->getDateDiff($df, $startN, $endN);

            $results[] = [
                'dd' => $ddN,
                'df' => $dfN,
            ];

            $dd = $ddN;
            $df = $dfN;
        }

        return $results;
    }
}
