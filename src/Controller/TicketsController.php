<?php

namespace App\Controller;

use App\Form\TicketsType;
use App\Repository\TicketsRepository;
use App\Repository\CommentsRepository;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TicketsController extends AbstractController
{
    /**
     * @Route("/tickets", name="app_tickets")
     */
    public function tickets_Show(TicketsRepository $repo, Request $request, SluggerInterface $slugger, CommentsRepository $repoComment, StatusRepository $repoStatus)
    {
        $tickets = $repo->findAll();
        $comments = $repoComment->findAll();
        $status = $repoStatus->findAll();
        // Formulaire du Ticket
        
        $form = $this->createForm(TicketsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();
            $ticket->setCreatedAt(new \DateTime())
                ->setUser($this->getUser());
            // On récupére le fichier dans le formulaire
            $file = $form->get('file')->getData();


            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('doc_tickets'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'Filename' property to store the PDF file name
                // instead of its contents
                $ticket->setFile($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            
            $this->addFlash('message', 'Ticket créé avec succés');
            return $this->redirectToRoute('app_tickets');
        }
           
        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'comments' => $comments,
            'status' => $status,
            'formTickets' => $form->createView()
        ]);
    }

    
}
