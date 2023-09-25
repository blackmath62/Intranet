<?php

namespace App\Controller;

use App\Repository\Divalto\RossignolRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_LHERMITTE")]

class RossignolController extends AbstractController
{
    #[Route("/Lhermitte/rossignol/{annee}", name: "app_lhermitte_rossignols")]

    public function index(RossignolRepository $repo, $annee = null): Response
    {

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);
        if (!$annee) {
            $annee = new DateTime();
            $annee = $annee->format('Y');
        }

        // édition des stocks des produits rossignols
        $stockRossignol = $repo->getRossignolStockList();
        $venteRossignol = $repo->getRossignolVenteList($annee);

        return $this->render('rossignol/index.html.twig', [
            'title' => 'Rossignol',
            'stockRossignols' => $stockRossignol,
            'venteRossignols' => $venteRossignol,
        ]);
    }
}
