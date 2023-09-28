<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]

class AdminController extends AbstractController
{
    #[Route("/admin", name: "app_admin")]

    public function index(Request $request)
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'title' => "Administration",
        ]);
    }
}
