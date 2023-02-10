<?php

namespace App\Controller;

use App\Controller\AdminEmailController;
use App\Entity\Main\MailList;
use App\Entity\Main\MovBillFsc;
use App\Form\AddEmailType;
use App\Form\FactureFournisseursFscType;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\documentsFscRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\MovBillFscRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
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

class MovementBillFscController extends AbstractController
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

    public function __construct(UsersRepository $repoUsers, AdminEmailController $adminEmailController, MailListRepository $repoMail, MovBillFscRepository $repoBill, documentsFscRepository $repoDocs, MouvRepository $repoMouv, MovBillFscRepository $repoFact, EntRepository $repoEnt, MailerInterface $mailer)
    {
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
        //parent::__construct();
    }

    /**
     * @Route("/Roby/movement/bill/fsc", name="app_movement_bill_fsc")
     */
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
                $em = $this->getDoctrine()->getManager();
                $em->persist($mail);
                $em->flush();
            } else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                return $this->redirectToRoute('app_movement_bill_fsc');
            }
        }

        return $this->render('movement_bill_fsc/index.html.twig', [
            'clients' => $this->repoFact->findAll(),
            'title' => 'Factures clients Fsc',
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Roby/movement/bill/fsc/show/{id}", name="app_movement_bill_fsc_show")
     */
    public function show($id = null, Request $request, MovBillFsc $bill): Response
    {
        $form = $this->createForm(FactureFournisseursFscType::class, $bill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bill);
            $entityManager->flush();

            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_movement_bill_fsc_show', ['id' => $id]);
        }
        $facture = $this->repoFact->findOneBy(['id' => $id]);
        $documents = [];
        foreach ($facture->getVentilations()->getValues() as $value) {
            $docs = $this->repoDocs->findBy(['fscListMovement' => $value->getId()]);
            foreach ($docs as $doc) {
                array_push($documents, $doc);
            }
        }
        return $this->render('movement_bill_fsc/show.html.twig', [
            'facture' => $facture,
            'documents' => $documents,
            'title' => 'Détail facture client Fsc',
            'details' => $this->repoMouv->getDetailFactureFscClient($facture->getFacture()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Roby/movement/bill/fsc/update", name="app_movement_bill_fsc_update")
     */
    // on ajoute les factures clients qui n'y sont pas déjà
    public function update(): Response
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
                        ->setNom($value['nom'])
                        ->setNotreRef($value['notreRef'])
                        ->setTypeTiers($value['typeTiers']);

                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($bill);
                $entityManager->flush();
            }
            $this->sendMailVenteSansLiaison();
        }

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_movement_bill_fsc');
    }

    // mail automatique pour demander la liaison avec les achats
    public function sendMailVenteSansLiaison()
    {

        $piecesAnormales = $this->repoBill->getFactCliSansLiaison();

        $treatementMails = $this->repoMail->findBy(['page' => 'app_movement_bill_fsc']);
        $mails = $this->adminEmailController->formateEmailList($treatementMails);
        // envoyer un mail si il y a des infos à envoyer
        if (count($piecesAnormales) > 0) {
            // envoyer un mail
            $html = $this->renderView('mails/listePieceFscClientSansLiaison.html.twig', ['piecesAnormales' => $piecesAnormales]);
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
