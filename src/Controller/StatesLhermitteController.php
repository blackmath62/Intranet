<?php

namespace App\Controller;

use App\Entity\Divalto\Cli;
use App\Entity\Divalto\Vrp;
use App\Form\YearMonthType;
use App\Form\StatesDateFilterType;
use App\Repository\Divalto\MouvRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\StatesLhermitteByTiersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_LHERMITTE")
 */

class StatesLhermitteController extends AbstractController
{
    
    /**
     * @Route("/Lhermitte/states/EV", name="app_states_lhermitte_ev")
     */
    public function statesEv(StatesLhermitteByTiersRepository $repo, Request $request)
    {
           $secteur = 'EV';
           $themeColor = 'success';
           $commercial = 'Commercial';
           $view = $this->states($secteur,$themeColor,$repo,$request);         
           return $view;
    }
    /**
     * @Route("/Lhermitte/states/HP", name="app_states_lhermitte_hp")
     */
    public function statesHp(StatesLhermitteByTiersRepository $repo, Request $request)
    {
           $secteur = 'HP';
           $themeColor = 'danger';
           $commercial = 'Commercial';
           $view = $this->states($secteur,$themeColor,$repo,$request);         
           return $view;
    }
    /**
     * @Route("/Lhermitte/states/ME", name="app_states_lhermitte_me")
     */
    public function statesMe(StatesLhermitteByTiersRepository $repo, Request $request)
    {
           $secteur = 'ME';
           $themeColor = 'warning';
           $commercial = 'Commercial2';
           $view = $this->states($secteur,$themeColor,$repo,$request);         
           return $view;
    }
    /**
     * @Route("/Lhermitte/states/MA", name="app_states_lhermitte_ma")
     */
    public function statesMa(StatesLhermitteByTiersRepository $repo, Request $request)
    {
           $secteur = 'MA';
           $themeColor = 'orange';
           $view = $this->states($secteur,$themeColor,$repo,$request);         
           return $view;
    }
    
    
    public function states($secteur,$themeColor,$repo,$request)
    {
                    
            $secteurRecherche = $secteur;
            $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $mois ='';

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                $mois = $form->getData()['month'];

                $statesGlobal = $repo->getStatesLhermitteGlobalesByMonth($annee,$mois);
                // States Globales du secteur
                // Nbre de client du secteur N et N-1 ainsi que le Delta
                for ($ligClient=0; $ligClient <count($statesGlobal) ; $ligClient++) {
                    if ($statesGlobal[$ligClient]['SecteurMouvement'] == $secteurRecherche) {
                        $client[$ligClient]['annee'] = $statesGlobal[$ligClient]['Annee'];
                        $client[$ligClient]['tiers'] = $statesGlobal[$ligClient]['Tiers'];
                        $client[$ligClient]['commercial'] = $statesGlobal[$ligClient]['Commercial'];
                    } 
                }
                $nbreclient = array_values(array_unique($client, SORT_REGULAR));
                $state['count']['client']['anneeN'] = 0;
                $state['count']['client']['anneeN1'] = 0;
                for ($nbcli=0; $nbcli <count($nbreclient) ; $nbcli++) { 
                    if ($nbreclient[$nbcli]['annee'] == $annee) {
                        $state['count']['client']['anneeN']++;
                    }
                    if ($nbreclient[$nbcli]['annee'] == $annee -1) {
                        $state['count']['client']['anneeN1']++;
                    }
                }
                 // Delta client
                 $state['count']['client']['delta'] = $this->calcul_pourcentage($state['count']['client']['anneeN1'],$state['count']['client']['anneeN'])['pourc'];
                 $state['count']['client']['color'] = $this->calcul_pourcentage($state['count']['client']['anneeN1'],$state['count']['client']['anneeN'])['color'];
                 $state['count']['client']['fleche'] = $this->calcul_pourcentage($state['count']['client']['anneeN1'],$state['count']['client']['anneeN'])['fleche'];
                
                // Nbre de BL du secteur N et N-1 ainsi que le Delta
                for ($ligBl=0; $ligBl <count($statesGlobal) ; $ligBl++) {
                    if ($statesGlobal[$ligBl]['SecteurMouvement'] == $secteurRecherche) {
                        $bl[$ligBl]['annee'] = $statesGlobal[$ligBl]['Annee'];
                        $bl[$ligBl]['bl'] = $statesGlobal[$ligBl]['Bl'];
                    } 
                }
                $nbreBl = array_values(array_unique($bl, SORT_REGULAR));
                $state['count']['bl']['anneeN'] = 0;
                $state['count']['bl']['anneeN1'] = 0;
                for ($nbBl=0; $nbBl <count($nbreBl) ; $nbBl++) { 
                    if ($nbreBl[$nbBl]['annee'] == $annee) {
                        $state['count']['bl']['anneeN']++;
                    }
                    if ($nbreBl[$nbBl]['annee'] == $annee -1) {
                        $state['count']['bl']['anneeN1']++;
                    }
                }
                 // Delta BL
                 $state['count']['bl']['delta'] = $this->calcul_pourcentage($state['count']['bl']['anneeN1'],$state['count']['bl']['anneeN'])['pourc'];
                 $state['count']['bl']['color'] = $this->calcul_pourcentage($state['count']['bl']['anneeN1'],$state['count']['bl']['anneeN'])['color'];
                 $state['count']['bl']['fleche'] = $this->calcul_pourcentage($state['count']['bl']['anneeN1'],$state['count']['bl']['anneeN'])['fleche'];

                // Nbre de Facture du secteur N et N-1 ainsi que le Delta
                for ($ligFacture=0; $ligFacture <count($statesGlobal) ; $ligFacture++) {
                    if ($statesGlobal[$ligFacture]['SecteurMouvement'] == $secteurRecherche) {
                        $facture[$ligFacture]['annee'] = $statesGlobal[$ligFacture]['Annee'];
                        $facture[$ligFacture]['facture'] = $statesGlobal[$ligFacture]['Facture'];
                    } 
                }
                $nbreFact = array_values(array_unique($facture, SORT_REGULAR));
                $state['count']['facture']['anneeN'] = 0;
                $state['count']['facture']['anneeN1'] = 0;
                for ($facture=0; $facture <count($nbreFact) ; $facture++) { 
                    if ($nbreFact[$facture]['annee'] == $annee) {
                        $state['count']['facture']['anneeN']++;
                    }
                    if ($nbreFact[$facture]['annee'] == $annee -1) {
                        $state['count']['facture']['anneeN1']++;
                    }
                }
                 // Delta BL
                 $state['count']['facture']['delta'] = $this->calcul_pourcentage($state['count']['facture']['anneeN1'],$state['count']['facture']['anneeN'])['pourc'];
                 $state['count']['facture']['color'] = $this->calcul_pourcentage($state['count']['facture']['anneeN1'],$state['count']['facture']['anneeN'])['color'];
                 $state['count']['facture']['fleche'] = $this->calcul_pourcentage($state['count']['facture']['anneeN1'],$state['count']['facture']['anneeN'])['fleche'];


                // Sum du secteur Dépôt N et N-1 ainsi que le Delta
                // Sum du secteur Direct N et N-1 ainsi que le Delta
                // Sum Total du secteur N et N-1 ainsi que le Delta
                $state['depot']['montantN'] = 0;
                $state['depot']['montantN1'] = 0;
                $state['direct']['montantN'] = 0;
                $state['direct']['montantN1'] = 0;
                $state['secteur']['montantN'] = 0;
                $state['secteur']['montantN1'] = 0;

                for ($ligne=0; $ligne <count($statesGlobal) ; $ligne++) { 
                   if ($statesGlobal[$ligne]['Annee'] == $annee && $statesGlobal[$ligne]['SecteurMouvement'] == $secteurRecherche) 
                   {
                        if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                            $state['depot']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                        } 
                        if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                            $state['direct']['montantN'] += $statesGlobal[$ligne]['MontantSign'];
                        }
                        $state['secteur']['montantN'] += $statesGlobal[$ligne]['MontantSign'];  
                   }
                   
