<?php

namespace App\Controller;

use App\Entity\Main\TypeDocumentFsc;
use App\Form\TypeDocumentFscType;
use App\Repository\Main\TypeDocumentFscRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class TypeDocumentFscController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    /**
     * @Route("/type/document/fsc", name="app_type_document_fsc")
     */
    public function index(TypeDocumentFscRepository $repo, Request $request): Response
    {
        $form = $this->createForm(TypeDocumentFscType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $typeDoc = new TypeDocumentFsc();
            $typeDoc = $form->getData();
            $typeDoc->setCreatedAt(new DateTime());
            $em = $this->entityManager;
            $em->persist($typeDoc);
            $em->flush();
        }
        $typeDocs = $repo->findAll();

        // tracking user page for stats
        //  $tracking = $request->attributes->get('_route');
        //  $this->setTracking($tracking);

        return $this->render('type_document_fsc/index.html.twig', [
            'typeDocs' => $typeDocs,
            'form' => $form->createView(),
            'title' => 'Type document FSC',
        ]);
    }

    /**
     * @Route("/type/document/fsc/edit/{id}", name="app_type_document_fsc_edit")
     */
    public function edit($id, TypeDocumentFsc $typeDocFsc, Request $request): Response
    {
        $form = $this->createForm(TypeDocumentFscType::class, $typeDocFsc);
        $form->handleRequest($request);

        // tracking user page for stats
        //    $tracking = $request->attributes->get('_route');
        //   $this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;
            $em->persist($typeDocFsc);
            $em->flush();

            $this->addFlash('message', 'Type de document FSC modifié avec succès');
            return $this->redirectToRoute('app_type_document_fsc');

        }
        return $this->render('type_document_fsc/edit_type_doc_fsc.html.twig', [
            'form' => $form->createView(),
            'typeDocFsc' => $typeDocFsc,
            'title' => 'Modification type doc Fsc',
        ]);
    }

    /**
     * @Route("/type/document/fsc/delete/{id}", name="app_type_document_fsc_delete")
     */
    public function delete($id, Request $request): Response
    {
        $repository = $this->entityManager->getRepository(TypeDocumentFsc::class);
        $typeDocFscId = $repository->find($id);

        $em = $this->entityManager;
        $em->remove($typeDocFscId);
        $em->flush();

        // tracking user page for stats
        //    $tracking = $request->attributes->get('_route');
        //    $this->setTracking($tracking);

        return $this->redirect($this->generateUrl('app_type_document_fsc'));
    }

}
