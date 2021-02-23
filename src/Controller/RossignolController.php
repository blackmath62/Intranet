<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RossignolController extends AbstractController
{
    /**
     * @Route("/Lhermitte/rossignol", name="app_lhermitte_rossignols")
     */
    public function index(): Response
    {
        return $this->render('rossignol/index.html.twig', [
            'title' => 'Rossignol',
        ]);
    }
}
