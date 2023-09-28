<?php

namespace App\Controller;

use App\Form\ActivitesMetierRobyType;
use App\Form\ActivitesMetierType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class ActiviteMetierController extends AbstractController
{
    #[Route("/Lhermitte/activite/metier", name: "app_activite_metier_Lhermitte")]
    #[Route("/Roby/activite/metier", name: "app_activite_metier_Roby")]

    public function index(MouvRepository $repo, Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);
        // initialisation de mes variables
        if ($request->attributes->get('_route') == 'app_activite_metier_Roby') {
            $form = $this->createForm(ActivitesMetierRobyType::class);
            $dos = '3';
        } elseif ($request->attributes->get('_route') == 'app_activite_metier_Lhermitte') {
            $form = $this->createForm(ActivitesMetierType::class);
            $dos = '1';
        }

        $activites = '';
        $activitesClient = '';
        $activitesFou = '';
        $total = '';
        $totalFou = '';
        $familleProduit = [];
        $colorProduit = [];
        $montantProduit = [];
        $countDataProduit = [];
        $familleClient = [];
        $colorClient = [];
        $montantClient = [];
        $countDataClient = [];

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dd = $form->getData()['start']->format('Y-m-d');
            $df = $form->getData()['end']->format('Y-m-d');
            $metier = $form->getData()['Metiers'];
            $activites = $repo->getActivitesMetier($dos, $dd, $df, $metier);
            $total = $repo->getTotalActivitesMetier($dos, $dd, $df, $metier, 'CLI');
            $activitesClient = $repo->getActivitesMetierClient($dos, $dd, $df, $metier, 'CLI');
            $totalFou = $repo->getTotalActivitesMetier($dos, $dd, $df, $metier, 'FOU');
            $activitesFou = $repo->getActivitesMetierClient($dos, $dd, $df, $metier, 'FOU');

            // Données stats camenbert Produits
            $dataProduit = $repo->getActivitesFamilleProduit($dos, $dd, $df, $metier);
            $countDataProduit = count($dataProduit);
            for ($ligFamProduit = 0; $ligFamProduit < count($dataProduit); $ligFamProduit++) {
                $r = 195 + $dataProduit[$ligFamProduit]['montantSign'] % 50;
                $g = 195 + round($dataProduit[$ligFamProduit]['montantSign'] / 3) % 50;
                $b = 195 + round($dataProduit[$ligFamProduit]['montantSign'] / 7) % 50;

                $familleProduit[] = $dataProduit[$ligFamProduit]['famille'];
                $montantProduit[] = $dataProduit[$ligFamProduit]['montantSign'];
                $colorProduit[] = 'rgba(' . $r . ',' . $g . ', ' . $b . ', 1)';
            }

            // Données stats camenbert Client
            $dataClient = $repo->getActivitesFamilleClient($dos, $dd, $df, $metier);
            $countDataClient = count($dataClient);
            for ($ligFamClient = 0; $ligFamClient < count($dataClient); $ligFamClient++) {
                $r = 195 + $dataClient[$ligFamClient]['montantSign'] % 50;
                $g = 195 + round($dataClient[$ligFamClient]['montantSign'] / 3) % 50;
                $b = 195 + round($dataClient[$ligFamClient]['montantSign'] / 7) % 50;

                $familleClient[] = $dataClient[$ligFamClient]['famille'];
                $montantClient[] = $dataClient[$ligFamClient]['montantSign'];
                $colorClient[] = 'rgba(' . $r . ',' . $g . ', ' . $b . ', 1)';
            }

        }

        return $this->render('activite_metier/index.html.twig', [
            'activites' => $activites,
            'activiteClients' => $activitesClient,
            'activiteFournisseurs' => $activitesFou,
            'total' => $total,
            'totalFou' => $totalFou,
            'form' => $form->createView(),
            'familleProduit' => json_encode($familleProduit),
            'colorProduit' => json_encode($colorProduit),
            'montantProduit' => json_encode($montantProduit),
            'countDataProduit' => $countDataProduit,
            'familleClient' => json_encode($familleClient),
            'colorClient' => json_encode($colorClient),
            'montantClient' => json_encode($montantClient),
            'countDataClient' => $countDataClient,
        ]);
    }
}
