<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */

class VideoController extends AbstractController
{
    /**
     * @Route("/video", name="app_video")
     */
    public function index(Request $request): Response
    {

        // tracking user page for stats
        //  $tracking = $request->attributes->get('_route');
        //  $this->setTracking($tracking);

        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
            'title' => "Vidéo",
        ]);
    }
}
