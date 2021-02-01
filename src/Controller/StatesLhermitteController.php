<?php

namespace App\Controller;

use App\Entity\Divalto\Ent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatesLhermitteController extends AbstractController
{
    /**
     * @Route("/Lhermitte/states/EV", name="app_states_lhermitte_ev")
     */
    public function statesEv(): Response
    {
        $statesEv = $this->getDoctrine()->getRepository(Ent::class, 'divaltoreel')->findBy(['picod' => 4, 'dos' => 1, 'repr0001' => 10]);

        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Ev',
            'ents' => $statesEv
        ]);
    }
    /**
     * @Route("/Lhermitte/states/HP", name="app_states_lhermitte_hp")
     */
    public function statesHp(): Response
    {
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Hp'
        ]);
    }
    /**
     * @Route("/Lhermitte/states/ME", name="app_states_lhermitte_me")
     */
    public function statesMe(): Response
    {
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Me'
        ]);
    }
    /**
     * @Route("/Lhermitte/states/MA", name="app_states_lhermitte_ma")
     */
    public function statesMa(): Response
    {
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Ma'
        ]);
    }
    /**
     * @Route("/Lhermitte/states/LH", name="app_states_lhermitte_lh")
     */
    public function statesLh(): Response
    {
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Lh'
        ]);
    }
}
