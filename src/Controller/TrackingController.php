<?php

namespace App\Controller;

use App\Entity\Main\Trackings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class TrackingController extends AbstractController
{
    /**
     * @Route("/tracking", name="tracking")
     */
    
}
