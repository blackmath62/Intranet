<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\Divalto\ArtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class SrefByArtController extends AbstractController
{
    #[Route("/sref/by/art", name: "app_sref_by_art")]

    public function index(Request $request, ArtRepository $repo): Response
    {
        $dos = 1;
        $articles = "";

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $articles = $repo->getSrefByArt($dos, $search);
        }

        return $this->render('sref_by_art/index.html.twig', [
            'articles' => $articles,
            'title' => 'Srefs Ouvertes sur Articles',
            'search' => $form->createView(),
        ]);
    }
}
