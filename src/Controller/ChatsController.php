<?php

namespace App\Controller;

use App\Entity\Chats;
use App\Form\ChatsType;
use App\Repository\ChatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatsController extends AbstractController
{
    /**
     * @Route("/chats", name="app_chats")
     */
    public function editUser(Request $request, EntityManagerInterface $em, ChatsRepository $repo){

        $chats = $repo->findAll();
        $form = $this->createForm(ChatsType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $chat = $form->getData();
            $chat->setCreatedAt(new \DateTime())
                ->setUser($this->getUser());
            $em->persist($chat);
            $em->flush();
            $this->addFlash('success', 'Chat modifié avec succès');
            return $this->redirectToRoute('app_chats');

        }
        return $this->render('chats/index.html.twig',[
            'ChatsForm' => $form->createView(),
            'chats' => $chats
        ]);
}
}