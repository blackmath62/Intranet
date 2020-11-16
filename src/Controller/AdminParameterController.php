<?php

namespace App\Controller;

use DateTime;
use App\Entity\Societe;
use App\Entity\Priorities;
use App\Form\PrioritiesType;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class AdminParameterController extends AbstractController
{
    /**
     * @Route("/admin/parameter", name="app_admin_parameter")
     */
    public function index(Societe $societe = null, Request $request, SocieteRepository $repo, EntityManagerInterface $manager)
    {
        if(!$societe){
            $societe = new Societe();
            }
            $form = $this->createFormBuilder($societe)
                ->add("nom")
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $societe->setCreatedAt(new DateTime());
                $manager->persist($societe);
                $manager->flush();
            }
            $societes = $repo->findAll();
            return $this->render('admin_parameter/index.html.twig', [
                'controller_name' => 'AdminParameterController',
                'societes' => $societes,
                'formSociete' => $form->createView(),
                'title' => "Paramétres"
            ]);
    }

    /**
     * @Route("/admin/delete/societe/{id}",name="app_delete_societe")
     */
    public function deleteSociete($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Societe::class);
        $societeId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($societeId);
        $em->flush();        

        
        return $this->redirect($this->generateUrl('app_admin_parameter'));
    }
    /**
     * @Route("/admin/edit/societe/{id}",name="app_edit_societe")
     */
    public function editSociete($id, $nom)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Societe::class);
        $societeId = $repository->find($id);
        if (!$societeId) {
            throw $this->createNotFoundException('La société n\'a pas été trouvé');
        } 
        $em = $this->getDoctrine()->getManager();
        $societeId->setNom($nom);
        $em->flush();        

        return $this->redirect($this->generateUrl('app_admin_parameter'));
    }
}
