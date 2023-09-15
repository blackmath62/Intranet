<?php

namespace App\Controller;

use App\Controller\AdminEmailController;
use App\Entity\Main\CmdRobyDelaiAccepteReporte;
use App\Entity\Main\MailList;
use App\Entity\Main\Note;
use App\Form\AddEmailType;
use App\Form\NoteType;
use App\Repository\Divalto\EntRepository;
use App\Repository\Main\CmdRobyDelaiAccepteReporteRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\NoteRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ROBY")
 */

class CmdRobyDelaiAccepteReporteController extends AbstractController
{

    private $mailer;
    private $cmdRoby;
    private $Users;
    private $entete;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, AdminEmailController $adminEmailController, EntRepository $entete, UsersRepository $Users, CmdRobyDelaiAccepteReporteRepository $cmdRoby, MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->cmdRoby = $cmdRoby;
        $this->Users = $Users;
        $this->entete = $entete;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    /**
     * @Route("/Roby/cmd/delai/accepte/reporte/active", name="app_cmd_roby_delai_accepte_reporte_active")
     * @Route("/Roby/cmd/delai/accepte/reporte/close", name="app_cmd_roby_delai_accepte_reporte_close")
     */
    // voir les commandes en cours ou celles terminées
    public function show(CmdRobyDelaiAccepteReporteRepository $repo, NoteRepository $repoNotes, Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        // initialisation de mes variables
        if ($request->attributes->get('_route') == 'app_cmd_roby_delai_accepte_reporte_active') {
            $commandes = $repo->findBy(['statut' => 'en cours ...']);
            $view = 'cmd_roby_delai_accepte_reporte/index.html.twig';
        } elseif ($request->attributes->get('_route') == 'app_cmd_roby_delai_accepte_reporte_close') {
            $commandes = $repo->findBy(['statut' => 'Terminé']);
            $view = 'cmd_roby_delai_accepte_reporte/cmdClose.html.twig';
        }

        unset($form);
        $form = $this->createForm(AddEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $find = $this->repoMail->findBy(['email' => $form->getData()['email'], 'page' => $tracking]);
            if (empty($find) | is_null($find)) {
                $mail = new MailList();
                $mail->setCreatedAt(new DateTime())
                    ->setEmail($form->getData()['email'])
                    ->setPage($tracking);
                $em = $this->entityManager;
                $em->persist($mail);
                $em->flush();
            } else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                return $this->redirectToRoute('app_cmd_roby_delai_accepte_reporte_active');
            }
        }

