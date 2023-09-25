<?php

namespace App\Controller;

use App\Form\DateDebutFinFamilleDossierType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmplacementDepotController extends AbstractController
{
    #[Route("/emplacement/depot", name: "app_emplacement_depot")]
    public function index(Request $request, MouvRepository $repo): Response
    {
        $produits = '';
        $fam = [];
        $color = [];
        $count = [];
        $form = $this->createForm(DateDebutFinFamilleDossierType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dd = $form->getData()['start']->format('Y-m-d');
            $df = $form->getData()['end']->format('Y-m-d');
            $dos = $form->getData()['dossier'];
            $fermeOuvert = $form->getData()['fermeOuvert'];
            $familles = $form->getData()['famille'];
            $stockOuBl = $form->getData()['stockOuBl'];
            $i = 0;
            $famille = '';
            foreach ($familles as $value) {
                if ($i == 0) {
                    $famille = "'" . $value . "'";
                } else {
                    $famille = $famille . ',' . "'" . $value . "'";
                }
                $i++;
            }
            $produits = $repo->getNbeBlEtStockParProduit($dos, $dd, $df, $fermeOuvert, $famille, $stockOuBl);

            $data = $repo->getNbeBlEtStockParFamille($dos, $dd, $df, $fermeOuvert, $famille, $stockOuBl);
            /*for ($ligFamille=0; $ligFamille <count($data) ; $ligFamille++) {
            if (!empty($data[$ligFamille]['famille'])) {
            //dd($data[$ligFamille]['famille']);
            $famille[] = $data[$ligFamille]['famille'];
            $count[] = $data[$ligFamille]['nbeBl'];
            $color[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ', ' . rand(0, 255) . ', 1)';
            }
            }*/
            foreach ($data as $value) {
                $fam[] = $value['famille'];
                $count[] = $value['nbeBl'];
                $color[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ', ' . rand(0, 255) . ', 1)';
            }

        }

        return $this->render('emplacement_depot/index.html.twig', [
            'title' => 'Emplacement Dépôt',
            'produits' => $produits,
            'form' => $form->createView(),
            'famille' => json_encode($fam),
            'color' => json_encode($color),
            'count' => json_encode($count),
        ]);
    }
}
