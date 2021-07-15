<?php

namespace App\Controller;

use DateTime;
use App\Form\TicketsType;
use App\Entity\Main\Tickets;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Main\StatusRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\Main\TicketsRepository;
use App\Repository\Main\CommentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @IsGranted("ROLE_USER")
 */

class TicketsController extends AbstractController
{
    
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct( EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    
    /**
     * @Route("/tickets", name="app_tickets")
     */
    public function tickets_Show(TicketsRepository $repo, Request $request, SluggerInterface $slugger, CommentsRepository $repoComment, StatusRepository $repoStatus)
    {
        $tickets = $repo->findAllExceptStatu(17);
        $comments = $repoComment->findAllExceptStatu(17);
        $status = $repoStatus->findAll();
        // Formulaire du Ticket
        
        $form = $this->createForm(TicketsType::class);
        $form->handleRequest($request);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
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
            'title' => 'Tickets',
            'formTickets' => $form->createView()
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

    /**
     * @Route("/export",  name="app_export")
     */
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
            $sheet->fromArray($this->getData(),null, 'A2', true);
        

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $d = new DateTime('NOW');
        $dateTime = $d->format('Ymd-His') ;
        $fileName = 'ticket' . $dateTime . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
  
        //return $this->redirectToRoute('app_tickets');
    }
}
