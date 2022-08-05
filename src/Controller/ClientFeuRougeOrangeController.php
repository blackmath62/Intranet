<?php

namespace App\Controller;

use DateTime;
use App\Form\AddEmailType;
use App\Entity\Main\MailList;
use App\Repository\Divalto\MouvRepository;
use Symfony\Component\Mime\Email;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientFeuRougeOrangeController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $repoMouv;
    private $mailTreatement;
    private $adminEmailController;

    public function __construct(MouvRepository $repoMouv, AdminEmailController $adminEmailController,MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        $this->adminEmailController = $adminEmailController;
        $this->repoMouv = $repoMouv;

        //parent::__construct();
    }

    /**
     * @Route("/client/feu/rouge/orange/send/mail", name="app_client_feu_rouge_orange_send_mail")
     */
    public function sendMail(): Response
    {   
       // envoyer un mail
       $treatementMails = $this->repoMail->findBy(['page' => 'app_admin_email', 'SecondOption' => 'feu']);
       $mails = $this->adminEmailController->formateEmailList($treatementMails); 
       $pieces = $this->repoMouv->getCmdBlClientFeuRougeOrange();
       if ($pieces) {
           $html = $this->renderView('mails/piecesClientFeuRougeOrange.html.twig', ['pieces' => $pieces]);
           $email = (new Email())
           ->from($this->mailEnvoi)
           ->to(...$mails)
           ->subject('Liste des commandes et BLs de la veille pour les clients feux rouges et oranges')
           ->html($html);
           $this->mailer->send($email);
        }

       return $this->redirectToRoute('app_home');
    }
}