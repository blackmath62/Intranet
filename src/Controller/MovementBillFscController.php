<?php

namespace App\Controller;

use DateTime;
use App\Entity\Main\MovBillFsc;
use App\Entity\Main\documentsFsc;
use Symfony\Component\Mime\Email;
use App\Entity\Main\fscListMovement;
use App\Form\FactureFournisseursFscType;
use App\Repository\Main\UsersRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\MovBillFscRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Main\documentsFscRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    public function __construct(MailListRepository $repoMail, MovBillFscRepository $repoBill, documentsFscRepository $repoDocs, MouvRepository $repoMouv, MovBillFscRepository $repoFact, EntRepository $repoEnt,MailerInterface $mailer)
    {
        $this->repoFact = $repoFact;
        $this->repoEnt = $repoEnt;
        $this->repoMouv = $repoMouv;
        $this->mailer = $mailer;
        $this->repoDocs = $repoDocs;
        $this->repoBill = $repoBill;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        //parent::__construct();
    }

    
    /**
     * @Route("/Roby/movement/bill/fsc", name="app_movement_bill_fsc")
     */
    public function index(): Response
    {
        return $this->render('movement_bill_fsc/index.html.twig', [
            'clients' => $this->repoFact->findAll(),
            'title' => 'Factures clients Fsc'
        ]);
    }

    /**
     * @Route("/Roby/movement/bill/fsc/show/{id}", name="app_movement_bill_fsc_show")
     */
    public function show($id=null, Request $request, MovBillFsc $bill): Response
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
        $facture = $this->repoFact->findOneBy(['id' =>$id]);
        $documents = [];
        foreach ($facture->getVentilations()->getValues() as $value) {
            $docs = $this->repoDocs->findBy(['fscListMovement' => $value->getId() ]);
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
    public function update(UsersRepository $users): Response
    {
        $user = $users->findOneBy(['pseudo' => 'intranet']);
            $factures = $this->repoEnt->getMouvfactCliFsc();
            foreach ($factures as $value) {
                $bill = $this->repoFact->findOneBy(['facture' => $value['facture']]);
                if ($bill == NULL) {
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

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_movement_bill_fsc');
    }

    // mail automatique pour demander la liaison avec les achats
    public function sendMailVenteSansLiaison(){
        
        $piecesAnormales = $this->repoBill->getFactCliSansLiaison();

        // envoyer un mail si il y a des infos à envoyer
        if (count($piecesAnormales) > 0) {
            // envoyer un mail
            $html = $this->renderView('mails/listePieceFscClientSansLiaison.html.twig', ['piecesAnormales' => $piecesAnormales ]);
            // TODO Remettre Nathalie en destinataire des mails.
            $email = (new Email())
            ->from($this->mailEnvoi)
            ->to('ndegorre@roby-fr.com')
            ->cc($this->mailTreatement)
            ->subject("Liste des piéces clients FSC sur lesquels il n'y a pas de liaison")
            ->html($html);
            $this->mailer->send($email);
        }
     }

     //TODO envoyer un mail avec les piéces clients dans le cas ou un probléme est détecté sur les piéces fournisseurs.

}
