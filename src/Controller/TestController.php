<?php

namespace App\Controller;

use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use DateTime;
use DateTimeZone;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Rewieer\TaskSchedulerBundle\Task\Schedule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Rewieer\TaskSchedulerBundle\Task\AbstractScheduledTask;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class TestController extends AbstractController
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        //parent::__construct();
    }      

    /**
     * @Route("/test", name="app_test")
     */
    public function test_tiers_mal_renseigne()
    {
        
        return $this->render('test/index.html.twig',[
            'title' => 'page de test',
        ]);
        
    }

    public function test_de_date()
    {
        $dateDuJour = new DateTime();
                
        //$dateDuJour = date_modify($dateDuJour, '+1 day');
        $dateDuJour  = $dateDuJour->format('d-m-Y');

        if ($this->isWeekend($dateDuJour) == false) {
            $texte = 'Nous ne sommes pas le Week-end, au boulot ! le ' . $dateDuJour;
        }else {
            $texte = 'Nous sommes le Week-end, youpi ! le ' . $dateDuJour;
        } 
        return $this->render('test/index.html.twig',[
            'title' => 'page de test',
            'texte' => $texte,
        ]);
        
    }

    function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }
    
}
