<?php

namespace App\Controller;

use App\Form\ListDivaltoUsersType;
use App\Entity\Main\ListDivaltoUsers;
use App\Repository\Divalto\VrpRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\ListDivaltoUsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListDivaltoUsersController extends AbstractController
{
    
    private $vrpRepo;
    private $listDivaltoUsersRepo;

    public function __construct(VrpRepository $vrpRepo, ListDivaltoUsersRepository $listDivaltoUsersRepo)
    {
        $this->vrpRepo = $vrpRepo;
        $this->listDivaltoUsersRepo = $listDivaltoUsersRepo;

        //parent::__construct();
    } 

    /**
     * @Route("admin/list/divalto/users/test", name="app_list_divalto_users_test")
     */
    public function test(Request $request): Response
    {
        $form = $this->createForm(ListDivaltoUsersType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            /*$entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($add);
            $entityManager->flush();*/
            
            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_list_divalto_users_test');
        }
        return $this->render('test/index.html.twig', [
            'users' => $form->createView(),
            'title' => 'Utilisateurs Divalto',
        ]);
    }

    /**
     * @Route("admin/list/divalto/users/show", name="app_list_divalto_users_show")
     */
    public function show(): Response
    {
        //dd($this->listDivaltoUsersRepo->findAll());
        return $this->render('list_divalto_users/index.html.twig', [
            'users' => $this->listDivaltoUsersRepo->findAll(),
            'title' => 'Utilisateurs Divalto',
        ]);
    }

    /**
     * @Route("admin/list/divalto/users/lock/unlock/{id}/{value}", name="app_list_divalto_users_lock_unlock")
     */
    public function lockUnlock($id,$value): Response
    {
        if ($value == 'false') {
            $value = false;
        }else {
            $value = true;
        }
        $user = $this->listDivaltoUsersRepo->findOneBy(['divalto_id' => $id]);
        $user->setValid($value);
        
        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_list_divalto_users_show');
    }

    /**
     * @Route("admin/list/divalto/users/update", name="app_list_divalto_users_update")
     */
    public function update(): Response
    {
        $users =  $this->vrpRepo->UpdateListDivaltoUser();
        foreach ($users as $value) {
            $findUser = $this->listDivaltoUsersRepo->findOneBy(['divalto_id' => $value['divalto_id']]);
            if ($findUser == null | $findUser == '') {
                $user = new ListDivaltoUsers();
                $user->setValid(true);
            }else {
                $user = $findUser;
            }
            $user->setDivaltoId($value['divalto_id'])
                    ->setUserX($value['userX'])
                    ->setNom($value['nom'])
                    ->setDos($value['dos'])
                    ->setEmail($value['email']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_list_divalto_users_show');
    }
}
