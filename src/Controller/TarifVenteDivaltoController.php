<?php

namespace App\Controller;

use App\Form\SearchAndFouCodeTarifType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class TarifVenteDivaltoController extends AbstractController
{
    #[Route("/Lhermitte/tarif/vente/divalto", name: "app_tarif_vente_divalto")]

    public function index(MouvRepository $repo, Request $request, StatsAchatController $mef): Response
    {

        $tarifs = "";
        $codes = "";
        $form = $this->createForm(SearchAndFouCodeTarifType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prefixe = $form->getData()['search'];
            $fous = $mef->miseEnForme($form->getData()['fournisseurs']);
            $familles = $mef->miseEnForme($form->getData()['familles']);
            $codes = $form->getData()['codeTarif'];

            $tarifs = $repo->tarifsVentesDivalto($prefixe, $fous, $familles, $codes);
        }
        //dd($tarifs);
        return $this->render('tarif_vente_divalto/index.html.twig', [
            'tarifs' => $tarifs,
            'form' => $form->createView(),
            'title' => 'Tarifs Vente',
            'codes' => $codes,
        ]);
    }
}
