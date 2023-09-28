<?php

namespace App\Controller;

use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\FAQRepository;
use App\Repository\Main\MailListRepository;
use Com\Tecnick\Barcode\Barcode;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class PDFController extends AbstractController
{
    private $repoMail;
    private $mailEnvoi;

    public function __construct(MailListRepository $repoMail)
    {
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        //parent::__construct();
    }

    #[Route("/pdf/faq/{id}", name: "app_send_pdf_faq")]

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

    #[Route("/pdf/etiquette/{ean}", name: "app_send_pdf_etiquette")]

    public function SendEtiquettePdf($ean, Pdf $pdf, ArtRepository $repo): Response
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
            30, // bar height (use absolute or negative value as multiplication factor)
            'black', // foreground color
            array(0, 0, 0, 10) // padding (use absolute or negative values as multiplication factors)
        )->setBackgroundColor('white'); // background color

        // output the barcode as HTML div (see other output formats in the documentation and examples)
        //echo $bobj->getHtmlDiv();

        $produit = $repo->getEanStock($dos, $ean);
        //dd($produit);
        $htmlPdf = $this->renderView('pdf/pdfEtiquette.html.twig', ['produit' => $produit, 'ean' => $bobj->getHtmlDiv()]);
        $pdf->setOption('page-height', '40mm');
        $pdf->setOption('page-width', '90mm');
        $pdf->setOption('margin-bottom', 1);
        $pdf->setOption('margin-left', 1);
        $pdf->setOption('margin-right', 1);
        $pdf->setOption('margin-top', 1);
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('print-media-type', true);
        //$pdf->setOption('header-font-size', 10);
        $pdf->setOption('zoom', false);
        $file = 'C:/wamp64/www/Intranet/bin/' . $ean . '.pdf';
        @unlink($file);
        $pdf->generateFromHtml($htmlPdf, $file);
        //$this->runPowerShell();

        $this->addFlash('message', 'Ajouté à la file d\'attente d\'impression !');
        return $this->redirectToRoute('app_scan_emplacement_print');

    }

    #[Route("/emplacement/pdf/etiquette/{dos}/{empl1}/{empl2}", name: "app_send_pdf_etiquette_emplacement")]

    public function SendEtiquetteEmplPdf($dos, $empl1, $empl2, Pdf $pdf, ArtRepository $repo): Response
    {

        $emplacements = $repo->gettrancheEmpl($dos, $empl1, $empl2);
        // instantiate the barcode class
        foreach ($emplacements as $empl) {
            $barcode = new Barcode();

            // generate a barcode
            $bobj = $barcode->getBarcodeObj(
                'C128', // barcode type and additional comma-separated parameters
                $empl['empl'], // data string to encode
                250, // bar width (use absolute or negative value as multiplication factor)
                60, // bar height (use absolute or negative value as multiplication factor)
                'black', // foreground color
                array(0, 0, 0, 10) // padding (use absolute or negative values as multiplication factors)
            )->setBackgroundColor('white'); // background color

            // output the barcode as HTML div (see other output formats in the documentation and examples)
            //echo $bobj->getHtmlDiv();

            $htmlPdf = $this->renderView('pdf/pdfEtiquetteEmpl.html.twig', ['empl' => $empl, 'ean' => $bobj->getHtmlDiv()]);
            $pdf->setOption('page-height', '40mm');
            $pdf->setOption('page-width', '90mm');
            $pdf->setOption('margin-bottom', 1);
            $pdf->setOption('margin-left', 1);
            $pdf->setOption('margin-right', 1);
            $pdf->setOption('margin-top', 8);
            $pdf->setOption('orientation', 'Portrait');
            $pdf->setOption('print-media-type', true);
            $pdf->setOption('zoom', false);
            $file = 'C:/wamp64/www/Intranet/bin/' . $empl['empl'] . '.pdf';
            @unlink($file);
            $pdf->generateFromHtml($htmlPdf, $file);
            //$file = 'C:\wamp64\www\Intranet\bin\ean.jpg';
            //$img->generateFromHtml($htmlPdf, $file);
        }
        //$this->runPowerShell();
        $this->addFlash('message', 'Ajouté à la file d\'attente d\'impression !');
        return $this->redirectToRoute('app_print_empl');

    }

    #[Route("/power/shell", name: "app_power_shell")]

    public function runPowerShell(): Response
    {

        $route = 'C:\wamp64\www\Intranet\bin\printEtiquette.ps1';
        //$route = 'C:\wamp64\www\Intranet\bin\defautPrint.ps1';
        $process = new Process(['powershell', $route]);
        $process->mustRun();
        // executes after the command finishes
        //dd($process->getOutput());
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $this->redirectToRoute('app_print_empl');
    }
    //dd($process->getOutput());
    // Fonctionne en partie, supprime mais n'imprime pas
    //$output = shell_exec('powershell -executionpolicy Unrestricted -command "& {"C:\wamp64\www\Intranet\bin\print.ps1"; exit $err}"');
    //$output = fopen('powershell -executionpolicy Unrestricted -command "& {"C:\wamp64\www\Intranet\bin\print.ps1"; exit $err}"', 'r');

    //$this->addFlash('success', $output);
    //print_r($output);
    //echo $output;
    //$output = shell_exec($route);
    //$output = shell_exec('C:\Users\WEBSRV.AXIS\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Windows PowerShell\powershell.exe -executionpolicy remotesigned -command "& {"C:\wamp64\www\Intranet\bin\print.ps1"; exit $err}"');
    //$output = shell_Exec('powershell.exe -executionpolicy Unrestricted -Force "C:\wamp64\www\Intranet\bin\print.ps1"');
    //$output = shell_Exec(' C:\Users\WEBSRV.AXIS\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Windows PowerShell\powershell.exe -InputFormat none -File -ExecutionPolicy ByPass -NoProfile -Command "& { . \"C:/wamp64/www/Intranet/bin/print.ps1"; }" ');

}
