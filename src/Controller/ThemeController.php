<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class ThemeController extends AbstractController
{
    /**
     * @Route("/theme", name="theme")
     */
    public function index(): Response
    {
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ThemeController',
        ]);
    }
}
