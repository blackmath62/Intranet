<?php

namespace App\Controller;

use DateTime;
use App\Form\LoadType;
use App\Entity\Main\CopyArt;
use App\Entity\Main\CopyFou;
use App\Form\DateDebutFinType;
use App\Entity\Main\Decisionnel;
use App\Form\NomDecisionnelType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\FouRepository;
use App\Repository\Main\CopyArtRepository;
use App\Repository\Main\CopyFouRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Main\DecisionnelRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\Divalto\InterrogationStockFAQteArticleRepository;

class DecisionnelController extends AbstractController
{
    /**
     * @Route("/decisionnel", name="app_decisionnel")
     */
    public function index(DecisionnelRepository $repo,FouRepository $repoFou, CopyFouRepository $repoCopyFou,ArtRepository $repoArt, CopyArtRepository $repoCopyArt, Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        $listeDecisionnel = $repo->findAll();
        
        $addFou = new CopyFou();
        $formAddNewFou = $this->createFormBuilder($addFou)
                        ->add("majFou", SubmitType::class, [ 'label' => 'Mise à jour des fournisseurs', 'attr' => [ 'class' => 'btn btn-primary mb-2'] ])
                        ->getForm();;
        $formAddNewFou->handleRequest($request);
        if ($formAddNewFou->isSubmitted() && $formAddNewFou->isValid()) {
            $em = $this->getDoctrine()->getManager();    
            // ajouter les fournisseurs inexistants
            $CopyFou = $repoCopyFou->findAll();
            $fournisseurs = "";
            for ($ligCopyFou=0; $ligCopyFou <count($CopyFou) ; $ligCopyFou++) { 
                if ($ligCopyFou == 0) {
                    $fournisseurs = '\'' .$CopyFou[$ligCopyFou]->getTiers(). '\'';
                }else {
                    $fournisseurs = $fournisseurs . ',' . '\'' . $CopyFou[$ligCopyFou]->getTiers(). '\'';
                }
            }
            $fou = $repoFou->getAddCopyFou($fournisseurs);

            // Création des fournisseurs inexistant dans ma table de copyFou
            for ($ligFou=0; $ligFou <count($fou) ; $ligFou++){ 

                        $addFou->setDos($fou[$ligFou]['DOS'])
                               ->setTiers($fou[$ligFou]['TIERS'])
                               ->setNom($fou[$ligFou]['NOM']);
                        $ClosedAtDate = null;
                        if ($fou[$ligFou]['HSDT'] != NULL) {
                            $ClosedAtDate = new DateTime($fou[$ligFou]['HSDT'] = null);
                            $ClosedAtDate->format('Y-m-d');
                        }
                        $addFou->setClosedAt($ClosedAtDate);
                        $em->persist($addFou);
                        $em->flush();
                        $em->clear();
            }      

            // Mettre à jour les fournisseurs existants

            for ($ligCopyFou=0; $ligCopyFou <count($CopyFou) ; $ligCopyFou++) {
                $fournisseurDb = '\'' . $CopyFou[$ligCopyFou]->getTiers() . '\'';
                $fournisseur = $CopyFou[$ligCopyFou]->getTiers();
                $updateFou = $repoFou->getUpdateCopyFou($fournisseurDb);
                $update = $repoCopyFou->findOneBy(array('tiers' => $fournisseur)) ; 
                $update->setNom($updateFou[0]['NOM'])
                        ->setUpdatedAt(new DateTime());
                $ClosedAtDate = null;
                if ($updateFou[0]['HSDT'] != NULL) {
                    $ClosedAtDate = new DateTime($updateFou[0]['HSDT'] = null);
                    $ClosedAtDate->format('Y-m-d');
                }
                $update->setClosedAt($ClosedAtDate);
                $em->persist($update);
                $em->flush();
                $em->clear();

            }
            
            $this->addFlash('message', 'La liste des fournisseurs a été mise à jour !');
            return $this->redirectToRoute('app_decisionnel');
        }

        // Listes des articles
        /*
        $addArt = new CopyArt();
        $formAddNewArt = $this->createFormBuilder($addArt)
                        ->add("majArt", SubmitType::class, [ 'label' => 'Mise à jour des articles', 'attr' => [ 'class' => 'btn btn-warning mb-2'] ])
                        ->getForm();;
        $formAddNewArt->handleRequest($request);
        if ($formAddNewArt->isSubmitted() && $formAddNewArt->isValid()) {
            $em = $this->getDoctrine()->getManager();    
            // ajouter les articles inexistants
            $CopyArt = $repoCopyArt->findAll();
            $articles = "";
            for ($ligCopyArt=0; $ligCopyArt <count($CopyArt) ; $ligCopyArt++) { 
                if ($ligCopyArt == 0) {
                    $articles = '\'' .$CopyArt[$ligCopyArt]->getRef(). '\'';
                }else {
                    $articles = $articles . ',' . '\'' . $CopyArt[$ligCopyArt]->getRef(). '\'';
                }
            }
            $art = $repoArt->getAddCopyArt($articles);

            // Création des articles inexistant dans ma table de copyArt
            for ($ligArt=0; $ligArt <count($art) ; $ligArt++){ 

                        $addArt->setDos($art[$ligArt]['DOS'])
                               ->setRef($art[$ligArt]['REF'])
                               ->setDes($art[$ligArt]['DES'])
                               ->setVenun($art[$ligArt]['VENUN'])
                               ->setMetier($art[$ligArt]['FAM_0002'])
                               ->setUpdatedAt(new DateTime());
                        //$ClosedAtDate = '';
                        //dd($art[$ligArt]['HSDT'] != NULL);
                        if ($art[$ligArt]['HSDT'] != NULL) {
                            $ClosedAtDate = date_create($art[$ligArt]['HSDT']);
                            //$ClosedAtDate->format('Y-m-d');
                            $addArt->setClosedAt($ClosedAtDate);
                        }
                        $em->persist($addArt);
                        $em->flush();
                        $em->clear();
            }      

            // Mettre à jour les articles existants

            /*for ($ligCopyArt=0; $ligCopyArt <count($CopyArt) ; $ligCopyArt++) {
                $articleDb = '\'' . $CopyArt[$ligCopyArt]->getRef() . '\'';
                $article = $CopyArt[$ligCopyArt]->getRef();
                $updateArt = $repoArt->getUpdateCopyArt($articleDb);
                $update = $repoCopyArt->findOneBy(array('ref' => $article)) ; 
                $update->setDes($updateArt[0]['DES'])
                        ->setMetier($art[$ligArt]['FAM_0002'])
                        ->setUpdatedAt(new DateTime());
                $ClosedAtDate = NULL;
                if ($updateArt[0]['HSDT'] != NULL) {
                    $ClosedAtDate = new DateTime($updateArt[0]['HSDT'] = null);
                    $ClosedAtDate->format('Y-m-d');
                }
                $update->setClosedAt($ClosedAtDate);
                $em->persist($update);
                $em->flush();
                $em->clear();

            }*/
            
        /*    $this->addFlash('message', 'La liste des articles a été mise à jour !');
            return $this->redirectToRoute('app_decisionnel');
        }
        */



        // Ajouter des fournisseurs à ma liste
        $newDecisionnel = new Decisionnel();
        $form = $this->createForm(NomDecisionnelType::class, $newDecisionnel);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $newDecisionnel = $form->getData();
            $newDecisionnel->setCreatedAt(new DateTime())
                           ->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($newDecisionnel);
            $em->flush();
            $this->addFlash('message', 'Décisionnel créé avec succés !');
            return $this->redirectToRoute('app_decisionnel');
        }      

