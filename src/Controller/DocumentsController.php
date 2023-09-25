<?php

namespace App\Controller;

use App\Repository\Main\DocumentsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class DocumentsController extends AbstractController
{
    #[Route("/Lh/documents", name: "app_lhermitte_documents")]

    public function Lhermitte_Documents(DocumentsRepository $repo, Request $request)
    {
        // TODO JEROME gérer le numéro de société ci dessous
        $documents = $repo->findBy(['societe' => 1]);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('documents/index.html.twig', [
            'controller_name' => 'DocumentsController',
            'documents' => $documents,
            'title' => 'Documents Lhermitte',
        ]);
    }
    #[Route("/Rb/documents", name: "app_Roby_documents")]

    public function Roby_Documents(DocumentsRepository $repo, Request $request)
    {
        // TODO JEROME gérer le numéro de société ci dessous
        $documents = $repo->findBy(['societe' => 15]);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('documents/index.html.twig', [
            'controller_name' => 'DocumentsController',
            'documents' => $documents,
            'title' => 'Documents Roby',
        ]);
    }
}
