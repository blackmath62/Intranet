<?php

namespace App\Controller;

use App\Entity\Main\MailList;
use App\Form\AddEmailType;
use App\Form\StatesDateFilterType;
use App\Repository\Main\InterventionFicheMonteurRepository;
use App\Repository\Main\InterventionMonteursRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\UsersRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Knp\Snappy\Pdf;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN_MONTEUR")
 */

class AffairesAdminController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $repoUsers;
    private $mailTreatement;
    private $adminEmailController;
    private $repoFiche;
    private $repoIntervention;

    public function __construct(UsersRepository $repoUsers, InterventionMonteursRepository $repoIntervention, InterventionFicheMonteurRepository $repoFiche, AdminEmailController $adminEmailController, MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->repoFiche = $repoFiche;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
        $this->repoIntervention = $repoIntervention;
        $this->repoUsers = $repoUsers;

        //parent::__construct();
    }

    /**
     * @Route("/Lhermitte/affaires/admin", name="app_affaires_admin")
     */
    public function index(Request $request): Response
    {
        $tracking = $request->attributes->get('_route');
        $tabPointages = [];
        $tabAffaires = [];
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
                return $this->redirectToRoute('app_affaires_admin');
            }
        }
        $fichesNonVerrouillees = $this->repoFiche->findBy(['lockedAt' => null]);
        $fichesAttenteValidations = $this->repoFiche->findFicheAttenteValidation();
        $fichesSansHeures = $this->repoFiche->findFicheSansHeures();
        $fichesDatesIncoherentes = $this->repoFiche->findFicheDatesIncohérentes();
        $fichesManquantes = $this->recupFichesManquantes();

        $formFiches = $this->createForm(StatesDateFilterType::class);
        $formFiches->handleRequest($request);
        if ($formFiches->isSubmitted() && $formFiches->isValid()) {

            $start = $formFiches->getData()['startDate']->format('Y-m-d');
            $end = $formFiches->getData()['endDate']->format('Y-m-d');
            $tabPointages = $this->repoFiche->findPointagePeriode($start, $end);
            $tabAffaires = [];
            //dd($tabPointages);

        }
        return $this->render('affaires_admin/index.html.twig', [
            'form' => $form->createView(),
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            'title' => 'Administration des fiches d\'interventions',
            'fichesNonVerrouillees' => $fichesNonVerrouillees,
            'fichesAttenteValidations' => $fichesAttenteValidations,
            'fichesSansHeures' => $fichesSansHeures,
            'fichesManquantes' => $fichesManquantes,
            'fichesDatesIncoherentes' => $fichesDatesIncoherentes,
            'tabAffaire' => $tabAffaires,
            'tabPointages' => $tabPointages,
            'formFiches' => $formFiches->createView(),
        ]);
    }

    /**
     * @Route("/Lhermitte/affaires/admin/valider/fiche/{id}", name="app_affaire_valider_fiche_intervention")
     */
    public function validerFiche($id)
    {

        $fiche = $this->repoFiche->findOneBy(['id' => $id]);
        $fiche->setValidedBy($this->getUser())
            ->setValidedAt(new DateTime);

        $em = $this->getDoctrine()->getManager();
        $em->persist($fiche);
        $em->flush();

        $this->addFlash('message', 'La fiche a été validée avec succés !');
        return $this->redirectToRoute('app_affaires_admin');

    }

    // Vérifier que le jour est hors férier et weekend
    public function controlDay($day)
    {

        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");

        // Jour férier ?
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, true)),
            RecursiveIteratorIterator::SELF_FIRST);
        $ferierDurantConges = array();
        foreach ($jsonIterator as $key => $val) {
            if ($key == $day) {
                return true;
            }
        }

        // jour du weekend ?
        if ($day->format('N') == 6 and $day->format('N') == 7) {
            return true;
        }
        return false;
    }

    public function recupFichesManquantes()
    {
        // lancer à chaque fois que la page affaire ou admin affaire est ouverte

        // pour chaque interventions
        $fichesManquantes = [];
        $interventions = $this->repoIntervention->findBy(['lockedAt' => null]);
        foreach ($interventions as $intervention) {
            // Pour chaque interval de date hors férier et weekend
            $interval = DateInterval::createFromDateString('1 day');
            $days = new DatePeriod($intervention->getStart(), $interval, $intervention->getEnd());
            $status = true;

            /** @var DateTimeInterface $day */
            foreach ($days as $day) {
                if ($day <= new Datetime) {
                    $valid = $this->controlDay($day);
                    if ($valid == false) {
                        // Pour chaque intervenant de cette intervention pour ce jour
                        foreach ($intervention->getEquipes() as $intervenant) {
                            $day = new DateTime($day->format('Y-m-d'));
                            $fiche = $this->repoFiche->findBy(['intervenant' => $intervenant, 'createdAt' => $day]);
                            if (!$fiche) {
                                // liste des fiches manquantes
                                $fichesManquantes[] = [
                                    'createdAt' => $day,
                                    'intervenant' => $intervenant,
                                    'intervention' => $intervention,

                                ];
                                // s'il manque une fiche on ne verrouillera pas l'intervention
                                $status = false;
                            } elseif (!$fiche[0]->getValidedBy()) {
                                // si la fiche n'a pas été validé, on ne verrouille pas l'intervention
                                $status = false;
                            }
                        }
                    }
                }
            }
            // on verrouille l'intervention si toutes les fiches sont renseignées et validées
            if ($status == true) {
                $intervention->setLockedAt(new DateTime)
                    ->setLockedBy($this->repoUsers->findOneBy(['id' => 3]));
                $em = $this->getDoctrine()->getManager();
                $em->persist($intervention);
                $em->flush();
            }
        }
        return $fichesManquantes;
    }

    /**
     * @Route("/Lhermitte/affaires/admin/signature", name="app_affaire_signature")
     */
    public function envoyerPourSignature(MailerInterface $mailer, Pdf $pdf)
    {
        // récupérer les interventions qui sont n'ont pas été envoyé et qui ont été verouillé par l'intranet
        $interventions = $this->repoIntervention->findBy(['lockedBy' => $this->repoUsers->findOneBy(['id' => 3]), 'sendAt' => null]);

        foreach ($interventions as $intervention) {
            //dd($intervention);
            // construire un PDF pour la demande de signature
            $htmlPdf = $this->renderView('affaires_admin/pdf/pdfSignature.html.twig', ['intervention' => $intervention]);
            //dd($htmlPdf);
            $html = 'Voici une la fiche d\'intervention à valider';
            $pdf = $pdf->getOutputFromHtml($htmlPdf);
            // envoyer le PDF par email au mail renseigné sur la fiche client
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($this->getUser()->getEmail())
                ->subject('Signature fiche d\'intervention')
                ->html($html)
                ->attach($pdf, $intervention->getCode()->getCode() . ' - ' . $intervention->getStart()->format('d-m-Y') . ' - ' . $intervention->getEnd()->format('d-m-Y') . '.pdf');
            $mailer->send($email);
            /*
        // noter sur l'intervention que le mail a bien été envoyé
        $intervention->setSendAt(new DateTime);
        $em = $this->getDoctrine()->getManager();
        $em->persist($intervention);
        $em->flush();*/
        }

    }

}
