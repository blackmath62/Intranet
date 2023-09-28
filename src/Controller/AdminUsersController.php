<?php

namespace App\Controller;

use App\Entity\Main\Users;
use App\Form\EditUsersType;
use App\Repository\Main\UsersRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("ROLE_ADMIN")]

class AdminUsersController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route("/admin/users", name: "app_admin_users")]

    public function index(UsersRepository $repo, Request $request)
    {

        $users = $repo->findAll();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);

        return $this->render('admin_users/index.html.twig', [
            'controller_name' => 'AdminUsersController',
            'title' => 'Administration des Utilisateurs',
            'users' => $users,
        ]);
    }

    #[Route("/admin/users/change/metier/{id}/{metier}/{value}", name: "app_admin_users_change_metier")]

    public function getChangeMetier($metier, $value, Request $request, Users $user)
    {
        if ($metier == 'ev') {
            $user->setEv($value);
        } elseif ($metier == 'hp') {
            $user->setHp($value);
        } elseif ($metier == 'me') {
            $user->setMe($value);
        }

        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $this->addFlash('message', 'Utilisateur modifié avec succès');
        return $this->redirectToRoute('app_admin_users');
    }
    /**
     * Modifier un Utilisateur
     */
    #[Route("/admin/users/edit/{id}", name: "app_edit_user")]

    public function editUser(Users $user, Request $request, SluggerInterface $slugger)
    {

        $form = $this->createForm(EditUsersType::class, $user);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $em = $this->entityManager;
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_admin_users');

        }
        return $this->render('admin_users/edit_user.html.twig', [
            'userEditForm' => $form->createView(),
            'user' => $user,
            'title' => 'Edition des Utilisateurs',
        ]);
    }

    /**
     * Supprimer l'utilisateur, ne sert pas, nous fermons ou ouvrons les utilisateurs
     */
    #[Route("/admin/users/delete/{id}", name: "app_delete_user")]

    public function deleteUser(Users $user, Request $request)
    {
        $em = $this->entityManager;
        $em->remove($user);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $this->addFlash('message', 'Utilisateur Supprimé avec succès');
        return $this->redirectToRoute('app_admin_users');

    }

    /**
     * Fermer l'utilisateur
     */
    #[Route("/admin/users/close/{id}", name: "app_close_user")]

    public function closeUser(Users $user, Request $request)
    {
        $user->setClosedAt(new DateTime());
        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $this->addFlash('message', 'L\'utilisateur a été fermé avec succès');
        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * Ouvrir l'utilisateur
     */
    #[Route("/admin/users/open/{id}", name: "app_open_user")]

    public function openUser(Users $user, Request $request)
    {
        $user->setClosedAt(null);
        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $this->addFlash('message', 'L\'utilisateur a été ouvert avec succès');
        return $this->redirectToRoute('app_admin_users');

    }
}
