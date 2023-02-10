<?php

namespace App\Controller;

use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\FAQRepository;
use App\Repository\Main\MailListRepository;
use Com\Tecnick\Barcode\Barcode;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */

class PdfController extends AbstractController
{
    private $repoMail;
    private $mailEnvoi;

    public function __construct(MailListRepository $repoMail)
    {
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        //parent::__construct();
    }

    /**
     * @Route("/pdf/faq/{id}", name="app_send_pdf_faq")
     */

    public function SendFaqPdf($id, MailerInterface $mailer, Pdf $pdf, FAQRepository $repo): Response
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
            ->attach($pdf, 'Tutoriel' . $faq->getTitle() . '.pdf');
        $mailer->send($email);

        $this->addFlash('message', 'Le Tutoriel vous a été envoyé par mail en version PDF !');
        return $this->redirectToRoute('app_faq');

    }

    /**
     * @Route("/pdf/etiquette/{ean}", name="app_send_pdf_etiquette")
     */

    public function SendEtiquettePdf($ean, MailerInterface $mailer, Pdf $pdf, ArtRepository $repo): Response
    {

        $produit = '';
        $dos = 1;
        // instantiate the barcode class
        $barcode = new Barcode();

        // generate a barcode
        $bobj = $barcode->getBarcodeObj(
            'C128', // barcode type and additional comma-separated parameters
            $ean, // data string to encode
            250, // bar width (use absolute or negative value as multiplication factor)
            50, // bar height (use absolute or negative value as multiplication factor)
            'black', // foreground color
            array(0, 0, 0, 10) // padding (use absolute or negative values as multiplication factors)
        )->setBackgroundColor('white'); // background color

        // output the barcode as HTML div (see other output formats in the documentation and examples)
        //echo $bobj->getHtmlDiv();

        $produit = $repo->getEanStock($dos, $ean);
        //dd($produit);
        $htmlPdf = $this->renderView('pdf/pdfEtiquette.html.twig', ['produit' => $produit, 'ean' => $bobj->getHtmlDiv()]);
        $html = $this->renderView('mails/MailTest.html.twig');
        $test = new pdf($this->renderView('pdf/pdfEtiquette.html.twig', ['produit' => $produit, 'ean' => $bobj->getHtmlDiv()]));
        $pdf->setOption('page-height', '40mm');
        $pdf->setOption('page-width', '90mm');
        $pdf->setOption('margin-bottom', 2);
        $pdf->setOption('margin-left', 2);
        $pdf->setOption('margin-right', 2);
        $pdf->setOption('margin-top', 2);
        $pdf->setOption('header-font-size', 10);
        /*
        $pdf->setOption('page-size', 10);
         */
        //echo $pdf->getOutput($htmlPdf);
        $pdf = $pdf->getOutputFromHtml($htmlPdf);
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to('jpochet@groupe-axis.fr')
            ->subject('Etiquette')
            ->html($html)
            ->attach($pdf, 'Etiquette');
        $mailer->send($email);

        $this->addFlash('message', 'L\'étiquette a été envoyé !');
        return $this->redirectToRoute('app_scan_emplacement_print');

    }

}
