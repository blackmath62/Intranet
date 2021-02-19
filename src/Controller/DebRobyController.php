<?php

namespace App\Controller;

use App\Form\YearMonthType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Divalto\DebRobyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ROBY")
 */

class DebRobyController extends AbstractController
{   
               
                /**
                 * @Route("/Roby/deb", name="app_deb_roby")
                 */
                public function getDebRobyByMonth(DebRobyRepository $repo, Request $request){
                    
                    $form = $this->createForm(YearMonthType::class);
                    $form->handleRequest($request);
                    // initialisation de mes variables
                    $annee = date("Y");
                    $mois = date("m")-1;

                    if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        $debs = $repo->getDebRobyByMonth($annee,$mois);
                    }else{
                        $debs = $repo->getDebRobyByMonth($annee,$mois);
                    }
                
                    return $this->render('deb_roby/index.html.twig', [
                        'debs' => $debs,
                        'title' => 'DEB Roby par mois',
                        'monthYear' => $form->createView()
                        ]);

                }
            }
