<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]

class AdminParameterController extends AbstractController
{

    #[Route("/admin/parameter", name: "app_admin_parameter")]

    public function index(Request $request)
    {

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('admin_parameter/index.html.twig', [
            'controller_name' => 'AdminParameterController',
            'title' => "Administration des paramétres",
        ]);
    }
}
