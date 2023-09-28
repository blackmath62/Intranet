<?php

namespace App\Controller;

use App\Entity\Main\Logiciel;
use App\Form\EditLogicielType;
use App\Repository\Main\LogicielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]

class LogicielController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route("/admin/logiciels", name: "app_admin_logiciel")]

    public function index(Request $request, LogicielRepository $repo, EntityManagerInterface $manager, Logiciel $logiciel = null)
    {
        if (!$logiciel) {
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
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

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
            'title' => "Administration des logiciels",
        ]);
    }

    #[Route("/admin/logiciel/delete/{id}", name: "app_delete_logiciel")]

    public function deletelogiciel($id)
    {
        $repository = $this->entityManager->getRepository(Logiciel::class);
        $logicielId = $repository->find($id);

        // tracking user page for stats
        // $tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);

        $em = $this->entityManager;
        $em->remove($logicielId);
        $em->flush();

        return $this->redirect($this->generateUrl('app_admin_logiciel'));
    }
    #[Route("/admin/logiciel/edit/{id}", name: "app_edit_logiciel")]

    public function editlogiciel(Logiciel $logiciel, Request $request)
    {
        $form = $this->createForm(EditLogicielType::class, $logiciel);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;
            $em->persist($logiciel);
            $em->flush();

            $this->addFlash('message', 'Logiciel modifié avec succès');
            return $this->redirectToRoute('app_admin_logiciel');

        }
        return $this->render('logiciel/edit_logiciels.html.twig', [
            'logicielEditForm' => $form->createView(),
            'logiciels' => $logiciel,
            'title' => 'Edition des logiciels',
        ]);
    }
}
