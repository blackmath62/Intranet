<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Entity\Annuaire;
use App\Repository\SocieteRepository;
use App\Repository\AnnuaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAnnuaireController extends AbstractController
{
    /**
     * @Route("/adminAnnuaire", name="admin_annuaire")
     */
    public function index(Request $request, AnnuaireRepository $repo, SocieteRepository $repoSociete, EntityManagerInterface $manager)
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
        return $this->render('admin_annuaire/index.html.twig', [
            'controller_name' => 'AdminAnnuaireController',
            'annuaires' => $annuaires,
            'societes' => $societes,
            'formAnnuaire' => $form->createView(),
            'title' => "Admin Annuaire"
        ]);
    }
    
}
