<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\RpdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_INFORMATIQUE")]

class DeclarationCeppController extends AbstractController
{
    #[Route('/declaration/cepp', name: 'app_declaration_cepp')]
    public function index(Request $request, RpdRepository $repo): Response
    {
        $produits = "";
        $form = $this->createForm(YearMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annee = $form->getData()['year'];
            $produits = $repo->getDeclarationCepp($annee);
        }

        return $this->render('declaration_cepp/index.html.twig', [
            'produits' => $produits,
            'monthYear' => $form->createView(),
            'title' => "Fiches actions CEPP",
        ]);
    }
}
