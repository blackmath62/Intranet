<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Entity\Annuaire;
use App\Form\AdminAnnuaireType;
use App\Repository\SocieteRepository;
use App\Repository\AnnuaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class AdminAnnuaireController extends AbstractController
{
    /**
     * Création d'une nouvelle entrée dans l'annuaire et affichage de l'annuaire avec option modification et suppression
     * 
     * @Route("/admin/annuaire", name="app_admin_annuaire")
     */
    public function index(Request $request, AnnuaireRepository $repo, SocieteRepository $repoSociete, EntityManagerInterface $manager)
    {
        
        $societes = $repoSociete->findAll();
        $newAnnuaire = new Annuaire();
        $formAddAnnuaire = $this->createFormBuilder($newAnnuaire)
            ->add("interne")
            ->add("nom", TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un nom'
                    ])
                    ],
                    'required' => true
            ])
            ->add("exterieur")
            ->add("mail", EmailType::class,['required'   => false])
            ->add("fonction")
            ->add("portable")
            ->add("Societe", EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'nom',
                'choice_name' => 'id'
            ])
            ->getForm();
        $formAddAnnuaire->handleRequest($request);
        // Si le formulaire a été soumis et est valide, on créé le nouveau contact dans l'annuaire
        if ($formAddAnnuaire->isSubmitted() && $formAddAnnuaire->isValid()) {
            $manager->persist($newAnnuaire);
            $manager->flush();
            return $this->redirect($request->getUri('/admin/annuaire'));
        }

        // affichage du formulaire de modification de l'annuaire 1
        $annuaires = $repo->findAll();
        return $this->render('admin_annuaire/index.html.twig', [
            'controller_name' => 'AdminAnnuaireController',
            'annuaires' => $annuaires,
            'societes' => $societes,
            'formAnnuaire' => $formAddAnnuaire->createView(),
            'title' => "Administration de l'Annuaire"
            ]);

    }

    /**
     * 
     * Suppresion d'une ligne de l'annuaire
     * 
     * @Route("/admin/annuaire/delete/{id}", name="app_delete_annuaire")
     */
    public function deleteAnnuaire(Annuaire $annuaire){
        $em = $this->getDoctrine()->getManager();
                $em->remove($annuaire);
                $em->flush();
    
                $this->addFlash('message', 'Ligne de l\'annuaire Supprimée avec succès');
                return $this->redirectToRoute('app_admin_annuaire');
    }
    
     /**
     * 
     * Modification d'une ligne de l'annuaire
     * 
     * @Route("/admin/annuaire/edit/{id}", name="app_edit_annuaire")
     */
    public function editAnnuaire(Request $request, Annuaire $annuaire){
        
        $formEditAnnuaire = $this->createForm(AdminAnnuaireType::class, $annuaire);
        $formEditAnnuaire->handleRequest($request);

        if($formEditAnnuaire->isSubmitted() && $formEditAnnuaire->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($annuaire);
            $em->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_admin_annuaire');

        }
        return $this->render('admin_annuaire/edit_annuaire.html.twig',[
            'title' => "Modification de l'annuaire",
            'annuaireEditForm' => $formEditAnnuaire->createView()
        ]);
    }
    
}
