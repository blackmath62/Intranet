<?php

namespace App\Controller;

use App\Form\DateDebutDateFinFournisseursType;
use App\Repository\Divalto\StatesFournisseursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class StatsAchatController extends AbstractController
{
    #[Route("/stats/achat/{dos}", name: "app_stats_achat")]

    public function index($dos, Request $request, StatesFournisseursRepository $repo): Response
    {
        $dd = '';
        $df = '';
        $fous = '';
        $fams = '';
        $metier = '';
        $states = '';
        $totauxFournisseurs = '';
        $totaux = '';
        $template = 'stats_achat/statesBasiques.html.twig';

        $form = $this->createForm(DateDebutDateFinFournisseursType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme de la liste des fournisseurs pour l'envoyer à la requête
            $fous = $this->miseEnForme($form->getData()['fournisseurs']);
            $fams = $this->miseEnForme($form->getData()['familles']);
            $tiers = $form->getData()['tiers'];
            $dd = $form->getData()['start']->format('Y-m-d');
            $df = $form->getData()['end']->format('Y-m-d');
            $metier = $this->miseEnForme($form->getData()['metier']);
            if ($form->getData()['type'] == 'dateOp') {
                $states = $repo->getStatesDetaillees($dos, $dd, $df, $fous, $fams, $metier, $tiers);
                $template = 'stats_achat/statesDetaillees.html.twig';
            }
            if ($form->getData()['type'] == 'basique') {
                $states = $repo->getStatesBasiques($dos, $dd, $df, $fous, $fams, $metier, $tiers);
            }
            if ($form->getData()['type'] == 'sansFournisseurs') {
                $states = $repo->getStatesSansFournisseurs($dos, $dd, $df, $fous, $fams, $metier, $tiers);
                $template = 'stats_achat/statesSansFournisseurs.html.twig';
            }
            $totauxFournisseurs = $repo->getTotauxStatesParFournisseurs($dos, $dd, $df, $fous, $fams, $metier, $tiers);
            $totaux = $repo->getTotauxStatesTousFournisseurs($dos, $dd, $df, $fous, $fams, $metier, $tiers);
        }

        return $this->render($template, [
            'states' => $states,
            'totauxFournisseurs' => $totauxFournisseurs,
            'totaux' => $totaux,
            'title' => 'States Achats',
            'form' => $form->createView(),
        ]);
    }

    public function miseEnForme($donnees)
    {
        $Mef = '';
        foreach ($donnees as $value) {
            if ($Mef == '') {
                $Mef = "'" . $value . "'";
            } else {
                $Mef = $Mef . ",'" . $value . "'";
            }
        }
        return $Mef;
    }
}
