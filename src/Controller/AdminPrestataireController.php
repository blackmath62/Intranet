<?php

namespace App\Controller;

use App\Entity\Main\Prestataire;
use App\Form\PrestataireType;
use App\Repository\Main\PrestataireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class AdminPrestataireController extends AbstractController
{
    /**
     * @Route("/admin/prestataire", name="app_admin_prestataire")
     */
    public function index(PrestataireRepository $repo, Request $request): Response
    {
        
        $form = $this->createForm(PrestataireType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $prestataire = new Prestataire();
            $prestataire = $form->getData();
            $prestataire->setImg('unnamed.png');
            $em = $this->getDoctrine()->getManager();
            $em->persist($prestataire);
            $em->flush();
        }
        $prestataires = $repo->findAll();
        return $this->render('admin_prestataire/index.html.twig', [
            'controller_name' => 'AdminPrestataireController',
            'prestataires' => $prestataires,
            'formPresta' => $form->createView(),
            'title' => 'Prestataire',
        ]);
    }

    /**
     * @Route("/admin/prestataire/delete/{id}",name="app_delete_prestataire")
     */
    public function deletePrestataire($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Prestataire::class);
        $prestataireId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($prestataireId);
        $em->flush();        

        
        return $this->redirect($this->generateUrl('app_admin_prestataire'));
    }
    /**
     * @Route("/admin/prestataire/edit/{id}",name="app_edit_prestataire")
     */
    public function editprestataire(Prestataire $prestataire, Request $request)
    {
        $form = $this->createForm(PrestataireType::class, $prestataire);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($prestataire);
                $em->flush();

                $this->addFlash('success', 'Prestataire modifié avec succès');
                return $this->redirectToRoute('app_admin_prestataire');

            }
            return $this->render('admin_prestataire/edit_prestataire.html.twig',[
                'prestataireEditForm' => $form->createView(),
                'prestataire' => $prestataire,
                'title' => 'Edition des prestataires'
            ]);
    }
}
