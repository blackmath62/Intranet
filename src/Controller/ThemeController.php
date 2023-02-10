<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class ThemeController extends AbstractController
{
    /**
     * @Route("/theme", name="theme")
     */
    public function index(Request $request): Response
    {
        // tracking user page for stats
        //  $tracking = $request->attributes->get('_route');
        //   $this->setTracking($tracking);

        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ThemeController',
        ]);
    }
}
