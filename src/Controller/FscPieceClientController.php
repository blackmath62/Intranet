<?php

namespace App\Controller;

use App\Controller\AdminEmailController;
use App\Entity\Main\Commentaires;
use App\Entity\Main\MailList;
use App\Entity\Main\MovBillFsc;
use App\Form\AddEmailType;
use App\Form\CommentairesType;
use App\Form\FactureFournisseursFscType;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\CommentairesRepository;
use App\Repository\Main\documentsFscRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\MovBillFscRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ROBY")]

class FscPieceClientController extends AbstractController
//class MovementBillFscController extends AbstractController

{
    private $repoFact;
    private $repoEnt;
    private $repoMouv;
    private $repoDocs;
    private $mailer;
    private $repoBill;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;
    private $repoUsers;
    private $repoComments;
    private $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        CommentairesRepository $repoComments,
        UsersRepository $repoUsers,
        AdminEmailController $adminEmailController,
        MailListRepository $repoMail,
        MovBillFscRepository $repoBill,
        documentsFscRepository $repoDocs,
        MouvRepository $repoMouv,
        MovBillFscRepository $repoFact,
        EntRepository $repoEnt,
        MailerInterface $mailer) {
        $this->repoFact = $repoFact;
        $this->repoEnt = $repoEnt;
        $this->repoUsers = $repoUsers;
        $this->repoMouv = $repoMouv;
        $this->mailer = $mailer;
        $this->repoDocs = $repoDocs;
        $this->repoBill = $repoBill;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
        $this->repoComments = $repoComments;
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/Roby/fsc/pieces/clients/index", name: "app_fsc_piece_client")]

    public function index(Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);

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
                return $this->redirectToRoute('app_fsc_piece_client');
            }
        }

        return $this->render('fsc_piece_client/index.html.twig', [
            'clients' => $this->repoFact->findAll(),
            'title' => 'Factures clients Fsc',
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            'form' => $form->createView(),
        ]);
    }

    #[Route("/Roby/fsc/pieces/clients/verrou/{id}", name: "app_fsc_piece_client_verrou")]

    public function verrou($id, Request $request): Response
    {

        if ($request->request->get('commentaire')) {

            $piece = $this->repoBill->findOneBy(['id' => $id]);
            if ($piece->getAnomalie() == true) {
                $piece->setAnomalie(false);
                $this->addFlash('message', 'Cette piece a bien été retiré des anomalies');
            } else {
                $piece->setAnomalie(true);
                $this->addFlash('danger', 'Cette piece a bien été mise en anomalie');
            }

            $entityManager = $this->entityManager;
            $entityManager->persist($piece);
            $entityManager->flush();

            $commentaire = new Commentaires;
            $commentaire->setCreatedAt(new DateTime())
                ->setUser($this->getUser())
                ->setContent($request->request->get('commentaire'))
                ->setTables('app_fsc_piece_client')
                ->setIdentifiant($id);

            $entityManager = $this->entityManager;
            $entityManager->persist($commentaire);
            $entityManager->flush();
        } else {
            $this->addFlash('danger', 'Le commentaire est obligatoire !');
        }

        return $this->redirectToRoute('app_fsc_piece_client');

    }

    #[Route("/Roby/fsc/pieces/clients/show/{id}", name: "app_fsc_piece_client_show")]

    public function show(Request $request, MovBillFsc $bill, $id = null): Response
    {
        $form = $this->createForm(FactureFournisseursFscType::class, $bill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $entityManager->persist($bill);
            $entityManager->flush();

            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_fsc_piece_client_show', ['id' => $id]);
        }
        $commentaires = new Commentaires();
        $formComment = $this->createForm(CommentairesType::class, $commentaires);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $commentaires->setIdentifiant($id)
                ->setUser($this->getUser())
                ->setCreatedAt(new DateTime())
                ->setTables('app_fsc_piece_client');
            $entityManager = $this->entityManager;
            $entityManager->persist($commentaires);
            $entityManager->flush();

            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_fsc_piece_client_show', ['id' => $id]);
        }
        $facture = $this->repoFact->findOneBy(['id' => $id]);
        $documents = [];
        foreach ($facture->getVentilations()->getValues() as $value) {
            $docs = $this->repoDocs->findBy(['fscListMovement' => $value->getId()]);
            foreach ($docs as $doc) {
                array_push($documents, $doc);
            }
        }
        return $this->render('fsc_piece_client/show.html.twig', [
            'facture' => $facture,
            'documents' => $documents,
            'title' => 'Détail facture client Fsc',
            'details' => $this->repoMouv->getDetailFactureFscClient($facture->getFacture()),
            'form' => $form->createView(),
            'formComment' => $formComment->createView(),
            'comments' => $this->repoComments->findBy(['Tables' => 'app_fsc_piece_client', 'identifiant' => $id]),
        ]);
    }

    #[Route("/Roby/fsc/pieces/clients/update", name: "app_fsc_piece_client_update")]

    // on ajoute les factures clients qui n'y sont pas déjà
    public function update(): Response
    {
        $this->updateWithoutFlash();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_fsc_piece_client');
    }

    #[Route("/Roby/fsc/pieces/clients/update/without/flash", name: "app_fsc_piece_client_update_wihout_flash")]

    // on ajoute les factures clients qui n'y sont pas déjà
    public function updateWithoutFlash()
    {
        $user = $this->repoUsers->findOneBy(['pseudo' => 'intranet']);
        $factures = $this->repoEnt->getMouvfactCliFsc();
        if ($factures) {
            foreach ($factures as $value) {
                $bill = $this->repoFact->findOneBy(['facture' => $value['facture']]);
                if ($bill == null) {
                    $bill = new MovBillFsc();
                    $bill->setCreatedAt(new DateTime())
                        ->setCreatedBy($user)
                        ->setFacture($value['facture'])
                        ->setdateFact(new DateTime($value['dateFacture']))
                        ->setTiers($value['tiers'])
                        ->setAnomalie(false)
                        ->setNom($value['nom'])
                        ->setNotreRef($value['notreRef'])
                        ->setTypeTiers($value['typeTiers']);

                }
                $entityManager = $this->entityManager;
                $entityManager->persist($bill);
                $entityManager->flush();
            }
            $this->sendMailVenteSansLiaison();
        }
    }

    // mail automatique pour demander la liaison avec les achats
    public function sendMailVenteSansLiaison()
    {

        $piecesAnormales = $this->repoBill->getFactCliSansLiaison();

        $treatementMails = $this->repoMail->findBy(['page' => 'app_fsc_piece_client']);
        $mails = $this->adminEmailController->formateEmailList($treatementMails);
        // envoyer un mail si il y a des infos à envoyer
        if (count($piecesAnormales) > 0) {
            // envoyer un mail
            $html = $this->renderView('fsc_piece_client/listePieceFscClientSansLiaison.html.twig', ['piecesAnormales' => $piecesAnormales]);
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to(...$mails)
                ->cc($this->mailTreatement)
                ->subject("Liste des piéces clients FSC sur lesquels il n'y a pas de liaison")
                ->html($html);
            $this->mailer->send($email);
        }
    }

    //TODO envoyer un mail avec les piéces clients dans le cas ou un probléme est détecté sur les piéces fournisseurs.

}
