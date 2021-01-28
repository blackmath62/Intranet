<?php

namespace App\Controller;

use DateTime;
use App\Entity\Societe;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class AdminParameterController extends AbstractController
{
    /**
     * @Route("/admin/parameter", name="app_admin_parameter")
     */
    public function index()
    {
        return $this->render('admin_parameter/index.html.twig', [
            'controller_name' => 'AdminParameterController',
            'title' => "Administration des paramétres"
        ]);
    }
}
