<?php

namespace App\Controller;

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
    public function Lhermitte_Documents()
    {
        // findby dossier Lhermitte
        return $this->render('documents/index.html.twig', [
            'controller_name' => 'DocumentsController',
        ]);
    }
    /**
     * @Route("/Roby/documents", name="app_Roby_documents")
     */
    public function Roby_Documents()
    {
        // findby dossier Roby
        return $this->render('documents/index.html.twig', [
            'controller_name' => 'DocumentsController',
        ]);
    }
}
