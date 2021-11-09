<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Divalto\RossignolRepository;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_LHERMITTE")
 */

class RossignolController extends AbstractController
{
    /**
     * @Route("/Lhermitte/rossignol/{annee}", name="app_lhermitte_rossignols")
     */
    public function index($annee = null, Request $request, RossignolRepository $repo): Response
    {

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
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
            'venteRossignols' => $venteRossignol
        ]);
    }
}
