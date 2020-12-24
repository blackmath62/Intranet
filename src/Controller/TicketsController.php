<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Form\TicketsType;
use App\Repository\TicketsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TicketsController extends AbstractController
{
    /**
     * @Route("/tickets", name="app_tickets")
     */
    public function index(TicketsRepository $repo, Request $request)
    {
 
        $tickets = $repo->findAll();
        $form = $this->createForm(TicketsType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $ticket = $form->getData();
            $ticket->setCreatedAt(new \DateTime())
                   ->setUser($this->getUser());
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('message', 'Ticket créé avec succés');
            return $this->redirectToRoute('app_tickets');

        }
        return $this->render('tickets/index.html.twig', [
            'controller_name' => 'TicketsController',
            'tickets' => $tickets,
            'formTickets' => $form->createView()
        ]);
    }
}
