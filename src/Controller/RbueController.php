<?php

namespace App\Controller;

use App\Form\StatesDateFilterType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RbueController extends AbstractController
{
    #[Route("/rbue", name: "app_rbue")]

    public function index(MouvRepository $repo, Request $request): Response
    {

        $mouv = '';
        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dd = $form->getData()['startDate']->format('Y-m-d');
            $df = $form->getData()['endDate']->format('Y-m-d');

            $mouv = $repo->getMouvRbue($dd, $df);
        }

        return $this->render('rbue/index.html.twig', [
            'form' => $form->createView(),
            'title' => 'Mouv Rbue',
            'mouvs' => $mouv,
        ]);
    }
}
