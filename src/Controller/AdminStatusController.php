<?php

namespace App\Controller;

use DateTime;
use App\Entity\Status;
use App\Form\EditStatusType;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminStatusController extends AbstractController
{
   /**
     * @Route("/admin/status", name="app_admin_status")
     */
    public function index(Status $status = null, Request $request, StatusRepository $repo, EntityManagerInterface $manager)
    {
        if(!$status){
            $status = new Status();
            }
            $form = $this->createFormBuilder($status)
                ->add("title")
                ->add('backgroundColor')
                ->add('textColor')
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $status->setCreatedAt(new DateTime());
                $manager->persist($status);
                $manager->flush();
            }
            $statut = $repo->findAll();
            return $this->render('admin_status/index.html.twig', [
                'controller_name' => 'AdminStatusController',
                'status' => $statut,
                'formStatus' => $form->createView(),
                'title' => "Administration des statuts"
            ]);
    }

    /**
     * @Route("/admin/statut/delete/{id}",name="app_delete_status")
     */
    public function deleteStatus($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Status::class);
        $StatusId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($StatusId);
        $em->flush();        

        
        return $this->redirect($this->generateUrl('app_admin_status'));
    }
    /**
     * @Route("/admin/status/edit/{id}",name="app_edit_status")
     */
    public function editSociete(Status $status, Request $request)
    {
        $form = $this->createForm(EditStatusType::class, $status);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($status);
                $em->flush();

                $this->addFlash('success', 'Statut modifié avec succès');
                return $this->redirectToRoute('app_admin_status');

            }
            return $this->render('admin_status/edit_status.html.twig',[
                'statusEditForm' => $form->createView(),
                'status' => $status,
                'title' => 'Edition des statuts'
            ]);
    }
}
