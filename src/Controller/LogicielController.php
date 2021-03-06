<?php

namespace App\Controller;

use App\Entity\Main\Logiciel;
use App\Form\EditLogicielType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Main\LogicielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @IsGranted("ROLE_ADMIN")
*/
class LogicielController extends AbstractController
{
    /**
     * @Route("/admin/logiciels", name="app_admin_logiciel")
     */
   
    public function index(Logiciel $logiciel = null, Request $request, LogicielRepository $repo, EntityManagerInterface $manager)
    {
        if(!$logiciel){
            $logiciel = new Logiciel();
            }
            $form = $this->createFormBuilder($logiciel)
                ->add("nom")
                ->add("backgroungColor")
                ->add("textColor")
                ->add('icon')
                ->getForm();
            $form->handleRequest($request);

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);

            if ($form->isSubmitted() && $form->isValid()) {
                $logiciel->setCreatedAt(new \DateTime());
                $logiciel->setClosedAt(new \DateTime());
                $manager->persist($logiciel);
                $manager->flush();
            }
            $logiciels = $repo->findAll();
            return $this->render('logiciel/index.html.twig', [
                'controller_name' => 'AdminlogicielController',
                'logiciels' => $logiciels,
                'formLogiciel' => $form->createView(),
                'title' => "Administration des logiciels"
            ]);
    }

    /**
     * @Route("/admin/logiciel/delete/{id}",name="app_delete_logiciel")
     */
    public function deletelogiciel($id, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Logiciel::class);
        $logicielId = $repository->find($id);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $em = $this->getDoctrine()->getManager();
        $em->remove($logicielId);
        $em->flush();        

        
        return $this->redirect($this->generateUrl('app_admin_logiciel'));
    }
    /**
     * @Route("/admin/logiciel/edit/{id}",name="app_edit_logiciel")
     */
    public function editlogiciel(Logiciel $logiciel, Request $request)
    {
        $form = $this->createForm(EditLogicielType::class, $logiciel);
            $form->handleRequest($request);

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($logiciel);
                $em->flush();

                $this->addFlash('message', 'Logiciel modifié avec succès');
                return $this->redirectToRoute('app_admin_logiciel');

            }
            return $this->render('logiciel/edit_logiciels.html.twig',[
                'logicielEditForm' => $form->createView(),
                'logiciels' => $logiciel,
                'title' => 'Edition des logiciels'
            ]);
    }
}
