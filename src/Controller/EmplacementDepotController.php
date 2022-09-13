<?php

namespace App\Controller;

use App\Repository\Divalto\MouvRepository;
use App\Form\DateDebutFinFamilleDossierType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmplacementDepotController extends AbstractController
{
    /**
     * @Route("/emplacement/depot", name="app_emplacement_depot")
     */
    public function index(Request $request, MouvRepository $repo): Response
    {
        $produits = '';
        $form = $this->createForm(DateDebutFinFamilleDossierType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dd = $form->getData()['start']->format('Y-m-d');
            $df = $form->getData()['end']->format('Y-m-d');
            $dos = $form->getData()['dossier'];
            $fermeOuvert = $form->getData()['fermeOuvert'];
            $familles = $form->getData()['famille'];
            $stockOuBl = $form->getData()['stockOuBl'];
            $i = 0 ;
            $famille = '';
            foreach ($familles as $value) {
                if ($i == 0) {
                    $famille = "'" . $value . "'";
                }else {
                    $famille = $famille . ',' . "'" . $value . "'" ;
                }
            $i++;    
            }
            $produits = $repo->getNbeBlEtStockParProduit($dos, $dd, $df, $fermeOuvert,$famille,$stockOuBl);
        }

        return $this->render('emplacement_depot/index.html.twig', [
            'title' => 'Emplacement Dépôt',
            'produits' => $produits,
            'form' => $form->createView(),
        ]);
    }
}
