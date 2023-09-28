<?php

namespace App\Controller;

use App\Controller\AdminEmailController;
use App\Entity\Main\Tickets;
use App\Form\TicketsType;
use App\Repository\Main\CommentsRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\StatusRepository;
use App\Repository\Main\TicketsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("ROLE_USER")]

class TicketsController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;

    public function __construct(
        AdminEmailController $adminEmailController,
        ManagerRegistry $registry,
        MailerInterface $mailer,
        MailListRepository $repoMail
    ) {

        $this->entityManager = $registry->getManager();
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
    }

    #[Route("/tickets", name: "app_tickets")]
    #[Route("/tickets/resolus", name: "app_tickets_resolus")]

    public function getListTickets(TicketsRepository $repo, Request $request, SluggerInterface $slugger, CommentsRepository $repoComment, StatusRepository $repoStatus)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);
        if ($tracking == 'app_tickets') {
            $tickets = $repo->findAllExceptStatu(17);
            $comments = $repoComment->findAllExceptStatu(17);
        }
        if ($tracking == 'app_tickets_resolus') {
            $tickets = $repo->findBy(['statu' => 17]);

            $comments = $repoComment->findAllExceptStatu(14, 16, 18, 19);
        }
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
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

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

            $em = $this->entityManager;
            $em->persist($ticket);
            $em->flush();

            $treatementMails = $this->repoMail->findBy(['page' => 'app_admin_email', 'SecondOption' => 'traitement']);
            $mails = $this->adminEmailController->formateEmailList($treatementMails);
            $html = $this->renderView('mails/sendMailNewTicket.html.twig', ['ticket' => $ticket]);
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to(...$mails)
                ->subject('Un nouveau ticket à été créé sur le site intranet: ' . $ticket->getTitle())
                ->html($html);
            $this->mailer->send($email);

            $this->addFlash('message', 'Ticket créé avec succés');
            return $this->redirectToRoute('app_tickets');
        }

        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'comments' => $comments,
            'status' => $status,
            'title' => 'Tickets',
            'formTickets' => $form->createView(),
        ]);
    }

    // Export Excel

    private function getData(): array
    {
        /**
         * @var $ticket Ticket[]
         */
        $list = [];
        $tickets = $this->entityManager->getRepository(Tickets::class)->findAll();

        foreach ($tickets as $ticket) {
            $list[] = [
                $ticket->getCreatedAt(),
                'Ticket' . $ticket->getId() . ' : ' . $ticket->getTitle(),
                $ticket->getService()->getTitle(),
                $ticket->getStatu()->getTitle(),
                $ticket->getPrestataire()->getNom(),

            ];
        }
        return $list;
    }

    #[Route("/export", name: "app_export")]

    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Tickets List');
        // Entête de colonne
        $sheet->getCell('A1')->setValue('Date d\'ouverture');
        $sheet->getCell('B1')->setValue('Titre');
        $sheet->getCell('C1')->setValue('Service');
        $sheet->getCell('D1')->setValue('Statut');
        $sheet->getCell('E1')->setValue('Prestataire');

        // Increase row cursor after header write
        $sheet->fromArray($this->getData(), null, 'A2', true);

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $d = new DateTime('NOW');
        $dateTime = $d->format('Ymd-His');
        $fileName = 'ticket' . $dateTime . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
}
