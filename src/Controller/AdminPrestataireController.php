<?php

namespace App\Controller;

use App\Entity\Main\Prestataire;
use App\Form\PrestataireType;
use App\Repository\Main\PrestataireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]

class AdminPrestataireController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route("/admin/prestataire", name: "app_admin_prestataire")]

    public function index(PrestataireRepository $repo, Request $request): Response
    {

        $form = $this->createForm(PrestataireType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $prestataire = new Prestataire();
            $prestataire = $form->getData();
            $prestataire->setImg('unnamed.png');
            $em = $this->entityManager;
            $em->persist($prestataire);
            $em->flush();
        }
        $prestataires = $repo->findAll();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('admin_prestataire/index.html.twig', [
            'controller_name' => 'AdminPrestataireController',
            'prestataires' => $prestataires,
            'formPresta' => $form->createView(),
            'title' => 'Prestataire',
        ]);
    }

    #[Route("/admin/prestataire/delete/{id}", name: "app_delete_prestataire")]

    public function deletePrestataire($id, Request $request)
    {
        $repository = $this->entityManager->getRepository(Prestataire::class);
        $prestataireId = $repository->find($id);

        $em = $this->entityManager;
        $em->remove($prestataireId);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->redirect($this->generateUrl('app_admin_prestataire'));
    }

    #[Route("/admin/prestataire/edit/{id}", name: "app_edit_prestataire")]

    public function editprestataire(Prestataire $prestataire, Request $request)
    {
        $form = $this->createForm(PrestataireType::class, $prestataire);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;
            $em->persist($prestataire);
            $em->flush();

            $this->addFlash('message', 'Prestataire modifié avec succès');
            return $this->redirectToRoute('app_admin_prestataire');

        }
        return $this->render('admin_prestataire/edit_prestataire.html.twig', [
            'prestataireEditForm' => $form->createView(),
            'prestataire' => $prestataire,
            'title' => 'Edition des prestataires',
        ]);
    }
}
