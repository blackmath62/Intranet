<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]

class ThemeController extends AbstractController
{
    #[Route("/theme", name: "theme")]

    public function index(): Response
    {
        // tracking user page for stats
        //  $tracking = $request->attributes->get('_route');
        //   $this->setTracking($tracking);

        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ThemeController',
        ]);
    }
}