                   if ($statesGlobal[$ligne]['Annee'] == $annee -1 && $statesGlobal[$ligne]['SecteurMouvement'] == $secteurRecherche) 
                   {
                        if ($statesGlobal[$ligne]['OP'] == 'C' || $statesGlobal[$ligne]['OP'] == 'D') {
                            $state['depot']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                        } 
                        if ($statesGlobal[$ligne]['OP'] == 'CD' || $statesGlobal[$ligne]['OP'] == 'DD') {
                            $state['direct']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                        }
                        $state['secteur']['montantN1'] += $statesGlobal[$ligne]['MontantSign'];
                   }
                }
                
                // Delta Dépôt
                    $state['depot']['delta'] = $this->calcul_pourcentage($state['depot']['montantN1'],$state['depot']['montantN'])['pourc'];
                    $state['depot']['color'] = $this->calcul_pourcentage($state['depot']['montantN1'],$state['depot']['montantN'])['color'];
                    $state['depot']['fleche'] = $this->calcul_pourcentage($state['depot']['montantN1'],$state['depot']['montantN'])['fleche'];

                // Delta Direct
                    $state['direct']['delta'] = $this->calcul_pourcentage($state['direct']['montantN1'],$state['direct']['montantN'])['pourc'];
                    $state['direct']['color'] = $this->calcul_pourcentage($state['direct']['montantN1'],$state['direct']['montantN'])['color'];
                    $state['direct']['fleche'] = $this->calcul_pourcentage($state['direct']['montantN1'],$state['direct']['montantN'])['fleche'];
    
                // Delta Secteur
                    $state['secteur']['delta'] = $this->calcul_pourcentage($state['secteur']['montantN1'],$state['secteur']['montantN'])['pourc'];
                    $state['secteur']['color'] = $this->calcul_pourcentage($state['secteur']['montantN1'],$state['secteur']['montantN'])['color'];
                    $state['secteur']['fleche'] = $this->calcul_pourcentage($state['secteur']['montantN1'],$state['secteur']['montantN'])['fleche'];                             
                
                
                // States par commercial du secteur
                // Sum par commercial du secteur N et N-1 ainsi que le Delta
                for ($ligCom=0; $ligCom <count($statesGlobal) ; $ligCom++) {
                    if ($statesGlobal[$ligCom]['SecteurMouvement'] == $secteurRecherche) {
                        $commercial[$ligCom]['commercial'] = $statesGlobal[$ligCom]['Commercial'];
                    }
                }
                $listeCommercial = array_values(array_unique($commercial, SORT_REGULAR));
                // pour chaque commercial dans le tableau
                for ($tabCommercial=0; $tabCommercial <count($listeCommercial) ; $tabCommercial++) {
                    $ceCommercial = $listeCommercial[$tabCommercial]['commercial'];
                    
                    $state['commercial'][$ceCommercial] = array();
                    $state['commercial'][$ceCommercial]['nom'] = $ceCommercial;
                    $state['commercial'][$ceCommercial]['montantN'] = 0;
                    $state['commercial'][$ceCommercial]['montantN1'] = 0;
                    
                    // pour chaque ligne des states
                    for ($ligStatesGlobales=0; $ligStatesGlobales<count($statesGlobal) ; $ligStatesGlobales++){ 
                        if ($statesGlobal[$ligStatesGlobales]['Commercial'] == $ceCommercial) {
                            if ($statesGlobal[$ligStatesGlobales]['Annee'] == $annee) {
                                $state['commercial'][$ceCommercial]['montantN'] += $statesGlobal[$ligStatesGlobales]['MontantSign'];
                            }
                            if ($statesGlobal[$ligStatesGlobales]['Annee'] == $annee -1) {
                                $state['commercial'][$ceCommercial]['montantN1'] += $statesGlobal[$ligStatesGlobales]['MontantSign'];
                            }
                        }
                    }
                    $state['commercial'][$ceCommercial]['deltaTotalN'] = ($state['commercial'][$ceCommercial]['montantN']*100)/$state['secteur']['montantN'];
                    $state['commercial'][$ceCommercial]['deltaTotalN1'] = ($state['commercial'][$ceCommercial]['montantN1']*100)/$state['secteur']['montantN1'];
                    $state['commercial'][$ceCommercial]['deltaMontant'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['montantN1'],$state['commercial'][$ceCommercial]['montantN'])['pourc'];
                    $state['commercial'][$ceCommercial]['colorMontant'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['montantN1'],$state['commercial'][$ceCommercial]['montantN'])['color'];
                    $state['commercial'][$ceCommercial]['flecheMontant'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['montantN1'],$state['commercial'][$ceCommercial]['montantN'])['fleche']; 
                }
                // Nbre de client par commercial du secteur N et N-1 ainsi que le Delta

                for ($tabCommercial=0; $tabCommercial <count($listeCommercial) ; $tabCommercial++) {
                    $ceCommercial = $listeCommercial[$tabCommercial]['commercial'];
                    
                    $state['commercial'][$ceCommercial]['clientN'] = 0;
                    $state['commercial'][$ceCommercial]['clientN1'] = 0;

                    // pour chaque ligne des states
                    for ($ligListeClient=0; $ligListeClient<count($nbreclient) ; $ligListeClient++){ 
                        if ($nbreclient[$ligListeClient]['commercial'] == $ceCommercial) {
                            if ($nbreclient[$ligListeClient]['annee'] == $annee) {
                                $state['commercial'][$ceCommercial]['clientN']++;
                            }
                            if ($nbreclient[$ligListeClient]['annee'] == $annee -1) {
                                $state['commercial'][$ceCommercial]['clientN1']++;
                            }
                        }
                    }
                    $state['commercial'][$ceCommercial]['deltaTotalClientN'] = ($state['commercial'][$ceCommercial]['clientN']*100)/$state['count']['client']['anneeN'];
                    $state['commercial'][$ceCommercial]['deltaTotalClientN1'] = ($state['commercial'][$ceCommercial]['clientN1']*100)/$state['count']['client']['anneeN1'];
                    $state['commercial'][$ceCommercial]['deltaClient'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['clientN1'],$state['commercial'][$ceCommercial]['clientN'])['pourc'];
                    $state['commercial'][$ceCommercial]['colorClient'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['clientN1'],$state['commercial'][$ceCommercial]['clientN'])['color'];
                    $state['commercial'][$ceCommercial]['flecheClient'] = $this->calcul_pourcentage($state['commercial'][$ceCommercial]['clientN1'],$state['commercial'][$ceCommercial]['clientN'])['fleche']; 
                }
               
                


                // Sum par client du Secteur N et N-1 ainsi que le Delta
                for ($ListeClients=0; $ListeClients <count($statesGlobal) ; $ListeClients++) {
                    if ($statesGlobal[$ListeClients]['SecteurMouvement'] == $secteurRecherche) {
                        $clientFilter[$ListeClients]['commercial'] = $statesGlobal[$ListeClients]['Commercial'];
                        $clientFilter[$ListeClients]['tiers'] = $statesGlobal[$ListeClients]['Tiers'];
                        $clientFilter[$ListeClients]['nom'] = $statesGlobal[$ListeClients]['Nom'];
                        $clientFilter[$ListeClients]['montantN'] = 0;
                        $clientFilter[$ListeClients]['montantN1'] = 0;
                    }
                }
                $clientFilter = array_values(array_unique($clientFilter, SORT_REGULAR));
                // pour chaque client
                for ($ligClient=0; $ligClient <count($clientFilter) ; $ligClient++) { 
                    
                    for ($statesClients=0; $statesClients<count($statesGlobal) ; $statesClients++) { 
                        if ($statesGlobal[$statesClients]['Annee'] == $annee -1 && $statesGlobal[$statesClients]['SecteurMouvement'] == $secteurRecherche && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                            $clientFilter[$ligClient]['montantN1'] += $statesGlobal[$statesClients]['MontantSign'];
                        }
                        if ($statesGlobal[$statesClients]['Annee'] == $annee && $statesGlobal[$statesClients]['SecteurMouvement'] == $secteurRecherche && $statesGlobal[$statesClients]['Tiers'] == $clientFilter[$ligClient]['tiers']) {
                            $clientFilter[$ligClient]['montantN'] += $statesGlobal[$statesClients]['MontantSign'];
                        }
                    }
                    $clientFilter[$ligClient]['delta'] = $this->calcul_pourcentage($clientFilter[$ligClient]['montantN1'] ,$clientFilter[$ligClient]['montantN'] )['pourc'];
                    $clientFilter[$ligClient]['color'] = $this->calcul_pourcentage($clientFilter[$ligClient]['montantN1'] ,$clientFilter[$ligClient]['montantN'] )['color'];
                    $clientFilter[$ligClient]['fleche'] = $this->calcul_pourcentage($clientFilter[$ligClient]['montantN1'] ,$clientFilter[$ligClient]['montantN'] )['fleche'];
                }
               
                // States par article page séparé
                // Liste des articles par client du secteur N et N-1 ainsi que le Delta
                return $this->render('states_lhermitte/index.html.twig', [
                    'title' => 'States Lhermitte',
                    'mois' => $mois,
                    'annee' => $annee,
                    'state' => $state,
                    'clients' => $clientFilter,
                    'monthYear' => $form->createView()
                    ]);

            }

                return $this->render('states_lhermitte/index.html.twig', [
                'title' => 'States Lhermitte',
                'mois' => $mois,
                'annee' => $annee,
                'state' => '',
                'clients' => '',
                'monthYear' => $form->createView()
                ]);

        }

        function calcul_pourcentage($nombreN1,$nombreN)
        { 
            if ($nombreN1 <> 0 && $nombreN <> 0) {
                $resultat['pourc'] = (($nombreN/$nombreN1) -1)*100;
                if ($resultat['pourc'] < 0) {
                    $resultat['color'] = 'danger';
                    $resultat['fleche'] = 'down';
                }
                if ($resultat['pourc'] > 0) {
                    $resultat['color'] = 'success';
                    $resultat['fleche'] = 'up';
                }
                if ($nombreN1 == $nombreN) {
                    $resultat['pourc'] = 0;
                    $resultat['color'] = 'warning';
                    $resultat['fleche'] = 'left';
                }
            }
            else{
                    $resultat['pourc'] = 0;
                    $resultat['color'] = 'warning';
                    $resultat['fleche'] = 'left';
            }
            if (($nombreN1 == 0 && $nombreN > 0) || ($nombreN1 < 0 && $nombreN == 0)) {
                    $resultat['pourc'] = 100;
                    $resultat['color'] = 'success';
                    $resultat['fleche'] = 'up';
            }
            if (($nombreN1 == 0 && $nombreN < 0) || ($nombreN1 > 0 && $nombreN == 0)) {
                    $resultat['pourc'] = -100;
                    $resultat['color'] = 'danger';
                    $resultat['fleche'] = 'down';
            }
                return $resultat; 
        } 



    /**
     * @Route("/Lhermitte/states/EV/DetailArticle/{tiers}/{annee}/{mois}", name="app_states_lhermitte_ev_par_article")
     */
    public function statesByArticleEv(StatesLhermitteByTiersRepository $repo, Request $request): Response
    {
        $annee = $request->get('annee');
        $mois = $request->get('mois');
        $tiers = $request->get('tiers');
        $sectArt1 = 'EV';
        $sectArt2 = 'HP';
        $sectCli1 = 'EV';
        $sectCli2 = 'EV';
        
        //$annee = '$form->getData()['year']';
        //$mois = '$form->getData()['month']';
        $statesByClientEv = $repo->getStatesLhermitteByArticles($annee, $mois, $tiers,$sectArt1, $sectArt2,$sectCli1,$sectCli2);
        $nom = $statesByClientEv[0]['nom']; 
        // on retire les doublons dans les clients
         for($i=0;$i<count($statesByClientEv);$i++){
             $tableau = $statesByClientEv[$i];    
             $tab[$i] = $tableau['des'];
            }
            $tab = array_values(array_unique($tab)); // rendre une liste de tiers Unique
            // boucler pour leurs assigner le nom, commercial, pays, Devise les montants des 2 années, on reconstruit le tableau
            
            for($j=0;$j<count($tab);$j++){
                $result[$j]['des'] = $tab[$j];
                $key = array_search($tab[$j], array_column($statesByClientEv, 'des'));
                dd(array_column($statesByClientEv, 'des'));
                
                $result[$j]['ref'] = $statesByClientEv[$key]['ref'];
                $result[$j]['sref1'] = $statesByClientEv[$key]['sref1'];
                $result[$j]['sref2'] = $statesByClientEv[$key]['sref2'];
                $result[$j]['uv'] = $statesByClientEv[$key]['uv'];
                $result[$j]['Delta'] = '';
                $result[$j]['AnneeN'] ='';
                $result[$j]['AnneeN1'] = '';
                for ($p=0; $p <count($statesByClientEv) ; $p++) { 
                    if ($statesByClientEv[$p]['des'] == $tab[$j] && $statesByClientEv[$p]['Annee'] == $annee ) {
                        $result[$j]['AnneeN'] = $statesByClientEv[$p]['MontantSign']; // Année demandée
                    }                            
                    if ($statesByClientEv[$p]['des'] == $tab[$j] && $statesByClientEv[$p]['Annee'] == $annee -1) {
                        $result[$j]['AnneeN1'] = $statesByClientEv[$p]['MontantSign']; // Année N-1
                    }
                    if ($result[$j]['AnneeN'] <> 0 && $result[$j]['AnneeN1'] <> 0) {
                        $result[$j]['Delta'] = (($result[$j]['AnneeN']/$result[$j]['AnneeN1']-1)*100);
                    }
                }

        }

        return $this->render('states_lhermitte/statesByArticleEv.html.twig', [
            'title' => 'Ev par Articles',
            'states' => $result,
            'annee' => $annee,
            'mois' => $mois,
            'nom' => $nom
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
