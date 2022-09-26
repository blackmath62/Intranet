<?php

namespace App\Controller;

use App\Form\MailingType;
use Symfony\Component\Mime\Email;
use App\Repository\Divalto\FouRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaillingController extends AbstractController
{
    /**
     * @Route("/admin/mailling", name="app_mailling")
     */
    public function index(MailerInterface $mailer,FouRepository $repo, Request $request): Response
    {
        
        $form = $this->createForm(MailingType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $fouMails = $repo->getAllMailFournisseur($form->getData()['dossier']);
            
            $mails = [];
            // on fait une liste des mails présents dans les champs mail fournisseur, web et mail contact
            foreach ($fouMails as $value) {
                if ($value['fouMail'] <> '') {
                    array_push( $mails, str_replace(' ', '', strtolower($value['fouMail'])) );
                }
                if ($value['web'] <> '') {
                    array_push( $mails, str_replace(' ', '', strtolower($value['web'])) );
                }
                if ($value['contactMail']) {
                    array_push( $mails, str_replace(' ', '', strtolower($value['contactMail'])) );
                }
            }
            // suppresion des doublons
            $mails = array_unique($mails);
            
            // on repére les champs qui contiennent des ; ou des , pour les scinder
            foreach ($mails as $value) {
                if (strstr($value, ';')) {
                    $explode = explode(';',$value);
                    foreach ($explode as $val) {
                        array_push( $mails, $val );
                    }
                    unset($mails[array_search($value, $mails)]);
                }
            }
            $objet = $form->getData()['objet'];
            $message = $form->getData()['message'];
            $de = $form->getData()['de'];
   
            $email = (new Email())
            ->from($de)
            ->to('jpochet@groupe-axis.fr')
            ->priority(Email::PRIORITY_HIGH)
            ->subject($objet)
            ->html($message);
    
            $mailer->send($email);
        }

        return $this->render('mailling/index.html.twig', [
            'title' => 'Mailing',
            'form' => $form->createView()
        ]);
    }

}
