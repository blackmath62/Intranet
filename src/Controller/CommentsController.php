<?php

namespace App\Controller;

use DateTime;
use App\Entity\Status;
use App\Entity\Tickets;
use App\Entity\Comments;
use App\Form\SendTicketType;
use App\Form\CommentsTicketsType;
use Symfony\Component\Mime\Email;
use App\Form\SendTicketAnnuaireType;
use App\Form\SendMailPrestataireType;
use App\Repository\TicketsRepository;
use App\Form\EditStatusTicketFormType;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestataireRepository;
use Egulias\EmailValidator\Warning\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentsController extends AbstractController
{
    /**
     * @Route("/ticket/comment/add/{id<\d+>}", name="app_comment")
     * @ParamConverter("Comments", options={"id" = "Ticket_id"})
     */
    public function addComment(int $id, MailerInterface $mailer,TicketsRepository $repoTicket, CommentsRepository $repoComments, Request $request, EntityManagerInterface $em, PrestataireRepository $repoPresta	)
    {
        // Enregistrement des commentaires
        
        $comments = $repoComments->findBy(['ticket' => $id]);
        $formComment = $this->createForm(CommentsTicketsType::class);
        $formComment->handleRequest($request);
        if($formComment->isSubmitted() && $formComment->isValid()){
            $comment = new Comments();
            $comment = $formComment->getData();
            $comment->setCreatedAt(new \DateTime())
            // TODO JEROME pouvoir ajouter plusieurs piéces jointes
            //->setFiles($id)
            ->setUser($this->getUser())
            ->setTicket($repoTicket->findOneBy(['id' => $id]));
            // TODO JEROME Modification du statut du ticket il est plus logique que cela se produise dans le commentaire      
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            
            $this->addFlash('message', 'Votre commentaire a bien été déposé');
            return $this->redirectToRoute('app_comment', ['id' => $id ]);
        }
        
        // Changement de statut du ticket
        
        $ticket = $repoTicket->findOneBy(['id' => $id]);
        $formEditStatut = $this->createForm(EditStatusTicketFormType::class, $ticket);
        $formEditStatut->handleRequest($request);

        if($formEditStatut->isSubmitted() && $formEditStatut->isValid()){
            
            $ticket = $formEditStatut->getData();
            $em->persist($ticket);
            $em->flush();
            $this->addFlash('message', 'Le Ticket a bien été déplacé');
            return $this->redirectToRoute('app_tickets');
        }

        // Envoyer par mail tous le contenu du ticket et des commentaires a un prestataire

        $formSendTicket = $this->createForm(SendTicketType::class);
        $formSendTicket->handleRequest($request);
        
        if($formSendTicket->isSubmitted() && $formSendTicket->isValid()){
            $data = $formSendTicket['prestataire']->getData();
            if ($data->getEmail()) {
                $commentsOfTicket = $repoComments->findBy(['ticket' => $id]);
                $email = (new Email())
                    ->from('intranet@groupe-axis.fr')
                    ->to($data->getEmail())
                    ->subject('Ticket ' . $ticket->getId() . ' : ' . $ticket->getTitle())
                    ->html($this->renderView('mails/sendMailToPrestataire.html.twig', ['Mail' => $formSendTicket->getData(), 'ticket' => $ticket, 'commentsOfTicket' => $commentsOfTicket]));
                $mailer->send($email);

                 // créer un commentaire pour sauvegarder les dates d'envois de mails
                 $comment = new Comments();
                 $dateTimeSend = new DateTime('NOW');
                 $commentSend = "Mail envoyer le " . $dateTimeSend->format('d-m-Y-H:i:s') . " à " . $data->getEmail();
                 $comment->setTitle($commentSend)
                         ->setContent('')
                         ->setTicket($repoTicket->findOneBy(['id' => $id]))
                         ->setUser($this->getUser())
                         ->setCreatedAt(new \DateTime());
                         $em = $this->getDoctrine()->getManager();
                         $em->persist($comment);
                         $em->flush();

                $this->addFlash('message', 'Message envoyé a ce prestataire!');
                    return $this->redirectToRoute('app_tickets');
            }else {
                $this->addFlash('danger', 'Pas d\'adresse mail, Impossible d\'envoyer a ce prestataire !');
                    return $this->redirectToRoute('app_tickets');
            }

        }

        // Envoyer par mail tous le contenu du ticket et des commentaires a un collégue

        $formSendAnnuaireTicket = $this->createForm(SendTicketAnnuaireType::class);
        $formSendAnnuaireTicket->handleRequest($request);
        
        if($formSendAnnuaireTicket->isSubmitted() && $formSendAnnuaireTicket->isValid()){
            $data = $formSendAnnuaireTicket['annuaire']->getData();
            if ($data->getMail()) {
                $commentsOfTicket = $repoComments->findBy(['ticket' => $id]);
                $email = (new Email())
                    ->from('intranet@groupe-axis.fr')
                    ->to($data->getMail())
                    ->subject('Ticket ' . $ticket->getId() . ' : ' . $ticket->getTitle())
                    ->html($this->renderView('mails/sendMailToPrestataire.html.twig', ['Mail' => $formSendAnnuaireTicket->getData(), 'ticket' => $ticket, 'commentsOfTicket' => $commentsOfTicket]));
                $mailer->send($email);

                // créer un commentaire pour sauvegarder les dates d'envois de mails
                $comment = new Comments();
                $dateTimeSend = new DateTime('NOW');
                $commentSend = "Mail envoyer le " . $dateTimeSend->format('d-m-Y-H:i:s') . " à " . $data->getMail();
                $comment->setTitle($commentSend)
                        ->setContent('')
                        ->setTicket($repoTicket->findOneBy(['id' => $id]))
                        ->setUser($this->getUser())
                        ->setCreatedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($comment);
                        $em->flush();
                $this->addFlash('warning', 'Message envoyé a ce collégue!');
                    return $this->redirectToRoute('app_tickets');
            }else {
                $this->addFlash('danger', 'Pas d\'adresse mail, Impossible d\'envoyer a ce collégue !');
                    return $this->redirectToRoute('app_tickets');
            }

        }





        return $this->render('comments/index.html.twig', [
            'ticket' => $ticket,
            'comments' => $comments,
            'formComment' => $formComment->createView(),
            'formstatu' => $formEditStatut->createView(),
            'formSendTicket' => $formSendTicket->createView(),
            'formSendAnnuaireTicket' => $formSendAnnuaireTicket->createView()
        ]);
    }

}
