<?php

namespace App\Controller;

use App\Entity\Divalto\Cli;
use App\Entity\Divalto\Vrp;
use App\Form\StatesDateFilterType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatesLhermitteController extends AbstractController
{
    /**
     * @Route("/Lhermitte/states/EV", name="app_states_lhermitte_ev")
     */
    public function statesEv(MouvRepository $repo, Request $request): Response
    {
        // DIVALTO cette requête fonctionne correctement
        $minFadt = '';
        $maxFadt = '';

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()){
        //$minFadt = new \DateTime('2021-01-01');
        //$maxFadt = new \DateTime('2021-01-31');
        //$commercial = $form->getdata()->getRepr0001();
        $minFadt = $form->getData()['startDate'];
        $maxFadt = $form->getData()['endDate'];
        }

        $entityManager = $this->getDoctrine()->getManager('divaltoreel');
        $statesEv = $repo->getStatesMouvEv($entityManager, $minFadt, $maxFadt);
        for($i=0;$i<count($statesEv);$i++){
          $nom = $this->getDoctrine()->getRepository(Cli::class, 'divaltoreel')->findBy(['tiers' => $statesEv[$i]['tiers']]);
          $statesEv[$i]['nom'] = $nom[0]->getNom();
          $commercial = $this->getDoctrine()->getRepository(Vrp::class, 'divaltoreel')->findOneBy(['tiers' => $nom[0]->getRepr0001()]);
          if(empty($commercial)){
              $stateEv[$i]["commercial"] = "Pas de commercial assigné";
            }else{
                $statesEv[$i]['commercial'] = $commercial->getNom();            
            }
        }
        return $this->render('states_lhermitte/index.html.twig', [
            'controller_name' => 'StatesLhermitteController',
            'title' => 'States Ev',
            'statesEv' => $statesEv,
            'DateFilterForm' => $form->createView()
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
