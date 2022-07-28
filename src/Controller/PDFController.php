<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\Main\Users;
use Symfony\Component\Mime\Email;
use App\Repository\Main\FAQRepository;
use App\Repository\Main\MailListRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class PdfController extends AbstractController
{
    private $repoMail;
    private $mailEnvoi;

    public function __construct(MailListRepository $repoMail)
    {
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        //parent::__construct();
    }

    /**
     * @Route("/pdf/faq/{id}", name="app_send_pdf_faq")
     */

    public function SendFaqPdf($id , MailerInterface $mailer, Pdf $pdf, FAQRepository $repo): Response
    {
        
            $faq = $repo->findOneBy(['id' => $id]);
            $user = $this->getUser()->getPseudo();
            $htmlPdf = $this->renderView('pdf/pdfFaq.html.twig', ['faq' => $faq]);
            $html = $this->renderView('mails/MailFaq.html.twig', ['faq' => $faq, 'user' => $user]);
            $pdf = $pdf->getOutputFromHtml($htmlPdf);
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($this->getUser()->getEmail())
                ->subject('Tutoriel PDF : ' . $faq->getTitle())
                ->html($html)
                ->attach($pdf, 'Tutoriel'. $faq->getTitle() .'.pdf');
            $mailer->send($email);

            $this->addFlash('message', 'Le Tutoriel vous a été envoyé par mail en version PDF !');
            return $this->redirectToRoute('app_faq');

    }

}