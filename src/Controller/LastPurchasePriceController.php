<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use App\Repository\Divalto\StocksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LastPurchasePriceController extends AbstractController
{
    #[Route('/last/purchase/price', name: 'app_last_purchase_price')]
    public function index(Request $request, ControleArtStockMouvEfRepository $repo, StocksRepository $repoStock): Response
    {
        $produits = "";
        $produitsModifies = [];

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $produits = $repo->getProductsWithStock($search);
            foreach ($produits as $produit) {
                $produitDivalto = $repoStock->getStockjN($produit['ref'], $produit['sref1'], $produit['sref2']);
                if ($produitDivalto) {
                    $port = $produitDivalto['ratio_port'];
                }
                if ($produitDivalto) {
                    if ($produitDivalto['ppar'] && $produitDivalto['stock'] > 0) {
                        $pu = $produitDivalto['pu'] * $produitDivalto['ppar'];
                    } else {
                        $pu = $produitDivalto['pu'];
                    }
                    $price = $pu + $port;
                    $produit['ref'] = trim($produit['ref']);
                    $produit['sref1'] = trim($produit['sref1']);
                    $produit['sref2'] = trim($produit['sref2']);
                    $produit['pa'] = $price;
                    $produit['ppar'] = $produitDivalto['ppar'];
                }
                if ($produitDivalto && isset($produitDivalto['datePu'])) {
                    $produit['date'] = $produitDivalto['datePu'];
                } else {
                    // Si la clé 'datePu' n'existe pas ou si $produitDivalto est false
                    $produit['date'] = null; // ou une autre valeur par défaut
                }
                if ($produitDivalto) {
                    $produitsModifies[] = $produit;
                }
            }
        }

        return $this->render('last_purchase_price/index.html.twig', [
            'title' => 'Dernier PA',
            'produits' => $produitsModifies,
            'search' => $form->createView(),
        ]);
    }

}
