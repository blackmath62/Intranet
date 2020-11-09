<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsersController extends AbstractController
{
    /**
     * @Route("/admin/users", name="app_admin_users")
     */
    public function index(UsersRepository $repo)
    {
        $users =$repo->findAll();
        return $this->render('admin_users/index.html.twig', [
            'controller_name' => 'AdminUsersController',
            'title' => 'gestion Utilisateurs',
            'users' => $users,
        ]);
    }

}
