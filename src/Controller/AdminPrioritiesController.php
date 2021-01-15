<?php

namespace App\Controller;

use DateTime;
use App\Entity\Priorities;
use App\Form\EditPrioritiesType;
use App\Repository\PrioritiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class AdminPrioritiesController extends AbstractController
{
     /**
     * @Route("/admin/priorities", name="app_admin_priorities")
     */
    public function index(Priorities $priorities = null, Request $request, PrioritiesRepository $repo, EntityManagerInterface $manager)
    {
        if(!$priorities){
            $priorities = new Priorities();
            }
            $form = $this->createFormBuilder($priorities)
                ->add("title")
                ->add('color')
                ->add('textColor')
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
               // $priorities->setCreatedAt(new DateTime());
                $manager->persist($priorities);
                $manager->flush();
            }
            $priorities = $repo->findAll();
            return $this->render('admin_priorities/index.html.twig', [
                'controller_name' => 'AdminprioritiesController',
                'priorities' => $priorities,
                'formPriorities' => $form->createView(),
                'title' => "Paramétres_priorities"
            ]);
    }

    /**
     * @Route("/admin/priorities/delete/{id}",name="app_delete_priorities")
     */
    public function deletepriorities($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(priorities::class);
        $prioritiesId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($prioritiesId);
        $em->flush();        

        
        return $this->redirect($this->generateUrl('app_admin_priorities'));
    }
    /**
     * @Route("/admin/priorities/edit/{id}",name="app_edit_priorities")
     */
    public function editSociete(priorities $priorities, Request $request)
    {
        $form = $this->createForm(EditPrioritiesType::class, $priorities);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($priorities);
                $em->flush();

                $this->addFlash('success', 'priorities modifié avec succès');
                return $this->redirectToRoute('app_admin_priorities');

            }
            return $this->render('admin_priorities/edit_priorities.html.twig',[
                'prioritiesEditForm' => $form->createView(),
                'priorities' => $priorities
            ]);
    }
}
