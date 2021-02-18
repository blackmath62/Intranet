<?php

namespace App\Controller;

use App\Entity\Divalto\Cli;
use App\Entity\Divalto\Vrp;
use App\Form\StatesDateFilterType;
use App\Repository\Divalto\EntRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatesRobyController extends AbstractController
{
    /**
     * @Route("/states/roby", name="app_states_roby")
     */
    public function index(Request $request, EntRepository $repo): Response
    {
        
        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);
        // initialisation de mes variables
        $minFadt = '';
        $maxFadt = '';
        $sumAnneeSelectionnee = 0;


        if($form->isSubmitted() && $form->isValid()){
         //$minFadt = new \DateTime('2021-01-01');
         //$maxFadt = new \DateTime('2021-01-31');
         //$commercial = $form->getdata()->getRepr0001();
        $minFadt = $form->getData()['startDate'];
        $maxFadt = $form->getData()['endDate'];
        }
 
        $entityManager = $this->getDoctrine()->getManager('divaltoreel');
        $statesRoby = $repo->getStatesEntRoby($entityManager, $minFadt, $maxFadt);

        for($i=0;$i<count($statesRoby);$i++){
            $nom = $this->getDoctrine()->getRepository(Cli::class, 'divaltoreel')->findBy(['tiers' => $statesRoby[$i]['tiers']]);
            $statesRoby[$i]['nom'] = $nom[0]->getNom();
            $commercial = $this->getDoctrine()->getRepository(Vrp::class, 'divaltoreel')->findOneBy(['tiers' => $nom[0]->getRepr0001()]);
            if(empty($commercial)){
                $statesRoby[$i]["commercial"] = "Pas de commercial assigné";
                }else{
                    $statesRoby[$i]['commercial'] = $commercial->getNom();            
                }
            $sumAnneeSelectionnee = $sumAnneeSelectionnee + $statesRoby[$i]['SumMontant'];
        }
        
        return $this->render('states_roby/index.html.twig', [
            'title' => 'States Roby',
            'statesRoby' => $statesRoby,
            'sumAnneeSelectionnee' => $sumAnneeSelectionnee,
             'DateFilterForm' => $form->createView()
        ]);
    }

}
