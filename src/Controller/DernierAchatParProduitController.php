<?php

namespace App\Controller;

use App\Form\MetierProdType;
use App\Repository\Divalto\ArtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DernierAchatParProduitController extends AbstractController
{
    /**
     * @Route("/dernier/achat/par/produit/{dos}", name="app_dernier_achat_par_produit")
     */
    public function index($dos, ArtRepository $repo, Request $request): Response
    {
        $produits = '';

        $form = $this->createForm(MetierProdType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produits = $repo->getAchatParProduit($dos, $form->getData()['produits'], $form->getData()['metiers']);
        }

        return $this->render('dernier_achat_par_produit/index.html.twig', [
            'produits' => $produits,
            'title' => 'Achat produit',
            'form' => $form->createView(),
        ]);
    }
}
