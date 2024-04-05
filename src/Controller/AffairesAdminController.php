<?php

namespace App\Controller;

use App\Entity\Main\MailList;
use App\Form\AddEmailType;
use App\Form\StatesDateFilterType;
use App\Form\TiersPieceAffaireFormType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\InterventionFicheMonteurRepository;
use App\Repository\Main\InterventionMonteursRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\UsersRepository;
use App\Service\EmailTreatementService;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN_MONTEUR")]

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
    private $emailTreatementService;
    private $entityManager;
    private $repoMouv;

    public function __construct(
        ManagerRegistry $registry,
        EmailTreatementService $emailTreatementService,
        UsersRepository $repoUsers,
        InterventionMonteursRepository $repoIntervention,
        InterventionFicheMonteurRepository $repoFiche,
        AdminEmailController $adminEmailController,
        MailerInterface $mailer,
        MailListRepository $repoMail,
        MouvRepository $repoMouv,
    ) {
        $this->mailer = $mailer;
        $this->repoMouv = $repoMouv;
        $this->repoMail = $repoMail;
        $this->repoFiche = $repoFiche;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
        $this->repoIntervention = $repoIntervention;
        $this->repoUsers = $repoUsers;
        $this->emailTreatementService = $emailTreatementService;
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/Lhermitte/affaires/change/code/affaire", name: "app_affaire_change_code_affaire_search")]

    public function changeCodeAffairePiece(Request $request)
    {

        $form = $this->createForm(TiersPieceAffaireFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Modification des données dans Divalto
            $this->repoMouv->changeCodeAffairePiece($form->getData()['tiers'], $form->getData()['piece'], $form->getData()['affaire']);
        }

        return $this->render('affaires_admin/setCodeAffairePiece.html.twig', [
            'title' => 'Change Affaire',
            'form' => $form->createView(),
        ]);

    }

    #[Route("/Lhermitte/affaires/search/piece/code/affaire", name: "app_affaire_search_piece_code_affaire")]

    public function searchPiece(Request $request)
    {
        // Récupérez les données du formulaire via la requête
        $tiers = $request->request->get('tiers');
        $piece = $request->request->get('piece');

        // Effectuez la recherche dans la base de données en utilisant $champ1 et $champ2
        $donnees = $this->repoMouv->searchCodeAffairePiece($tiers, $piece);

        // Convertissez les données en format JSON et renvoyez-les
        return new JsonResponse($donnees);
    }

    #[Route("/Lhermitte/affaires/admin", name: "app_affaires_admin")]

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
                $em = $this->entityManager;
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

    #[Route("/Lhermitte/affaires/admin/valider/fiche/{id}", name: "app_affaire_valider_fiche_intervention")]

    public function validerFiche($id)
    {

        $fiche = $this->repoFiche->findOneBy(['id' => $id]);
        $fiche->setValidedBy($this->getUser())
            ->setValidedAt(new DateTime);

        $em = $this->entityManager;
        $em->persist($fiche);
        $em->flush();

        $this->addFlash('message', 'La fiche a été validée avec succés !');
        return $this->redirectToRoute('app_affaires_admin');

    }

    // Vérifier que le jour est hors férier et weekend
    public function controlDay($day)
    {

        $d = clone $day;
        $d = $d->format('Y-m-d');
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");

        // Jour férier ?
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, true)),
            RecursiveIteratorIterator::SELF_FIRST);
        $ferierDurantConges = array();
        foreach ($jsonIterator as $key => $val) {
            if ($key == $d) {
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
            if (count($intervention->getEquipes()) === 0) {
                $status = false;
                $this->addFlash('danger', 'Attention, Intervention ' . $intervention->getCode()->getLibelle() . ' du ' . $intervention->getStart()->format("d/m/y") . ' au ' . $intervention->getEnd()->format("d/m/y") . ' sans intervenants.');
            }
            /*if ($intervention->getId() == 205) {
            dd($intervention);
            }*/

            /** @var DateTimeInterface $day */
            foreach ($days as $day) {
                $end = clone $intervention->getEnd();
                $endDateTime = clone $intervention->getEnd();
                $hi = $endDateTime->format('H:i'); // Récupérer les heures
                $nc = false;
                if ($day === $end && $hi != '00:00') {
                    $nc = true;
                }
                // revoir cette partie
                if ($nc === false) {
                    if ($day >= new DateTime('2024/03/25')) {
                        if ($day <= new Datetime) {
                            $valid = $this->controlDay($day);
                            if ($valid == false) {
                                // Pour chaque intervenant de cette intervention pour ce jour
                                foreach ($intervention->getEquipes() as $intervenant) {
                                    $day = new DateTime($day->format('Y-m-d'));
                                    $fiche = $this->repoFiche->findBy(['intervenant' => $intervenant, 'createdAt' => $day, 'intervention' => $intervention]);
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
                        } else {
                            $status = false;
                        }
                    }
                }
            }
            // on verrouille l'intervention si toutes les fiches sont renseignées et validées
            if ($status == true) {
                $intervention->setLockedAt(new DateTime)
                    ->setLockedBy($this->repoUsers->findOneBy(['id' => 3]));
                $em = $this->entityManager;
                $em->persist($intervention);
                $em->flush();
            }
        }
        return $fichesManquantes;
    }

    /**
     * @Route("/Lhermitte/affaires/admin/intervention/delete/{id}", name="app_admin_intervention_delete", methods={"POST"})
     */
    public function delete(int $id): JsonResponse
    {
        // Récupérer l'intervention par son ID
        $intervention = $this->repoIntervention->find($id);

        // Vérifier si l'intervention existe
        if (!$intervention) {
            return new JsonResponse(['error' => 'Intervention not found'], 404);
        }

        // Vérifier s'il existe des pièces reliées à cette intervention
        if ($intervention->getPieces()->count() > 0) {
            return new JsonResponse(['error' => 'Des pièces sont reliées à cette intervention'], 400);
        }

        // Supprimer l'intervention
        $this->entityManager->remove($intervention);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Intervention deleted successfully']);
    }

    // TODO CETTE PARTIE PLUS COMPLEXE EST A FAIRE
    // CREER LE PDF POUR QU'IL PRESENTE BIEN ET METTRE LES CGV EN DERNIERE PAGE
    // CONNECTER CELA AVEC YOUSIGN
    // PEUT ÊTRE CREER UNE PAGE COMPTE RENDU
    #[Route("/Lhermitte/affaires/admin/signature", name: "app_affaire_signature")]

    public function envoyerPourSignature(MailerInterface $mailer, Pdf $pdf)
    {
        // Récupérer les interventions qui n'ont pas été envoyées et qui ont été verrouillées par l'intranet
        $interventions = $this->repoIntervention->findBy(['lockedBy' => $this->repoUsers->findOneBy(['id' => 3]), 'sendAt' => null]);

        foreach ($interventions as $intervention) {
            // Construire un PDF pour la demande de signature

            $htmlPdf = $this->renderView('affaires_admin/pdf/pdfSignature.html.twig', ['intervention' => $intervention]);

            // Envoyer le PDF par email à l'adresse renseignée sur la fiche client
            $pdfContent = $pdf->getOutputFromHtml($htmlPdf);

            // Créer l'email avec le PDF attaché
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($this->getUser()->getEmail())
                ->subject('Signature fiche d\'intervention')
                ->text('Voici la fiche d\'intervention à valider.')
                ->attach($pdfContent, $intervention->getCode()->getCode() . ' - ' . $intervention->getStart()->format('d-m-Y') . ' - ' . $intervention->getEnd()->format('d-m-Y') . '.pdf');

            // Envoyer l'email
            $mailer->send($email);
            /*
        // Marquer l'intervention comme envoyée
        $intervention->setSendAt(new DateTime());
        $em = $this->entityManager;
        $em->persist($intervention);
        $em->flush();
         */
        }

    }

}
