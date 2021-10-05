<?php

namespace App\Controller;

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

class TestController extends AbstractScheduledTask
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        //parent::__construct();
    }
    
   
        protected function initialize(Schedule $schedule) {
          $schedule
            ->minutes(1)
            ->everyHours(0); // Perform the task every 5 hours on minute 0
            
          // Or if you want to perform your task at midnight every day
          // $schedule->minutes(0)->hours(0)->daily();
          
          // Or schedule your task to run once at 9AM daily (this is effectively the same as daily() above)
          // $schedule->minutes(0)->hours(9);
        }
      
        public function run() {
            return $this->sendMail();
        }
      

    /**
     * @Route("/test", name="app_test")
     */
    public function index()
    {
        
        return $this->sendMail();
        
    }
    public function sendMail(){
                
                $email = (new Email())
                    ->from('intranet@groupe-axis.fr')
                    ->to('jpochet@lhermitte.fr')
                    ->subject('Je suis en train de tester les commandes')
                    ->html('<p>Bonjour Jérôme, est ce que tu a bien reçu ce mail ?</p>');
                $this->mailer->send($email);
    }
}
