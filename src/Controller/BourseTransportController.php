<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BourseTransportController extends AbstractController
{
    /**
     * @Route("/bourse/transport", name="app_bourse_transport")
     */
    public function index(): Response
    {
        return $this->render('bourse_transport/index.html.twig', [
            'controller_name' => 'BourseTransportController',
            'title' => 'Bourse aux transports'
        ]);
    }
}
