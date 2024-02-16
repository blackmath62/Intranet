<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MouvAdminPreparationCmdController extends AbstractController
{
    #[Route('/mouv/admin/preparation/cmd', name: 'app_mouv_admin_preparation_cmd')]
    public function index(): Response
    {
        return $this->render('mouv_admin_preparation_cmd/index.html.twig', [
            'controller_name' => 'MouvAdminPreparationCmdController',
        ]);
    }
}
