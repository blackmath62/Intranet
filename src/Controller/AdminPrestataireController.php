<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPrestataireController extends AbstractController
{
    /**
     * @Route("/admin/prestataire", name="app_admin_prestataire")
     */
    public function index(): Response
    {
        return $this->render('admin_prestataire/index.html.twig', [
            'controller_name' => 'AdminPrestataireController',
        ]);
    }
}
