<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\Mime\Email;
use App\Entity\Main\ListCmdTraite;
use App\Repository\Divalto\EntRepository;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\ListCmdTraiteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class OldCmdController extends AbstractController
{
    private $repoNumCmd;
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;

    public function __construct(MailListRepository $repoMail, ListCmdTraiteRepository $repoNumCmd,MailerInterface $mailer)
    {
        $this->repoNumCmd = $repoNumCmd;
        $this->mailer = $mailer;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];

        //parent::__construct();
    }

    /**
     * @Route("/old/cmd/deleteBy", name="app_list_delete_old_cmd")
     */
    public function listDelete(Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $commandesTraites = $this->repoNumCmd->findAll();

        return $this->render('old_cmd/ListDelete.html.twig', [
            'title' => 'Liste des suppressions Cmds',
            'commandesTraites' => $commandesTraites
        ]);
    }
    /**
     * @Route("/old/cmd", name="app_old_cmd")
     */
    public function show(EntRepository $repo, Request $request): Response
    {
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $roby = false;
        $lhermitte = false;
        $dos = '';
        $roles = $this->getUser()->getRoles();
        
        foreach ($roles as $role) {
            if ( strstr($role, 'ROBY') ) {
                $roby = true;
                $dos = '\'3\'';
                $dossier = 3;
                $numeros = $this->listNumeroCmd($dossier);
            }
            if ( strstr($role, 'LHERMITTE') ) {
                $lhermitte = true;
                $dos = '\'1\'';
                $dossier = 1;
                $numeros = $this->listNumeroCmd($dossier);
            }
        }
        if ($lhermitte == true && $roby == true ) {
            $dos = '\'1\',\'3\'';
            $numeros = 1;
        }

        $oldCmds = $repo->getOldCmds($dos, $numeros);
        $oldCmdsMouv = $repo->getOldCmdsMouv($dos, $numeros);
        
        return $this->render('old_cmd/index.html.twig', [
            'controller_name' => 'OldCmdController',
            'oldCmds' => $oldCmds,
            'oldCmdsMouvs' => $oldCmdsMouv,
            'title' => 'Vieilles Commandes actives'
        ]);
    }

    /**
     * @Route("/delete/old/cmd/{numero}/{dossier}", name="app_delete_old_cmd")
     */
    public function sendDelete(Request $request,$numero = null, $dossier = null ): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $lockCmd = new ListCmdTraite();
        $lockCmd->setNumero($numero)
                ->setCreatedAt(new DateTime())
                ->setTreatedBy($this->getUser())
                ->setDossier($dossier);
        $em = $this->getDoctrine()->getManager();
        $em->persist($lockCmd);
        $em->flush();

        // envoyer un mail
        $html = $this->renderView('mails/MailDeleteCmd.html.twig', ['lockCmd' => $lockCmd]);
        if ($lockCmd->getDossier() == 1) {
            $destinataire = $this->mailTreatement;
        }
        if ($lockCmd->getDossier() == 3) {
            $destinataire = 'ndegorre@roby-fr.com';
        }
        $email = (new Email())
        ->from($this->mailEnvoi)
        ->to($destinataire)
        ->subject('Message Intranet, merci de supprimer la commande ' . $lockCmd->getNumero() . ' pour le dossier ' . $lockCmd->getDossier())
        ->html($html);
        $this->mailer->send($email);

        $this->addFlash('message', 'Un mail a été envoyé pour demander la suppresion de cette commande, cette commande n\'apparaitra plus dans la liste, merci !');
        return $this->redirectToRoute('app_old_cmd');
    }

    public function listNumeroCmd($dossier){
        $numeros = '';
        $listNum = $this->repoNumCmd->findBy(['dossier' => $dossier]);
            $i =0;
            foreach ($listNum as $num) {
                if ($i == 0) {
                    $numeros = $num->getNumero();
                    $i = 1;
                }else {
                    $numeros = $numeros . ',' . $num->getNumero();
                }
            }
            return $numeros;
    }
}
