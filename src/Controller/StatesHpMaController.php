<?php

namespace App\Controller;

use App\Form\YearType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatesHpMaController extends AbstractController
{
    #[Route('/states/hp/ma', name: 'app_states_hp_ma')]
    public function index(Request $request): Response
    {
        $title = 'States HP MA';

        $form = $this->createForm(YearType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $year = $form->getData()['year']->format('Y-m-d');
            $title = 'States HP MA ' . $year;
        }

        return $this->render('states_hp_ma/index.html.twig', [
            'title' => $title,
        ]);
    }
}
