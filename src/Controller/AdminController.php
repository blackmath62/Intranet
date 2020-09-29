<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Entity\Annuaire;
use Doctrine\ORM\EntityManager;
use App\Repository\SocieteRepository;
use App\Repository\AnnuaireRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/getAnnuaire", name="getAnnuaire")
     */
    public function getAnnuaire(Request $request, AnnuaireRepository $repo, SocieteRepository $repoSociete, EntityManagerInterface $manager)
    {
        $societes = $repoSociete->findAll();
        $newAnnuaire = new Annuaire();
        $form = $this->createFormBuilder($newAnnuaire)
            ->add("interne")
            ->add("nom")
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
        $form->handleRequest($request);
        // Si le formulaire a été soumis et est valide, on créé le nouveau contact dans l'annuaire
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($newAnnuaire);
            $manager->flush();
        }

        // affichage du formulaire de modification de l'annuaire 1
        $annuaires = $repo->findAll();
        return $this->render('admin/getAnnuaire.html.twig', [
            'controller_name' => 'AdminController',
            'annuaires' => $annuaires,
            'societes' => $societes,
            'formAnnuaire' => $form->createView()
        ]);
    }
    /**
     * @Route("/parameter", name="parameter")
     * @Route("/parameter/{id}/edit", name="societe_edit")
     */
    public function getGeneralParameter(Societe $societe = null, Request $request, SocieteRepository $repo, EntityManagerInterface $manager)
    {
        if(!$societe){
        $societe = new Societe();
        }
        $form = $this->createFormBuilder($societe)
            ->add("nom")
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!$societe){
            $societe->setCreatedAt(new \DateTime());
            }
            $manager->persist($societe);
            $manager->flush();
        }
        $societes = $repo->findAll();
        return $this->render('admin/getGeneralParameter.html.twig', [
            'controller_name' => 'AdminController',
            'societes' => $societes,
            'formSociete' => $form->createView()
        ]);
    }

    /**
     * @Route("/deleteSociete/{id}",name="deleteSociete")
     */
    // todo
    public function deleteSociete($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Societe::class);
        $societeId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($societeId);
        $em->flush();        

        /*return $this->render('admin/getGeneralParameter.html.twig');*/
        return $this->redirect($this->generateUrl('parameter'));
    }
    /**
     * @Route("/updateSociete/{id}/{text}",name="updateSociete")
     */
    // todo
    public function updateSociete($id, $nom)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Societe::class);
        $societeId = $repository->find($id);
        if (!$societeId) {
            throw $this->createNotFoundException('La société n\'a pas été trouvé');
        } 
        $em = $this->getDoctrine()->getManager();
        $societeId->setNom($nom);
        $em->flush();        

        /*return $this->render('admin/getGeneralParameter.html.twig');*/
        return $this->redirect($this->generateUrl('parameter'));
    }

}