        return $this->render('decisionnel/index.html.twig', [
            'controller_name' => 'DecisionnelController',
            'title' => 'Décisionnel',
            'listeDecisionnel' => $listeDecisionnel,
            'formAddNewFou' => $formAddNewFou->createView(),
            //'formAddNewArt' => $formAddNewArt->createView(),
            'CreerDecisionnel' => $form->createView()
        ]);
    }
    /**
     * @Route("/decisionnel/view/{id}", name="app_view_decision")
     */
    public function view($id, Request $request, InterrogationStockFAQteArticleRepository $repo, StatesController $statesController, DecisionnelRepository $repoDecisionnel)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $DonneesDecisionnel = $repoDecisionnel->findOneBy(array('id' => $id));
        $statesArticles = '';
        $form = $this->createForm(DateDebutFinType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $dateDebutN = $statesController->dateParameter($form->getData()['dates'])['dateDebutN'];
            $dateFinN = $statesController->dateParameter($form->getData()['dates'])['dateFinN'];

            // il faut que la requête tienne compte des articles sélectionnés
            $statesArticles = $repo->getInterrogationStockFAQteArticle($dateDebutN, $dateFinN);
        }

        return $this->render('decisionnel/view.html.twig',[
            'title' => 'Decisionnel',
            'statesArticles' => $statesArticles,
            'DonneesDecisionnel' => $DonneesDecisionnel,
            'DateDebutFinForm' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/decisionnel/edit/{id}", name="app_edit_decision")
     */
    public function edit($id, Request $request, DecisionnelRepository $repoDecisionnel)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $DonneesDecisionnel = $repoDecisionnel->findOneBy(array('id' => $id));
        $form = $this->createForm(NomDecisionnelType::class, $DonneesDecisionnel);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $DonneesDecisionnel = $form->getData();
            $DonneesDecisionnel->setCreatedAt(new DateTime())
                               ->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($DonneesDecisionnel);
            $em->flush();
            $this->addFlash('message', 'Décisionnel modifié avec succés !');
            return $this->redirectToRoute('app_edit_decision', ['id' => $id ]);
        }

        $formAddNewArt = $this->createFormBuilder($addArt)
                        ->add("addArt", SubmitType::class, [ 'label' => 'Ajouter des articles', 'attr' => [ 'class' => 'btn btn-warning mb-2'] ])
                        ->getForm();;
        $formAddNewArt->handleRequest($request);
        if ($formAddNewArt->isSubmitted() && $formAddNewArt->isValid()) {
        
        }

        return $this->render('decisionnel/edit.html.twig',[
            'title' => 'Modification',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/decisionnel/addProduct/{id}", name="app_add_product_decision")
     */
    public function addProduct($id, Request $request, DecisionnelRepository $repoDecisionnel)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $DonneesDecisionnel = $repoDecisionnel->findOneBy(array('id' => $id));
        $form = $this->createForm(NomDecisionnelType::class, $DonneesDecisionnel);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $DonneesDecisionnel = $form->getData();
            $DonneesDecisionnel->setCreatedAt(new DateTime())
                               ->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($DonneesDecisionnel);
            $em->flush();
            $this->addFlash('message', 'Ajout des articles éffectué avec succés !');
            return $this->redirectToRoute('app_add_product_decision', ['id' => $id ]);
        }

        return $this->render('decisionnel/addProduct.html.twig',[
            'title' => 'Ajout produit',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/decisionnel/delete/{id}", name="app_delete_decision")
     */
    public function delete(Decisionnel $decisionnel, Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $em = $this->getDoctrine()->getManager();
                $em->remove($decisionnel);
                $em->flush();
        $this->addFlash('message', 'Décisionnel Supprimé avec succès');
        return $this->redirectToRoute('app_decisionnel');
    }

}
