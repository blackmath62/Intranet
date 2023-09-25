<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\ControleComptabiliteAchatRepository;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\ControleComptabiliteVenteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_COMPTA")]

class ControleComptabiliteController extends AbstractController
{
    #[Route("/compta/controle/comptabilite/{slug}", name: "app_controle_comptabilite")]

    public function controleComptabilite($slug, Request $request, ControleComptabiliteRepository $repo, ControleComptabiliteAchatRepository $repoAchat, ControleComptabiliteVenteRepository $repoVente): Response
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $typeTiers = $slug;
        if ($slug == 'F') {
            $tiers = 'Fournisseurs';
        }
        if ($slug == 'C') {
            $tiers = 'Clients';
        }
        $controleTaxes = "";
        $controleRegimeTiers = "";
        $controleRegimeTransport = "";
        $controleRegimeArticles = "";
        $form = $this->createForm(YearMonthType::class);
        $form->handleRequest($request);
        // initialisation de mes variables
        $annee = '';
        $mois = '';

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $annee = $form->getData()['year'];
            $mois = $form->getData()['month'];

            $controleTaxes = $repo->getControleTaxesComptabilite($annee, $mois, $typeTiers);
            if ($typeTiers == 'F') {
                $controleRegimeTiers = $repoAchat->getControleRegimeTiersAchat($annee, $mois);
            }
            if ($typeTiers == 'C') {
                $controleRegimeTiers = $repoVente->getControleRegimeTiersVente($annee, $mois);
            }

            $controleRegimeTransport = $repo->getControleRegimeTransport($annee, $mois, $typeTiers);

            $controleRegimeArticles = $repo->getRegimeArticleFromOrder($annee, $mois, $typeTiers);
        }

        return $this->render('controle_comptabilite/index.html.twig', [
            'title' => 'Contrôle Comptabilité',
            'annee' => $annee,
            'mois' => $mois,
            'typeTiers' => $tiers,
            'controleTaxes' => $controleTaxes,
            'controleRegimesTiers' => $controleRegimeTiers,
            'controleRegimesTransports' => $controleRegimeTransport,
            'controleRegimeArticles' => $controleRegimeArticles,
            'monthYear' => $form->createView(),
        ]);
    }

}
