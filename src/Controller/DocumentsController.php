<?php

namespace App\Controller;

use App\Repository\DocumentsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @IsGranted("ROLE_USER")
 */
class DocumentsController extends AbstractController
{
    /**
     * @Route("/lhermitte/documents", name="app_lhermitte_documents")
     */
    public function Lhermitte_Documents(DocumentsRepository $repo)
    {
        // TODO JEROME gérer le numéro de société ci dessous
        $documents = $repo->findBy(['societe' => 12]);
        return $this->render('documents/index.html.twig', [
            'controller_name' => 'DocumentsController',
            'documents' => $documents
        ]);
    }
    /**
     * @Route("/Roby/documents", name="app_Roby_documents")
     */
    public function Roby_Documents(DocumentsRepository $repo)
    {
        // TODO JEROME gérer le numéro de société ci dessous
        $documents = $repo->findBy(['societe' => 15]);
        return $this->render('documents/index.html.twig', [
            'controller_name' => 'DocumentsController',
            'documents' => $documents
        ]);
    }
}
