<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchTiersController extends AbstractController
{
    /**
     * @Route("/search/tiers", name="search_tiers")
     */
    public function index(): Response
    {
        return $this->render('search_tiers/index.html.twig', [
            'controller_name' => 'SearchTiersController',
        ]);
    }
}
