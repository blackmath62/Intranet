<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfileUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProfileUserController extends AbstractController
{
    /**
     * @Route("/profile/user", name="profile_user")
     */
    public function index(Request $request, Users $user)
    {
        $form = $this->createForm(ProfileUserType::class, $this->user);
        $form->handleRequest($request);

        return $this->render('profile_user/index.html.twig',[
            'controller_name' => 'ProfileUserController',
            'title' => 'gestion de mon compte',
            'profileUserForm' => $form->createView(),
            'user' => $user
        ]);
    }

    
}
