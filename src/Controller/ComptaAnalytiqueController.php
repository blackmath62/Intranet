<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\ComptaAnalytiqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_COMPTA")
*/

class ComptaAnalytiqueController extends AbstractController
{
    /**
     * @Route("/compta_analytique", name="app_compta_analytique")
     */
    public function index(Request $request, ComptaAnalytiqueRepository $repo): Response
    {
        $form = $this->createForm(YearMonthType::class);
                    $form->handleRequest($request);
                    $comptaAnalytiques = array();
                    $compteAchat = array();
                    // tracking user page for stats
                    $tracking = $request->attributes->get('_route');
                    $this->setTracking($tracking);
                    // Initialisation des variables
                    // nombre de ligne HP -> EV
                    $CompteurHP = 0;
                    // Montant HP -> EV
                    $MontantHP = 0;
                    // nombre de ligne EV -> HP
                    $CompteurEV = 0;
                    // Montant EV -> HP
                    $MontantEV = 0;
                    // nombre de ligne Cr = 0
                    $CompteurCrNul = 0;
                    $CompteTest = array();
                    $HP = array();
                    $HP['compteur'] = 0;
                    $EV = array();

                    if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        // On interroge la requête
                        $comptaAnalytiques = $repo->getComptaAnalytiqueByMonth($annee,$mois);
                        // On isole les comptes Achats
                        for ($ligCompteAchat=0; $ligCompteAchat <count($comptaAnalytiques) ; $ligCompteAchat++) { 
                            $compteAchat[] = $comptaAnalytiques[$ligCompteAchat]['CompteAchat'];
                        }
                        $compteAchat = array_values(array_unique($compteAchat, SORT_REGULAR));
                        for ($ligCompte=0; $ligCompte <count($compteAchat) ; $ligCompte++) { 
                            for ($ligCompta=0; $ligCompta <count($comptaAnalytiques) ; $ligCompta++) {
                                if ($comptaAnalytiques[$ligCompta]['Client']  == 'HP' && $comptaAnalytiques[$ligCompta]['Article']  == 'EV' ) {
                                    $CompteurHP++;
                                    $HP['compteur'] == $CompteurHP;
                                    
                                    // Signature du montant
                                    if ($comptaAnalytiques[$ligCompta]['Op'] == 'DD' || $comptaAnalytiques[$ligCompta]['Op'] == 'D') {
                                        $MontantHP = $MontantHP + ($comptaAnalytiques[$ligCompta]['CrTotal'])*(-1);
                                    }else{    
                                        $MontantHP = $MontantHP + $comptaAnalytiques[$ligCompta]['CrTotal'];
                                    }
                                }
                                if ($comptaAnalytiques[$ligCompta]['Client']  == 'EV' && $comptaAnalytiques[$ligCompta]['Article']  == 'HP' ) {
                                    $CompteurEV++;
                                    // Signature du montant
                                    if ($comptaAnalytiques[$ligCompta]['Op'] == 'DD' || $comptaAnalytiques[$ligCompta]['Op'] == 'D') {
                                        $MontantEV = $MontantEV + ($comptaAnalytiques[$ligCompta]['CrTotal'])*(-1);
                                    }else{    
                                        $MontantEV = $MontantEV + $comptaAnalytiques[$ligCompta]['CrTotal'];
                                    }
                                }
    
                            }
                        }
                        //dd($HP);
                        

                    }
                
                    return $this->render('compta_analytique/index.html.twig', [
                        'comptaAnalytiques' => $comptaAnalytiques,
                        'title' => 'Compta Analytique par mois',
                        'compteAchats' => $compteAchat, 
                        'monthYear' => $form->createView()
                        ]);

    }
}
