<?php
namespace App\Service;

use App\Entity\Main\MailList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailTreatementService
{
    private $mailer;
    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    public function sendMail(string $subjet, $mails, $donnees = null, string $pageUrl = null, array $urlFiles = null)
    {
        $repository = $this->entityManager->getRepository(MailList::class);
        if ($mails) {
            if ($donnees) {
                $html = $this->twig->render($pageUrl, ['donnees' => $donnees]);
            } else {
                $html = $this->twig->render($pageUrl);
            }
            $email = (new Email())
                ->from($repository->getEmailEnvoi())
                ->to(...$mails)
                ->subject($subjet)
                ->html($html);
            $this->mailer->send($email);
        }
    }

    //$addMails = ['xdupire@lhermitte.fr', 'bgovaere@lhermitte.fr'];

    public function treatementMails(string $page, string $secondOption = null, array $addMails = null)
    {

        $repository = $this->entityManager->getRepository(MailList::class);

        // les mails de traitement sont les mails d'envois paramétrés sur les différentes pages
        if ($secondOption == null) {
            $mails = $repository->findBy(['page' => $page]);
        } else {
            $mails = $repository->findBy(['page' => $page, 'secondOption' => $secondOption]);
        }
        // on ajoute les emails additionnels dans le tableau
        $mails = $this->addEmailsToArray($mails, $addMails);
        // on formate les adresses pour permettre l'envoi avec Mailer
        $mails = $this->formateEmailList($mails);
        return $mails;
    }

    public function addEmailsToArray(array $emailArray, array $newEmails = null)
    {
        if ($newEmails) {
            // Convertir les objets MailList en adresses e-mail
            $newEmails = array_merge($newEmails, array_map(function (MailList $mailList) {
                return $mailList->getEmail();
            }, $emailArray));
            return $newEmails;
        } else {
            $emails = [];
            foreach ($emailArray as $mail) {
                array_push($emails, $mail->getEmail());
            }
            return $emails;
        }

    }

    public function formateEmailList($listMails)
    {
        $MailsList = [];
        foreach ($listMails as $value) {
            array_push($MailsList, new Address($value, substr($value, 0, strpos($value, '@'))));
        }
        return $MailsList;
    }

}
