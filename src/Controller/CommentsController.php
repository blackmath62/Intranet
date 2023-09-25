<?php

namespace App\Controller;

use App\Entity\Main\Comments;
use App\Form\CommentsTicketsType;
use App\Form\EditStatusTicketFormType;
use App\Form\SendTicketAnnuaireType;
use App\Form\SendTicketType;
use App\Repository\Main\CommentsRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\StatusRepository;
use App\Repository\Main\TicketsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]

class CommentsController extends AbstractController
{
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, MailListRepository $repoMail)
    {
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/ticket/comment/add/{id<\d+>}", name: "app_comment")]
    #[ParamConverter("Comments", options: ["id" => "Ticket_id"])]

    public function addComment(int $id, Pdf $pdf, StatusRepository $repoStatut, MailerInterface $mailer, TicketsRepository $repoTicket, CommentsRepository $repoComments, Request $request, EntityManagerInterface $em)
    {
        // Enregistrement des commentaires

        $comments = $repoComments->findBy(['ticket' => $id]);
        $formComment = $this->createForm(CommentsTicketsType::class);
        $formComment->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment = new Comments();
            $comment = $formComment->getData();
            $comment->setCreatedAt(new \DateTime())
            // TODO JEROME pouvoir ajouter plusieurs piéces jointes
            //->setFiles($id)
                ->setUser($this->getUser())
                ->setTicket($repoTicket->findOneBy(['id' => $id]));
            // TODO JEROME Modification du statut du ticket il est plus logique que cela se produise dans le commentaire
            $em = $this->entityManager;
            $em->persist($comment);
            $em->flush();

            $ticket = $repoTicket->findOneBy(['id' => $id]);
            $ticket->setModifiedAt(new \DateTime());
            $em = $this->entityManager;
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('message', 'Votre commentaire a bien été déposé');
            return $this->redirectToRoute('app_comment', ['id' => $id]);
        }

        // Changement de statut du ticket

        $ticket = $repoTicket->findOneBy(['id' => $id]);
        $formEditStatut = $this->createForm(EditStatusTicketFormType::class, $ticket);
        $formEditStatut->handleRequest($request);

        if ($formEditStatut->isSubmitted() && $formEditStatut->isValid()) {

            $ticket = $formEditStatut->getData();
            $ticket->setModifiedAt(new \DateTime());
            $em->persist($ticket);
            $em->flush();

            // créer un commentaire pour sauvegarder les dates d'envois de mails
            $comment = new Comments();
            $dateTimeSend = new DateTime('NOW');
            $commentSend = "Le ticket est maintenant " . $ticket->getStatu()->getTitle();
            $comment->setTitle($commentSend)
                ->setContent('')
                ->setTicket($repoTicket->findOneBy(['id' => $id]))
                ->setUser($this->getUser())
                ->setCreatedAt(new \DateTime());
            $em = $this->entityManager;
            $em->persist($comment);
            $em->flush();

            $this->addFlash('message', 'Le Ticket a bien été déplacé');
            return $this->redirectToRoute('app_tickets');
        }

        // Envoyer par mail tous le contenu du ticket et des commentaires a un prestataire

        $formSendTicket = $this->createForm(SendTicketType::class);
        $formSendTicket->handleRequest($request);

        if ($formSendTicket->isSubmitted() && $formSendTicket->isValid()) {

            $data = $formSendTicket['prestataire']->getData();
            if ($data->getEmail()) {
                $commentsOfTicket = $repoComments->findBy(['ticket' => $id]);
                $html = $this->renderView('mails/sendMailToPrestataire.html.twig', ['Mail' => $formSendTicket->getData(), 'ticket' => $ticket, 'commentsOfTicket' => $commentsOfTicket]);
                $pdf = $pdf->getOutputFromHtml($html);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($data->getEmail())
                    ->subject('Ticket ' . $ticket->getId() . ' : ' . $ticket->getTitle() . " => " . $ticket->getStatu()->getTitle())
                    ->html($html)
                    ->attach($pdf, 'Ticket ' . $ticket->getId() . ' : ' . $ticket->getTitle() . '.pdf');
                $mailer->send($email);

                // assignation du prestataire aprés l'envoi du mail au prestataire

                $ticket = $repoTicket->findOneBy(['id' => $id]);
                $ticket->setPrestataire($data);
                $ticket->setModifiedAt(new \DateTime());
                $statuEnCours = $repoStatut->findOneBy(['id' => 16]);
                $ticket->setStatu($statuEnCours);
                $em = $this->entityManager;
                $em->persist($ticket);
                $em->flush();

                // créer un commentaire pour sauvegarder les dates d'envois de mails
                $comment = new Comments();
                $dateTimeSend = new DateTime('NOW');
                $commentSend = "Mail envoyer le " . $dateTimeSend->format('d-m-Y-H:i:s') . " à " . $data->getEmail();
                $comment->setTitle($commentSend)
                    ->setContent('')
                    ->setTicket($repoTicket->findOneBy(['id' => $id]))
                    ->setUser($this->getUser())
                    ->setCreatedAt(new \DateTime());
                $em = $this->entityManager;
                $em->persist($comment);
                $em->flush();

                $this->addFlash('message', 'Message envoyé a ce prestataire!');
                return $this->redirectToRoute('app_tickets');
            } else {
                $this->addFlash('danger', 'Pas d\'adresse mail, Impossible d\'envoyer a ce prestataire !');
                return $this->redirectToRoute('app_tickets');
            }

        }

        // Envoyer par mail tous le contenu du ticket et des commentaires a un collégue

        $formSendAnnuaireTicket = $this->createForm(SendTicketAnnuaireType::class);
        $formSendAnnuaireTicket->handleRequest($request);

        if ($formSendAnnuaireTicket->isSubmitted() && $formSendAnnuaireTicket->isValid()) {
            $data = $formSendAnnuaireTicket['annuaire']->getData();
            if ($data->getMail()) {
                $commentsOfTicket = $repoComments->findBy(['ticket' => $id]);
                $html = $this->renderView('mails/sendMailToPrestataire.html.twig', ['Mail' => $formSendAnnuaireTicket->getData(), 'ticket' => $ticket, 'commentsOfTicket' => $commentsOfTicket]);
                $pdf = $pdf->getOutputFromHtml($html);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($data->getMail())
                    ->subject('Ticket ' . $ticket->getId() . ' : ' . $ticket->getTitle() . " => " . $ticket->getStatu()->getTitle())
                    ->html($html)
                    ->attach($pdf, 'Ticket ' . $ticket->getId() . ' : ' . $ticket->getTitle() . '.pdf');
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
                $em = $this->entityManager;
                $em->persist($comment);
                $em->flush();
                $this->addFlash('warning', 'Message envoyé a ce collégue!');
                return $this->redirectToRoute('app_tickets');
            } else {
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
            'title' => 'Ajout de Commentaires',
            'formSendAnnuaireTicket' => $formSendAnnuaireTicket->createView(),
        ]);
    }

}
