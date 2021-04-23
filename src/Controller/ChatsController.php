<?php

namespace App\Controller;

use App\Form\ChatsType;
use App\Repository\Main\ChatsRepository;
use App\Repository\Main\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class ChatsController extends AbstractController
{
    /**
     * @Route("/chats", name="app_chats")
     */
    public function chat(Request $request, EntityManagerInterface $em, ChatsRepository $repo, UsersRepository $repoUsers){

        $chats = $repo->findAll();
        $users= $repoUsers->FindAll();
        $form = $this->createForm(ChatsType::class);
        $form->handleRequest($request);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $chat = $form->getData();
            $chat->setCreatedAt(new \DateTime())
                ->setUser($this->getUser());
            $em->persist($chat);
            $em->flush();
            $this->addFlash('message', 'Chat modifié avec succès');
            return $this->redirectToRoute('app_chats');

        }
        return $this->render('chats/index.html.twig',[
            'ChatsForm' => $form->createView(),
            'chats' => $chats,
            'users' => $users,
            'title' => 'Chat'
        ]);
}
}