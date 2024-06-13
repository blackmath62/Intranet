<?php

namespace App\Controller;

use App\Form\PiecesByCommercialByPeriodsType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class PiecesByCommercialByPeriodsController extends AbstractController
{
    #[Route('/pieces/by/commercial/by/periods', name: 'app_pieces_by_commercial_by_periods')]
    public function index(Request $request, MouvRepository $repoMouv): Response
    {
        $donnees = '';
        $periode = '';
        $title = 'Pieces par Com et Périodes';
        // form pour selectionner le type de piéce, les commerciaux, dd et df
        $form = $this->createForm(PiecesByCommercialByPeriodsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $periode = [$form->getData()['start']->format('d-m-Y'), $form->getData()['end']->format('d-m-Y')];
            $donnees = $repoMouv->getPiecesByCommercialByPeriods(
                $form->getData()['pieces'],
                implode(', ', $form->getData()['commerciaux']),
                $form->getData()['start']->format('Y-m-d'),
                $form->getData()['end']->format('Y-m-d')
            );
            $title = 'Pieces par Com et Périodes P' . $form->getData()['pieces'] . ' COM ' . implode(', ', $form->getData()['commerciaux']) . ' du ' . $form->getData()['start']->format('Y-m-d') . ' au ' . $form->getData()['end']->format('Y-m-d');
        }

        return $this->render('pieces_by_commercial_by_periods/index.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'donnees' => $donnees,
            'periode' => $periode,
        ]);
    }
}
