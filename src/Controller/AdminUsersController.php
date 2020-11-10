<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\EditUsersType;
use App\Repository\UsersRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_ADMIN")
 */

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
    /**
     * Modifier un Utilisateur
     * 
     * @Route("/admin/users/edit/{id}", name="app_edit_user")
     */
    public function editUser(Users $user, Request $request){

            $form = $this->createForm(EditUsersType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('message', 'Utilisateur modifié avec succès');
                return $this->redirectToRoute('app_admin_users');

            }
            return $this->render('admin_users/edit_user.html.twig',[
                'userEditForm' => $form->createView(),
                'user' => $user
            ]);
    }

    /**
     * Supprimer l'utilisateur
     * 
     * @Route("/admin/users/delete/{id}", name="app_delete_user")
     */

    public function deleteUser(Users $user)
        {
                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();
    
                $this->addFlash('message', 'Utilisateur Supprimé avec succès');
                return $this->redirectToRoute('app_admin_users');
            
        }
}
