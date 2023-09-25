<?php

namespace App\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

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

    public function test_tiers_mal_renseigne()
    {

        return $this->render('test/index.html.twig', [
            'title' => 'page de test',
        ]);

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

}
