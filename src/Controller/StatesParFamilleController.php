<?php

namespace App\Controller;

use App\Form\StatesDateFilterType;
use App\Repository\Divalto\StatesByTiersRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatesParFamilleController extends AbstractController
{
    /**
     * @Route("/Roby/states/par/famille/produits/{dos}", name="app_states_par_famille")
     * @Route("/Roby/states/par/famille/produits/{dos}/{dd}/{df}", name="app_states_par_famille_dd_df")
     *  @Route("/Roby/states/par/famille/clients/{dos}", name="app_states_par_famille_clients")
     * @Route("/Roby/states/par/famille/clients/{dos}/{dd}/{df}", name="app_states_par_famille_clients_dd_df")
     */
    public function index($dos, $dd = null, $df = null, Request $request, StatesByTiersRepository $repo): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
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
        if ($request->attributes->get('_route') == 'app_states_par_famille' | $request->attributes->get('_route') == 'app_states_par_famille_dd_df') {
            $famille = "produits";
        } elseif ($request->attributes->get('_route') == 'app_states_par_famille_clients' | $request->attributes->get('_route') == 'app_states_par_famille_clients_dd_df') {
            $famille = "clients";
        }

        $states = $repo->getStatesParFamilleRoby($dos, $startN, $endN, $startN1, $endN1, $famille);
        $totaux = $repo->getStatesParFamilleRobyTotaux($dos, $startN, $endN, $startN1, $endN1, $famille);
        $familles = $repo->getStatesParFamilleRobyTotauxParFamille($dos, $startN, $endN, $startN1, $endN1, $famille);

        // Données stats diagramme famille
        $dataFamille = $repo->getStatesParFamilleRobyTotauxParFamille($dos, $startN, $endN, $startN1, $endN1, $famille);
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

    /**
     * @Route("/Roby/states/produits/{dos}", name="app_states_par_produits")
     * @Route("/Roby/states/produits/{dos}/{dd}/{df}", name="app_states_par_produits_dd_df")
     *  @Route("/Roby/states/clients/{dos}", name="app_states_par_clients")
     * @Route("/Roby/states/clients/{dos}/{dd}/{df}", name="app_states_par_clients_dd_df")
     */
    public function getClientArticle($dos, $dd = null, $df = null, Request $request, StatesByTiersRepository $repo): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
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
        if ($request->attributes->get('_route') == 'app_states_par_produits' | $request->attributes->get('_route') == 'app_states_par_produits_dd_df') {
            $famille = "produits";
        } elseif ($request->attributes->get('_route') == 'app_states_par_clients' | $request->attributes->get('_route') == 'app_states_par_clients_dd_df') {
            $famille = "clients";
        }

        $states = $repo->getStatesRobyTotalParClientArticle($dos, $startN, $endN, $startN1, $endN1, $famille);
        $totaux = $repo->getStatesParFamilleRobyTotaux($dos, $startN, $endN, $startN1, $endN1, $famille);
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

    public function getDateDiff($d, $dd, $df)
    {

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
}
