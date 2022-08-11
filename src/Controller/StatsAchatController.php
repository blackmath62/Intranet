<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\DateDebutDateFinFournisseursType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\FournisseursDivaltoController;
use App\Repository\Divalto\StatesFournisseursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatsAchatController extends AbstractController
{
    /**
     * @Route("/rs/stats/achat", name="app_stats_achat")
     */
    public function index(Request $request,StatesFournisseursRepository $repo): Response
    {
        $dos = 1;
        $dd = '';
        $df = '';
        $Mef = '';
        $states = '';
        $totauxFournisseurs = '';
        $totaux = '';
        $template = 'stats_achat/statesBasiques.html.twig';

        $form = $this->createForm(DateDebutDateFinFournisseursType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme de la liste des fournisseurs pour l'envoyer à la requête
            $Mef = $this->miseEnFormeFournisseur($form->getData()['fournisseurs']);
            $dd = $form->getData()['start']->format('Y-m-d');
            $df = $form->getData()['end']->format('Y-m-d');
            if ($form->getData()['type'] == 'dateOp') {
                $states = $repo->getStatesDetaillees($dos, $dd, $df, $Mef);
                $template = 'stats_achat/statesDetaillees.html.twig';
            }
            if ($form->getData()['type'] == 'basique') {
                $states = $repo->getStatesBasiques($dos, $dd, $df, $Mef);
            }
            $totauxFournisseurs = $repo->getTotauxStatesParFournisseurs($dos, $dd, $df, $Mef);
            $totaux = $repo->getTotauxStatesTousFournisseurs($dos, $dd, $df, $Mef);
        }

        return $this->render($template, [
            'states' => $states,
            'totauxFournisseurs' => $totauxFournisseurs,
            'totaux' => $totaux,
            'title' => 'States Achats',
            'form' => $form->createView()
        ]);
    }

    public function miseEnFormeFournisseur($fournisseurs)
    {
        $Mef = '' ;
        foreach ($fournisseurs as $value) {
            if ($Mef == '') {
                $Mef = "'" . $value->getTiers() . "'";
            }else {
                $Mef = $Mef . ",'" . $value->getTiers() . "'";
            }
        }
        return $Mef;
    }
}
