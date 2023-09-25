<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\DebRobyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ROBY")]

class DebRobyController extends AbstractController
{

    #[Route("/Roby/deb", name: "app_deb_roby")]

    public function getDebRobyByMonth(DebRobyRepository $repo, Request $request)
    {

        $form = $this->createForm(YearMonthType::class);
        $form->handleRequest($request);
        // initialisation de mes variables
        $annee = date("Y");
        $mois = date("m") - 1;

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $annee = $form->getData()['year'];
            $mois = $form->getData()['month'];
            $debs = $repo->getDebRobyByMonth($annee, $mois);
        } else {
            $debs = $repo->getDebRobyByMonth($annee, $mois);
        }

        return $this->render('deb_roby/index.html.twig', [
            'debs' => $debs,
            'title' => 'DEB Roby par mois',
            'monthYear' => $form->createView(),
        ]);

    }
}
