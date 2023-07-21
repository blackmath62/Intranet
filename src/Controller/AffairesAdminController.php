<?php

namespace App\Controller;

use App\Entity\Main\MailList;
use App\Form\AddEmailType;
use App\Repository\Main\InterventionFicheMonteurRepository;
use App\Repository\Main\InterventionMonteursRepository;
use App\Repository\Main\MailListRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN_MONTEUR")
 */

class AffairesAdminController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;
    private $repoFiche;
    private $repoIntervention;

    public function __construct(InterventionMonteursRepository $repoIntervention, InterventionFicheMonteurRepository $repoFiche, AdminEmailController $adminEmailController, MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->repoFiche = $repoFiche;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
        $this->repoIntervention = $repoIntervention;

        //parent::__construct();
    }

    /**
     * @Route("/Lhermitte/affaires/admin", name="app_affaires_admin")
     */
    public function index(Request $request): Response
    {
        $tracking = $request->attributes->get('_route');
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

        return $this->render('affaires_admin/index.html.twig', [
            'form' => $form->createView(),
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            'title' => 'Administration des fiches d\'interventions',
            'fichesNonVerrouillees' => $fichesNonVerrouillees,
            'fichesAttenteValidations' => $fichesAttenteValidations,
            'fichesSansHeures' => $fichesSansHeures,
            'fichesManquantes' => $fichesManquantes,
            'fichesDatesIncoherentes' => $fichesDatesIncoherentes,
        ]);
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
        // pour chaque interventions
        $interventions = $this->repoIntervention->findBy(['lockedAt' => null]);
        foreach ($interventions as $intervention) {
            // Pour chaque interval de date hors férier et weekend
            $interval = DateInterval::createFromDateString('1 day');
            $days = new DatePeriod($intervention->getStart(), $interval, $intervention->getEnd());

            /** @var DateTimeInterface $day */
            foreach ($days as $day) {
                if ($day <= new Datetime) {
                    $valid = $this->controlDay($day);
                    if ($valid == false) {
                        // Pour chaque intervenant de cette intervention pour ce jour
                        foreach ($intervention->getEquipes() as $intervenant) {
                            $fiche = $this->repoFiche->findBy(['intervenant' => $intervenant, 'createdAt' => $day]);
                            if (!$fiche) {
                                // liste des fiches manquantes
                                $fichesManquantes[] = [
                                    'createdAt' => $day,
                                    'intervenant' => $intervenant,
                                    'intervention' => $intervention,

                                ];
                            }
                        }
                    }
                }
            }
        }
        return $fichesManquantes;
    }
}
