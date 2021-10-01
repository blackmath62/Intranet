<?php

namespace App\Controller;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
