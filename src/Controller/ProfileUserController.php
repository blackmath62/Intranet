<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfileUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @IsGranted("ROLE_USER")
 */

class ProfileUserController extends AbstractController
{
    /**
     * @Route("/profile/user", name="app_profile_user")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUserType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_profile_user');

        }

        return $this->render('profile_user/index.html.twig',[
            'controller_name' => 'ProfileUserController',
            'title' => 'gestion de mon compte',
            'profileUserForm' => $form->createView(),
        ]);
    }

    
}
