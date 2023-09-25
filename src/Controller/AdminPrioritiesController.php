<?php

namespace App\Controller;

use App\Entity\Main\Priorities;
use App\Form\EditPrioritiesType;
use App\Repository\Main\PrioritiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]

class AdminPrioritiesController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route("/admin/priorities", name: "app_admin_priorities")]

    public function index(Priorities $priorities = null, Request $request, PrioritiesRepository $repo, EntityManagerInterface $manager)
    {
        if (!$priorities) {
            $priorities = new Priorities();
        }
        $form = $this->createFormBuilder($priorities)
            ->add("title")
            ->add('color')
            ->add('textColor')
            ->add('fa')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $priorities->setCreatedAt(new DateTime());
            $manager->persist($priorities);
            $manager->flush();
        }
        $priorities = $repo->findAll();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('admin_priorities/index.html.twig', [
            'controller_name' => 'AdminprioritiesController',
            'priorities' => $priorities,
            'formPriorities' => $form->createView(),
            'title' => "Administration des priorités",
        ]);
    }

    #[Route("/admin/priorities/delete/{id}", name: "app_delete_priorities")]

    public function deletepriorities($id, Request $request)
    {
        $repository = $this->entityManager->getRepository(priorities::class);
        $prioritiesId = $repository->find($id);

        $em = $this->entityManager;
        $em->remove($prioritiesId);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->redirect($this->generateUrl('app_admin_priorities'));
    }

    #[Route("/admin/priorities/edit/{id}", name: "app_edit_priorities")]

    public function editSociete(priorities $priorities, Request $request)
    {
        $form = $this->createForm(EditPrioritiesType::class, $priorities);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;
            $em->persist($priorities);
            $em->flush();

            $this->addFlash('message', 'priorities modifié avec succès');
            return $this->redirectToRoute('app_admin_priorities');

        }
        return $this->render('admin_priorities/edit_priorities.html.twig', [
            'prioritiesEditForm' => $form->createView(),
            'priorities' => $priorities,
            'title' => 'Edition des priorités',
        ]);
    }
}
