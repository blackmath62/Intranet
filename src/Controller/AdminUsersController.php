<?php

namespace App\Controller;

use App\Entity\Main\Users;
use App\Form\EditUsersType;
use App\Repository\Main\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            'title' => 'Administration des Utilisateurs',
            'users' => $users,
        ]);
    }
    /**
     * Modifier un Utilisateur
     * 
     * @Route("/admin/users/edit/{id}", name="app_edit_user")
     */
    public function editUser(Users $user, Request $request, SluggerInterface $slugger){


            $form = $this->createForm(EditUsersType::class, $user);
            $form->handleRequest($request);
            //dd($form);
            if($form->isSubmitted() && $form->isValid()){
                $user = $form->getData();
                /*$file = $form->get('img')->getData();
                // TODO JEROME Voir pour avoir accés à la modification de l'image utilisateur, actuellement si le champ est vide, il ne garde pas l'ancienne image
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('doc_profiles'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
    
                    // updates the 'Filename' property to store the PDF file name
                    // instead of its contents
                    $user->setImg($newFilename);
                }
                if(!$img){
                   $user->setImg('AdminLTELogo.png');
                }*/
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Utilisateur modifié avec succès');
                return $this->redirectToRoute('app_admin_users');

            }
            return $this->render('admin_users/edit_user.html.twig',[
                'userEditForm' => $form->createView(),
                'user' => $user,
                'title' => 'Edition des Utilisateurs'
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