        $countNotes = $repoNotes->getCountNoteByCmd();
        $total = $repo->getTotalHt();
        //dd($total);
        return $this->render($view, [
            'controller_name' => 'CmdRobyDelaiAccepteReporteController',
            'title' => 'Commandes Actives Roby',
            'commandes' => $commandes,
            'countNotes' => $countNotes,
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            'form' => $form->createView(),
            'total' => $total,
        ]);
    }

    /**
     * @Route("/Roby/cmd/status/close/{id}", name="app_cmd_roby_status_close")
     * @Route("/Roby/cmd/status/active/{id}", name="app_cmd_roby_status_active")
     */
    // modifier le statut des commandes sur l'intranet
    public function setStatus($id, CmdRobyDelaiAccepteReporteRepository $repo, Request $request): Response
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        // initialisation de mes variables
        $commande = $repo->findOneBy(['id' => $id]);
        if ($request->attributes->get('_route') == 'app_cmd_roby_status_close') {
            $commande->setModifiedBy($this->getUser())
                ->setModifiedAt(new DateTime)
                ->setStatut('Terminé');
        } elseif ($request->attributes->get('_route') == 'app_cmd_roby_status_active') {
            $commande->setModifiedBy(null)
                ->setModifiedAt(null)
                ->setStatut('en cours ...');
        }

        $em = $this->entityManager;
        $em->persist($commande);
        $em->flush();

        $this->addFlash('message', 'Le statut de cette commande a bien été modifié');
        return $this->redirectToRoute('app_cmd_roby_delai_accepte_reporte_active');
    }

    /**
     * @Route("/Roby/cmd/update", name="app_cmd_roby_update")
     */
    // mettre à jour les commandes du systéme sur l'intranet
    public function runUpdate(CmdRobyDelaiAccepteReporteRepository $cmdRoby, EntRepository $entete, Request $request): Response
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $nbeCmdBefore = count($cmdRoby->findBy(['statut' => 'en cours ...']));

        $donnees = $entete->majCmdsRobyDelaiAccepteReporte();

        for ($lig = 0; $lig < count($donnees); $lig++) {
            $id = $donnees[$lig]['Identification'];
            $search = $cmdRoby->findOneBy(['identification' => $id]);
            // si elle n'existe pas, on la créér
            if (empty($search)) {
                $listCmd = new CmdRobyDelaiAccepteReporte();
                $listCmd->setIdentification($donnees[$lig]['Identification']);
                $listCmd->setStatut('en cours ...');
                $listCmd->setCreatedAt(new DateTime);
                $listCmd->setTiers($donnees[$lig]['Tiers']);
                $listCmd->setNom($donnees[$lig]['Nom']);
                $listCmd->setHt($donnees[$lig]['ht']);
                $listCmd->setTel($donnees[$lig]['Tel']);
                $listCmd->setCmd($donnees[$lig]['Cmd']);
                $listCmd->setDateCmd(new DateTime($donnees[$lig]['DateCmd']));
                $listCmd->setNotreRef($donnees[$lig]['NotreRef']);
                if ($donnees[$lig]['DelaiAccepte']) {
                    $listCmd->setDelaiAccepte(new DateTime($donnees[$lig]['DelaiAccepte']));
                } else {
                    $listCmd->setDelaiAccepte(null);
                }
                if ($donnees[$lig]['DelaiReporte']) {
                    $listCmd->setDelaiReporte(new DateTime($donnees[$lig]['DelaiReporte']));
                } else {
                    $listCmd->setDelaiReporte(null);
                }
            } elseif (!is_null($search)) {
                $listCmd = $search;
                $listCmd->setNotreRef($donnees[$lig]['NotreRef']);
                $listCmd->setTel($donnees[$lig]['Tel']);
                $listCmd->setHt($donnees[$lig]['ht']);
                if ($donnees[$lig]['DelaiAccepte']) {
                    $listCmd->setDelaiAccepte(new DateTime($donnees[$lig]['DelaiAccepte']));
                } else {
                    $listCmd->setDelaiAccepte(null);
                }
                if ($donnees[$lig]['DelaiReporte']) {
                    $listCmd->setDelaiReporte(new DateTime($donnees[$lig]['DelaiReporte']));
                } else {
                    $listCmd->setDelaiReporte(null);
                }
            }
            $em = $this->entityManager;
            $em->persist($listCmd);
            $em->flush();
        }

        $nbeCmdAfter = count($cmdRoby->findBy(['statut' => 'en cours ...']));
        $nbre = $nbeCmdBefore - $nbeCmdAfter;
        $texte = '';
        if ($nbre == 1) {
            $texte = ' commande a été ajoutée';
        } elseif ($nbre == 0) {
            $texte = ' aucune commande n\'a été ajoutée';
        } elseif ($nbre > 1) {
            $texte = ' commandes ont été ajoutées';
        }
        if ($nbre > 0) {
            $this->addFlash('message', 'La liste des commandes a été mise à jour, ' . $nbre . $texte);
        } else {
            $this->addFlash('message', 'La liste des commandes a été mise à jour, ' . $texte);
        }

        $this->setCloseAuto();

        return $this->redirectToRoute('app_cmd_roby_delai_accepte_reporte_active');
    }

    /**
     * @Route("/Roby/cmd/status/auto/close", name="app_cmd_roby_auto_close")
     */
    // passer automatiquement les commandes qui ne sont plus en actives dans le systéme en Terminées
    public function setCloseAuto()
    {

        $search = $this->cmdRoby->findBy(['statut' => 'en cours ...']);
        $nbeCmdBefore = count($search);

        foreach ($search as $value) {
            $donnee = $this->entete->controleStatusOfCmd($value->getCmd());

            if ($donnee != 1) {
                $cmd = $this->cmdRoby->findOneBy(['cmd' => $value->getCmd()]);
                $cmd->setModifiedBy($this->Users->findOneBy(['id' => 3]))
                    ->setStatut('Terminé')
                    ->setModifiedAt(new DateTime);
                $em = $this->entityManager;
                $em->persist($cmd);
                $em->flush();
                if ($donnee == false) {
                    $note = new Note();
                    $note->setUser($this->Users->findOneBy(['id' => 3]))
                        ->setContent('Cette commande n\'est plus présente dans le systéme, elle a du être supprimée')
                        ->setCreatedAt(new DateTime)
                        ->setModifiedAt(new DateTime)
                        ->setCmdRobyDelaiAccepteReporte($value);
                    $em = $this->entityManager;
                    $em->persist($note);
                    $em->flush();
                }
                if ($donnee != false) {
                    $note = new Note();
                    $note->setUser($this->Users->findOneBy(['id' => 3]))
                        ->setContent('Cette commande semble avoir été passée en BL, elle n\'est plus en active dans le systéme')
                        ->setCreatedAt(new DateTime)
                        ->setModifiedAt(new DateTime)
                        ->setCmdRobyDelaiAccepteReporte($value);
                    $em = $this->entityManager;
                    $em->persist($note);
                    $em->flush();
                }
            }
        }
        $search = $this->cmdRoby->findBy(['statut' => 'en cours ...']);
        $nbeCmdAfter = count($search);
        $nbre = $nbeCmdBefore - $nbeCmdAfter;
        if ($nbre == 1) {
            $texte = ' commande a été mise en terminée';
        } elseif ($nbre == 0) {
            $texte = ' aucune commande n\'a été mise en terminée';
        } elseif ($nbre > 1) {
            $texte = ' commandes ont été mise en terminées';
        }

        if ($nbre > 0) {
            $this->addFlash('message', 'La liste des commandes a été nettoyée, ' . $nbre . $texte);
        } else {
            $this->addFlash('message', 'La liste des commandes a été nettoyée, ' . $texte);
        }

        //return $this->redirectToRoute('app_cmd_roby_delai_accepte_reporte_active');

    }

    /**
     * @Route("/Roby/cmd/status/view/note/{id}", name="app_cmd_roby_status_view_note")
     */
    // voir les notes liées a une commande et pouvoir en ajouter
    public function viewNote($id, CmdRobyDelaiAccepteReporteRepository $repo, NoteRepository $repoNotes, Request $request): Response
    {

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $commande = $repo->findOneBy(['id' => $id]);
        $notes = $repoNotes->findBy(['cmdRobyDelaiAccepteReporte' => $id]);
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());

            $note = new Note;
            $note->setUser($this->getUser())
                ->setCmdRobyDelaiAccepteReporte($commande)
                ->setCreatedAt(new DateTime)
                ->setContent($form->getData()->getContent());
            $em = $this->entityManager;
            $em->persist($note);
            $em->flush();
            $this->addFlash('message', 'Vous avez bien ajouté une note !');
            return $this->redirectToRoute('app_cmd_roby_delai_accepte_reporte_active');
        }

        return $this->render('cmd_roby_delai_accepte_reporte/note.html.twig', [
            'title' => 'Note',
            'commande' => $commande,
            'notes' => $notes,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Roby/cmd/mail", name="app_cmd_roby_send_mail")
     */
    // envoyer un mail pour avertir sur les commandes qui ont un délai accéptées
    public function sendMail(): Response
    {
        // lancer une mise à jour

        // envoyer les mails suite à la mise à jour pour les commandes qui ne sont pas terminées
        $donnees = $this->cmdRoby->findBy(['statut' => 'en cours ...']);
        $d = new DateTime();
        $i = 0;
        $commandesAvecDelai = [];
        $commandesSansDelai = [];
        $commandesDelaiDepasse = [];
        $commandesDelaiDepasseTotal = 0;
        $commandesAvecDelaiTotal = 0;
        $commandesSansDelaiTotal = 0;
        foreach ($donnees as $donnee) {
            $texte = '';
            if ($donnee->getDelaiReporte() != null | $donnee->getDelaiAccepte() != null) {
                if ($donnee->getDelaiReporte() != null) {
                    $da = $donnee->getDelaiReporte();
                } else {
                    $da = $donnee->getDelaiAccepte();
                }
                $intvl = $d->diff($da);

                if ($da < $d) { // si le délai est dépassé
                    if ($intvl->days == 1) {
                        $texte = 'le délai convenu est dépassé de ' . $intvl->days . " jour";
                    } elseif ($intvl->days > 1) {
                        $texte = 'le délai convenu est dépassé de ' . $intvl->days . " jours";
                    }
                    $commandesDelaiDepasse[$i]['Tiers'] = $donnee->getTiers();
                    $commandesDelaiDepasse[$i]['Nom'] = $donnee->getNom();
                    $commandesDelaiDepasse[$i]['Cmd'] = $donnee->getCmd();
                    $commandesDelaiDepasse[$i]['DateCmd'] = $donnee->getDateCmd();
                    $commandesDelaiDepasse[$i]['NotreRef'] = $donnee->getNotreRef();
                    $commandesDelaiDepasse[$i]['ht'] = $donnee->getHt();
                    $commandesDelaiDepasseTotal += $donnee->getHt();
                    if ($donnee->getDelaiAccepte() != null) {
                        $commandesDelaiDepasse[$i]['DelaiAccepte'] = $donnee->getDelaiAccepte();
                    } else {
                        $commandesDelaiDepasse[$i]['DelaiAccepte'] = '';
                    }
                    if ($donnee->getDelaiReporte() != null) {
                        $commandesDelaiDepasse[$i]['DelaiReporte'] = $donnee->getDelaiReporte();
                    } else {
                        $commandesDelaiDepasse[$i]['DelaiReporte'] = '';
                    }
                    $commandesDelaiDepasse[$i]['Commentaire'] = $texte;
                } elseif ($d < $da) { // si le délai n'est pas encore dépassé
                    if ($intvl->days <= 15) {
                        if ($intvl->days == 1) {
                            $texte = 'le délai convenu arrive a échéance dans ' . $intvl->days . " jour";
                        } elseif ($intvl->days == 0) {
                            $texte = 'le délai convenu est aujourd\'hui !';
                        } elseif ($intvl->days > 1) {
                            $texte = 'le délai convenu arrive a échéance dans ' . $intvl->days . " jours";
                        }
                        $commandesAvecDelai[$i]['Tiers'] = $donnee->getTiers();
                        $commandesAvecDelai[$i]['Nom'] = $donnee->getNom();
                        $commandesAvecDelai[$i]['Cmd'] = $donnee->getCmd();
                        $commandesAvecDelai[$i]['DateCmd'] = $donnee->getDateCmd();
                        $commandesAvecDelai[$i]['NotreRef'] = $donnee->getNotreRef();
                        $commandesAvecDelai[$i]['ht'] = $donnee->getHt();
                        $commandesAvecDelaiTotal += $donnee->getHt();
                        if ($donnee->getDelaiAccepte() != null) {
                            $commandesAvecDelai[$i]['DelaiAccepte'] = $donnee->getDelaiAccepte();
                        } else {
                            $commandesAvecDelai[$i]['DelaiAccepte'] = '';
                        }
                        if ($donnee->getDelaiReporte() != null) {
                            $commandesAvecDelai[$i]['DelaiReporte'] = $donnee->getDelaiReporte();
                        } else {
                            $commandesAvecDelai[$i]['DelaiReporte'] = '';
                        }
                        $commandesAvecDelai[$i]['Commentaire'] = $texte;
                    }
                }
            } else {
                $commandesSansDelai[$i]['Tiers'] = $donnee->getTiers();
                $commandesSansDelai[$i]['Nom'] = $donnee->getNom();
                $commandesSansDelai[$i]['Cmd'] = $donnee->getCmd();
                $commandesSansDelai[$i]['DateCmd'] = $donnee->getDateCmd();
                $commandesSansDelai[$i]['NotreRef'] = $donnee->getNotreRef();
                $commandesSansDelai[$i]['ht'] = $donnee->getHt();
                $commandesSansDelaiTotal += $donnee->getHt();
                if ($donnee->getDelaiAccepte() != null) {
                    $commandesSansDelai[$i]['DelaiAccepte'] = $donnee->getDelaiAccepte();
                } else {
                    $commandesSansDelai[$i]['DelaiAccepte'] = '';
                }
                $commandesSansDelai[$i]['DelaiReporte'] = $donnee->getDelaiReporte();
                $commandesSansDelai[$i]['Commentaire'] = 'aucun délai n\'est renseigné';
            }
            $i++;
        }

        // envoyer un mail
        $treatementMails = $this->repoMail->findBy(['page' => 'app_cmd_roby_delai_accepte_reporte_active']);
        $mails = $this->adminEmailController->formateEmailList($treatementMails);
        $total = $commandesDelaiDepasseTotal + $commandesAvecDelaiTotal + $commandesSansDelaiTotal;
        $html = $this->renderView('mails/cmdRobyDelaiAccepteReporte.html.twig', ['commandesSansDelais' => $commandesSansDelai,
            'commandesAvecDelais' => $commandesAvecDelai,
            'commandesDelaiDepasses' => $commandesDelaiDepasse,
            'commandesDelaiDepasseTotal' => $commandesDelaiDepasseTotal,
            'commandesAvecDelaiTotal' => $commandesAvecDelaiTotal,
            'commandesSansDelaiTotal' => $commandesSansDelaiTotal,
            'total' => $total]);
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to(...$mails)
            ->subject('Liste des commandes avec Délais et sans délais')
            ->html($html);
        $this->mailer->send($email);

        return $this->redirectToRoute('app_cmd_roby_delai_accepte_reporte_active');
    }
}
