<?php

namespace App\Controller;

use DateTime;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]

class TestController extends AbstractController
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        //parent::__construct();
    }

    #[Route("/test", name: "app_test")]

    public function test()
    {

        // Assurez-vous que wkhtmltopdf est correctement configuré
        $pdf = new Pdf('C:\Program Files (x86)\wkhtmltopdf\bin\wkhtmltopdf.exe');
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        // Chemin vers le bureau de l'utilisateur actuel
        $desktopPath = 'C:\Users\\' . getenv("USERNAME") . '\Desktop\output.pdf';
        $pdf->generateFromHtml('<h1>Hello World</h1>', $desktopPath);

        return $this->render('test/info.php');

    }

    public function test_de_date()
    {
        $dateDuJour = new DateTime();

        //$dateDuJour = date_modify($dateDuJour, '+1 day');
        $dateDuJour = $dateDuJour->format('d-m-Y');

        if ($this->isWeekend($dateDuJour) == false) {
            $texte = 'Nous ne sommes pas le Week-end, au boulot ! le ' . $dateDuJour;
        } else {
            $texte = 'Nous sommes le Week-end, youpi ! le ' . $dateDuJour;
        }
        return $this->render('test/index.html.twig', [
            'title' => 'page de test',
            'texte' => $texte,
        ]);

    }

    public function isWeekend($date)
    {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }

    #[Route("/test/excel", name: "app_test_excel")]

    public function test_excel(MailerInterface $mailer)
    {
        $fileName = 'excel.xlsx';

        $email = (new Email())
            ->from('intranet@groupe-axis.fr')
            ->to($this->getUser()->getEmail())
            ->subject('test envoi excel')
            ->html('Ceci est un test')
            ->attachFromPath($temp_file, $fileName);

        // Envoyez l'e-mail avec le fichier Excel en pièce jointe
        $mailer->send($email);

        return $this->redirectToRoute('app_test');

    }

}
