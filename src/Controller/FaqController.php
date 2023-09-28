<?php

namespace App\Controller;

use App\Form\FaqType;
use App\Repository\Main\FAQRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class FaqController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route("/faq", name: "app_faq")]

    public function index(Request $request, FAQRepository $repo): Response
    {
        $faqs = $repo->findAll();
        $form = $this->createForm(FaqType::class);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $faq = $form->getData();
            $faq->setCreatedAt(new \DateTime())
                ->setUser($this->getUser());
            $em = $this->entityManager;
            $em->persist($faq);
            $em->flush();

            $this->addFlash('message', 'FAQ créé avec succés');
            return $this->redirectToRoute('app_faq');
        }
        return $this->render('faq/index.html.twig', [
            'controller_name' => 'FaqController',
            'title' => 'FAQ',
            'faqs' => $faqs,
            'faqForm' => $form->createView(),
        ]);
    }

    #[Route("/faq/show/{id}", name: "app_faq_show")]

    public function faqShow(int $id, FAQRepository $repo)
    {
        $faq = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('faq/faq_show.html.twig', [
            'faq' => $faq,
            'title' => 'FAQ View',
        ]);
    }

    #[Route("/faq/edit/{id}", name: "app_faq_edit")]

    public function faqEdit(int $id, FAQRepository $repo, Request $request)
    {
        $faq = $repo->findOneBy(['id' => $id]);

        $form = $this->createForm(FaqType::class, $faq);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $faq = $form->getData();
            $em = $this->entityManager;
            $em->persist($faq);
            $em->flush();

            $this->addFlash('message', 'FAQ Modifié avec succés');
            return $this->redirectToRoute('app_faq_show', ['id' => $id]);
        }
        return $this->render('faq/faq_edit.html.twig', [
            'faqForm' => $form->createView(),
            'title' => 'FAQ Edit',
        ]);
    }
}
