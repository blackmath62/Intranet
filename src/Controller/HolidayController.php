<?php

namespace App\Controller;

use App\Controller\AdminEmailController;
use App\Entity\Main\Holiday;
use App\Form\HolidayType;
use App\Form\HolidayTypeDateDebutFinExcel;
use App\Form\ImposeVacationType;
use App\Form\StatesDateFilterType;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\statusHolidayRepository;
use App\Repository\Main\UsersRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[IsGranted("ROLE_USER")]

class HolidayController extends AbstractController
{

    private $mailerInterface;
    private $repoHoliday;
    private $repoStatuts;
    private $repoUser;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $mailer;
    private $adminEmailController;
    private $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        AdminEmailController $adminEmailController,
        MailListRepository $repoMail,
        MailerInterface $mailer,
        MailerInterface $mailerInterface,
        HolidayRepository $repoHoliday,
        statusHolidayRepository $repoStatuts,
        UsersRepository $repoUser) {
        $this->mailerInterface = $mailerInterface;
        $this->repoHoliday = $repoHoliday;
        $this->repoStatuts = $repoStatuts;
        $this->repoUser = $repoUser;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->mailer = $mailer;
        $this->adminEmailController = $adminEmailController;
        $this->entityManager = $registry->getManager();
    }

    public function sendMailSummerForAllUsers()
    {
        $usersMails = $this->repoUser->getFindAllEmails();
        $listMails = $this->adminEmailController->formateEmailList($usersMails);

        // Avertir chaque utilisateur par mail
        $html = $this->renderView('mails/pleaseDeposeSummerHoliday.html.twig');
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to(...$listMails)
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Veuillez déposer vos congés d'été sur le site intranet avant le 31 Mars")
            ->html($html);

        $this->mailer->send($email);

    }

    #[Route("/holiday", name: "app_holiday_list")]

    public function index(UrlGeneratorInterface $urlGenerator, Request $request)
    {
        $holidays = $this->repoHoliday->findAll();

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $users = $this->repoUser->findBy(['closedAt' => null]);

        // Calendrier des congés
        $events = $this->repoHoliday->findBy(['holidayStatus' => 3]);
        $rdvs = [];

        foreach ($events as $event) {
            $id = $event->getId();
            $userId = $this->repoHoliday->getUserIdHoliday($id);
            $user = $this->repoUser->findOneBy(['id' => $userId]);
            $pseudo = $user->getPseudo();
            $color = $user->getService()->getColor();
            $textColor = $user->getService()->getTextColor();
            $start = $event->getStart()->format('Y-m-d H:i:s');
            $end = $event->getEnd()->format('Y-m-d H:i:s');
            if ($event->getStart()->format('Y-m-d') == $event->getEnd()->format('Y-m-d') && $event->getStart()->format('H:i') == '00:00' && $event->getEnd()->format('H:i') == '23:00') {
                $start = $event->getStart()->format('Y-m-d');
                $end = $event->getEnd()->format('Y-m-d');
            }

            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $start,
                'end' => $end,
                'url' => $urlGenerator->generate('app_holiday_show', ['id' => $id]),
                'title' => 'Congés ' . $pseudo . ' du ' . $event->getStart()->format('d-m-Y') . ' au ' . $event->getEnd()->format('d-m-Y'),
                'backgroundColor' => $color,
                'borderColor' => '#FFFFFF',
                'textColor' => $textColor,
            ];
        }

        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        // On ajoute les fériers au calendrier des congés
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, true)),
            RecursiveIteratorIterator::SELF_FIRST);
        foreach ($jsonIterator as $key => $val) {
            $rdvs[] = [
                'id' => '',
                'start' => $key,
                'end' => $key,
                'title' => $val,
                'backgroundColor' => '#404040',
                'borderColor' => '#FFFFFF',
                'textColor' => '#FFFFFF',
            ];
        }

        // Les anniversaires des utilisateurs

        foreach ($users as $key => $value) {
            $annif = $value->getBornAt()->format('m-d');
            $annee = date("Y") - 1;
            $annee2 = date("Y") + 3;
            for ($ligAnnee = $annee; $ligAnnee < $annee2; $ligAnnee++) {
                $anniversaire = $ligAnnee . '-' . $annif;

                $rdvs[] = [
                    'id' => '',
                    'start' => $anniversaire,
                    'end' => $anniversaire,
                    'title' => 'Anniversaire ' . $value->getPseudo(),
                    'backgroundColor' => '#FF9BFF',
                    'borderColor' => '#D7D7D7',
                    'textColor' => '#FFFFFF',
                ];
            }
        }

        $data = json_encode($rdvs);

        return $this->render('holiday/index.html.twig', [
            'holidays' => $holidays,
            'title' => 'Liste des congés',
            'data' => $data,
        ]);
    }

    #[Route("/conges/holiday/fermeture", name: "app_holiday_new_closing", methods: ["GET", "POST"])]

    public function newClosing(Request $request)
    {
        $i = 0;
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $formExportExcel = $this->createForm(HolidayTypeDateDebutFinExcel::class);
        $formExportExcel->handleRequest($request);

        if ($formExportExcel->isSubmitted() && $formExportExcel->isValid()) {
            $start = $formExportExcel->getData()->getStart()->format('Y-m-d');
            $end = $formExportExcel->getData()->getEnd()->format('Y-m-d');

            $this->sendMail($start, $end);
        }

        $form = $this->createForm(ImposeVacationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listUsers = $form->getData()['user'];
            // adapter l'heure de start à la tranche de journée selectionnée
            $sliceStart = $form->getData()['sliceStart'];
            $start = $form->getData()['start'];
            $anneeStart = substr($form->getData()['start']->format('Y'), -4, 2);
            if ($anneeStart != 20) {
                $this->addFlash('danger', 'Votre année début n\'est pas cohérente, aucune information n\'a été enregistré');
                return $this->redirectToRoute('app_holiday_new_closing');
            }
            if ($sliceStart == 'PM') {
                $start = $start->modify("+14 hours");
            }
            // adapter l'heure de End à la tranche de journée selectionnée
            $sliceEnd = $form->getData()['sliceEnd'];
            $end = $form->getData()['end'];
            $anneeEnd = substr($form->getData()['end']->format('Y'), -4, 2);
            if ($anneeEnd != 20) {
                $this->addFlash('danger', 'Votre année de fin n\'est pas cohérente, aucune information n\'a été enregistré');
                return $this->redirectToRoute('app_holiday_new_closing');
            }
            if ($sliceEnd == 'AM') {
                $end = $end->modify("+12 hours");
            } elseif ($sliceEnd == 'PM') {
                $end = $end->modify("+18 hours");
            } elseif ($sliceEnd == 'DAY') {
                $end = $end->modify("+23 hours");
            }

            $id = '';

            //intégrer le congés pour chaque utilisateurs
            foreach ($listUsers as $value) {

                // vérifier si cette utilisateur n'a pas déjà déposé durant cette période
                $holiday_id = 0;
                $result = $this->repoHoliday->getAlreadyInHolidayInThisPeriod($start, $end, $value->getId(), $holiday_id);

                if ($result) {
                    $this->addFlash('danger', $value->getpseudo() . ' a déjà posé du ' . $result[0]['start'] . ' au ' . $result[0]['end'] . ' aucun congés n\'a été ajouté pour cette utilisateur');
                    goto end;
                }

                $holiday = new Holiday;

                $statut = $this->repoStatuts->findOneBy(['id' => 3]);
                $holiday->setCreatedAt(new DateTime())
                    ->setStart($start)
                    ->setSliceStart($sliceStart)
                    ->setEnd($end)
                    ->setSliceEnd($sliceEnd)
                    ->setNbJours(0)
                    ->setHolidayType($form->getData()['holidayType'])
                    ->setHolidayStatus($statut)
                    ->setTreatmentedBy($this->getUser())
                    ->setTreatmentedAt(new DateTime())
                    ->setUser($value);
                $em = $this->entityManager;
                $em->persist($holiday);
                $em->flush();
                $majHoliday = $this->repoHoliday->findOneBy(['id' => $this->repoHoliday->getLastHoliday()]);
                $nbJours = $this->countHolidayDay($majHoliday);
                if ($nbJours['nbjours'] == -1) {
                    $this->addFlash('danger', 'données irrationnelles');
                    return $this->redirectToRoute('app_holiday_new');
                }
                $nbJ = $nbJours['nbjours'];
                $majHoliday->setNbJours($nbJ);
                $em = $this->entityManager;
                $em->persist($majHoliday);
                $em->flush();
                $i++;

                // Avertir l'utilisateur par mail
                $html = $this->renderView('mails/ImposeHoliday.html.twig', ['holiday' => $holiday]);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($value->getEmail())
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('Dépôt d\'un nouveau congés en votre nom')
                    ->html($html);

                $this->mailerInterface->send($email);
                end: ;
            }
            if ($i > 1) {
                $this->addFlash('message', 'Les congés ont bien été enregistrés');
            }
            return $this->redirectToRoute('app_holiday_list');

        }
        $users = $this->repoUser->findAll();
        $listCountConges = '';
        $typesDeConge = [];
        $formattedData = [];
        $formDates = $this->createForm(StatesDateFilterType::class);
        $formDates->handleRequest($request);
        if ($formDates->isSubmitted() && $formDates->isValid()) {
            $start = $formDates->getData()['startDate']->format('Y-m-d');
            $end = $formDates->getData()['endDate']->format('Y-m-d');

            $listCountConges = $this->repoHoliday->getListeCongesDurantPeriode($start, $end);
            // TODO Modifier la page fermeture pour qu'elle utilise le même procédé (ajouter des colonnes)

            for ($d = 0; $d < count($listCountConges); $d++) {

                $sd = $this->sliceTraduct($listCountConges[$d]['sliceStart']);
                $se = $this->sliceTraduct($listCountConges[$d]['sliceEnd']);
                $compter = $this->countDaysIntoThisPeriod($start, $listCountConges[$d]["startCp"], $sd, $end, $listCountConges[$d]['endCp'], $se);
                // les dates du formulaire
                $listCountConges[$d]['sliceStart'] = $sd;
                $listCountConges[$d]['sliceEnd'] = $se;
                $listCountConges[$d]['periodeStart'] = $start;
                $listCountConges[$d]['periodeEnd'] = $end;
                // le nombre de jours comptés
                $listCountConges[$d]['NbeJoursPeriode'] = $compter;
            }

            // Types de congé
            $typesDeConge = [
                'Congés Payés',
                'RTT',
                'Sans Solde',
                'Familiale',
                'Maternité',
                'Décès',
                'Déménagement',
                'Arrêt de travail',
                'Arrêt Covid',
                'Autre',
            ];

            // Initialisation des types de congé pour chaque nom
            foreach ($listCountConges as $conge) {
                $nom = $conge['nom'];

                if (!isset($formattedData[$nom])) {
                    $formattedData[$nom] = array_fill_keys($typesDeConge, 0);
                    $formattedData[$nom]['total'] = 0; // Ajout de la colonne "Total"
                }
            }

            // Remplissage des types de congé avec les valeurs réelles et calcul du total
            foreach ($listCountConges as $conge) {
                $nom = $conge['nom'];
                $type = $conge['typeCp'];
                $nbeJours = $conge['NbeJoursPeriode'];

                if (isset($formattedData[$nom][$type])) {
                    $formattedData[$nom][$type] += $nbeJours;
                    $formattedData[$nom]['total'] += $nbeJours; // Calcul du total
                }
            }
        }

        return $this->render('holiday/closing.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
            'formDates' => $formDates->createView(),
            'formExportExcel' => $formExportExcel->createView(),
            'listCountConges' => $formattedData,
            'typesDeConge' => $typesDeConge,
        ]);
    }

    #[Route("/holiday/new", name: "app_holiday_new", methods: ["GET", "POST"])]
    #[Route("/holiday/edit/{id}", name: "app_holiday_edit", methods: ["GET", "POST"])]

    function Holiday(Request $request, Holiday $holiday = null, $id = null)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($id) {
            // Si on est pas le dépositaire ou le décideur pas accés à la modification
            if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};

            // bloquer la modification si le congés est déjà traité
            if ($this->holiday_lock($id) == true) {return $this->redirectToRoute('app_holiday_list');};
        } else {
            $holiday = new Holiday;
        }

        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier la cohérence des années
            $anneeStart = substr($holiday->getStart()->format('Y'), -4, 2);
            if ($anneeStart != 20) {
                $this->addFlash('danger', 'Votre année début n\'est pas cohérente, aucune information n\'a été enregistré');
                return $this->redirectToRoute($tracking);
            }
            $anneeEnd = substr($holiday->getEnd()->format('Y'), -4, 2);
            if ($anneeEnd != 20) {
                $this->addFlash('danger', 'Votre année fin n\'est pas cohérente, aucune information n\'a été enregistré');
                return $this->redirectToRoute($tracking);
            }

            // vérifier si cette utilisateur n'a pas déjà déposé durant cette période
            if ($id) {
                $utilisateur = $holiday->getUser();
                $result = $this->repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $utilisateur->getId(), $holiday->getId());
            } else {
                $holiday_id = 0;
                $utilisateur = $this->getUser();
                $result = $this->repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $utilisateur->getId(), $holiday_id);
            }
            if ($result) {
                $this->addFlash('danger', 'Vous avez déjà posé du ' . $result[0]['start'] . ' au ' . $result[0]['end']);
                return $this->redirectToRoute('app_holiday_list');
            }
            // Liste de congés dans le même interval de date d'un service
            $overlaps = $this->repoHoliday->getOverlapHoliday($holiday->getStart(), $holiday->getEnd(), $utilisateur->getService()->getId());
            // On bascule les statuts des congés pour les mettres en chevauchement
            for ($ligOverlaps = 0; $ligOverlaps < count($overlaps); $ligOverlaps++) {
                if ($overlaps[$ligOverlaps]['statutId'] == 2) {
                    $statut = $this->repoStatuts->findOneBy(['id' => 1]);
                    $holiday->setHolidayStatus($statut);
                    $em = $this->entityManager;
                    $em->persist($holiday);
                    $em->flush();
                }
            }
            // Nombre de personne dans un service
            $countService['totalUsersService'] = count($this->repoUser->findBy(['service' => $utilisateur->getService()->getId()]));

            // Nombre de personne unique en congés durant cette période
            $personService = array();
            for ($ligService = 0; $ligService < count($overlaps); $ligService++) {
                $personService[$ligService]['user'] = $overlaps[$ligService]['users_id'];
            }
            $countService['totalUsersServiceInTime'] = count(array_values(array_unique($personService, SORT_REGULAR)));

            // Le nombre de personne présentent dans le service durant la période

            $countService['nbPersonPresent'] = $countService['totalUsersService'] - $countService['totalUsersServiceInTime'];

            // création d'une demande de congés
            if ($countService['totalUsersServiceInTime'] > 1) {
                $statut = $this->repoStatuts->findOneBy(['id' => 1]);
            } else {
                $statut = $this->repoStatuts->findOneBy(['id' => 2]);
            }
            // adapter l'heure de start à la tranche de journée selectionnée
            $sliceStart = $form->getData()->getSliceStart();
            $start = $form->getData()->getStart();
            if ($sliceStart == 'PM') {
                $start = $start->modify("+14 hours");
            }
            // adapter l'heure de End à la tranche de journée selectionnée
            $sliceEnd = $form->getData()->getSliceEnd();
            $end = $form->getData()->getEnd();
            if ($sliceEnd == 'AM') {
                $end = $end->modify("+12 hours");
            } elseif ($sliceEnd == 'PM') {
                $end = $end->modify("+18 hours");
            } elseif ($sliceEnd == 'DAY') {
                $end = $end->modify("+23 hours");
            }
            $nbJours = $this->countHolidayDay($holiday);
            if ($nbJours['nbjours'] == -1) {
                $this->addFlash('danger', 'données irrationnelles');
                return $this->redirectToRoute('app_holiday_new');
            }

            $nbJ = $nbJours['nbjours'];
            $holiday->setCreatedAt(new DateTime())
                ->setStart($start)
                ->setEnd($end)
                ->setNbJours($nbJ)
                ->setHolidayStatus($statut)
                ->setUser($utilisateur);
            $em = $this->entityManager;
            $em->persist($holiday);
            $em->flush();

            // récupérer le dernier id de congés
            $lastHoliday = $this->repoHoliday->getLastHoliday();
            $holiday = $this->repoHoliday->findOneBy(['id' => $lastHoliday]);
            // envoie d'un email au dépositaire
            $role = 'depositaire';
            if (!$id) {
                $object = 'Votre demande de congés a bien été prise en compte !';
                $message_flash = 'Demande de congés déposé avec succès';
                $id = $holiday->getId();
            } else {
                $object = 'Votre modification de congés a bien été prise en compte !';
                $message_flash = 'Modification de congés effectué avec succès';
            }
            $html = $this->renderView('mails/requestHoliday.html.twig', ['holiday' => $holiday]);
            $this->holiday_send_mail($role, $id, $object, $html);

            // envoie d'un email aux décisionnaires
            $role = 'decisionnaire';
            if (!$id) {
                $object = 'Une nouvelle demande de congés a été déposé !';
                $id = $holiday->getId();
            } else {
                $object = 'Une nouvelle demande de congés a été effectuée !'; // modification d'un congés TODO
            }
            $html = $this->renderView('mails/requestDecideurHoliday.html.twig', ['holiday' => $holiday, 'overlaps' => $overlaps, 'countService' => $countService]);
            $this->holiday_send_mail($role, $id, $object, $html);

            $this->addFlash('message', $message_flash);
            return $this->redirectToRoute('app_holiday_list');
        }

        return $this->render('holiday/cp.html.twig', [
            'form' => $form->createView()]
        );
    }

    #[Route("/holiday/show/{id}", name: "app_holiday_show", methods: ["GET"])]

    // Voir un congés
    function showHoliday($id)
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);

        // Si on est pas le dépositaire ou le décideur pas accés à la modification
        if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};

        return $this->render('holiday/show.html.twig', [
            'holiday' => $holiday,
        ]);
    }
    // compter le nombre de jour de congés déposés
    function countHolidayDay($holiday)
    {

        // déclaration des variables
        $dateStart = $holiday->getStart()->format('Y-m-d');
        $dateEnd = $holiday->getEnd()->format('Y-m-d');
        $dd = new DateTime($holiday->getStart()->format('Y') . '-' . $holiday->getStart()->format('m') . '-' . $holiday->getStart()->format('d') . '00:00');
        $df = new DateTime($holiday->getEnd()->format('Y') . '-' . $holiday->getEnd()->format('m') . '-' . $holiday->getEnd()->format('d') . '23:00');
        $interval = DateInterval::createFromDateString('1 days');
        $days = new DatePeriod($dd, $interval, $df);
        //$dateTimeStart = new DateTime($holiday->getStart()->format('Y-m-d'));
        //$dateTimeEnd = new DateTime($holiday->getEnd()->format('Y-m-d'));
        $sliceStart = $holiday->getSliceStart();
        $sliceEnd = $holiday->getSliceEnd();

        // compter le nombre de jour déposer pour cette période
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");

        // Liste des fériers
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, true)),
            RecursiveIteratorIterator::SELF_FIRST);
        $ferierDurantConges = array();
        foreach ($jsonIterator as $key => $val) {
            if ($key >= $dateStart and $key <= $dateEnd) {
                $ferierDurantConges[] = $key;
            }
        }

        $nbConges = 0;
        $return = [];

        foreach ($days as $dt) {
            // le jour qui est actuellement dans la boucle
            // compter le nombre de jour de congés déposé hors weekend
            if ($dt->format('N') != 6 and $dt->format('N') != 7) {
                $ymd = $dt->format('Y-m-d');
                $searchFerier = in_array($ymd, $ferierDurantConges, true);
                // Ne pas tenir compte des jours fériers
                if ($searchFerier == false) {
                    // si le jour start et le jour end sont identiques
                    if ($dateStart == $dateEnd) {
                        if (($sliceStart == 'DAY' && $sliceEnd == 'AM') | ($sliceStart == 'PM' && $sliceEnd == 'DAY')) {
                            $return['nbjours'] = -1;
                            return $return;
                        }

                        // s'ils sont tous les 2 le matin ou l'aprés midi, on ne compte qu'une demi journée
                        if ($holiday->getStart()->format('H:i') == '00:00' && $holiday->getEnd()->format('H:i') == '12:00') {
                            $nbConges = $nbConges + 0.5;
                            // S'ils sont tous les 2 Journée ou l'un matin l'autre aprés midi on ajoute un jour
                        }
                        if ($holiday->getStart()->format('H:i') == '14:00' && $holiday->getEnd()->format('H:i') == '18:00') {
                            $nbConges = $nbConges + 0.5;
                        }
                        if ($holiday->getStart()->format('H:i') == '00:00' && $holiday->getEnd()->format('H:i') == '23:00') {
                            $nbConges = $nbConges + 1;
                        }
                        if ($holiday->getStart()->format('H:i') == '00:00' && $holiday->getEnd()->format('H:i') == '18:00') {
                            $nbConges = $nbConges + 1;
                        }
                    } else {
                        // si le jour start est différent du jour end
                        // si on est sur le jour de start ou le jour end et qu'il ne s'agit pas de journée compléte
                        // l'aprés midi en date start
                        if ($ymd == $dateStart && $holiday->getStart()->format('H:i') == '14:00') {
                            $nbConges = $nbConges + 0.5;
                        }
                        // si on est sur le jour de démarrage et que le slice est sur DAY
                        // la journée compléte en start
                        if ($ymd == $dateStart && $holiday->getStart()->format('H:i') == '00:00') {
                            $nbConges = $nbConges + 1;
                        }
                        // la matinée en End
                        if ($ymd == $dateEnd && $holiday->getEnd()->format('H:i') == '12:00') {
                            $nbConges = $nbConges + 0.5;
                        }
                        // l'aprés midi en End
                        if ($ymd == $dateEnd && $holiday->getEnd()->format('H:i') == '18:00') {
                            $nbConges = $nbConges + 1;
                        }
                        // l'a journée en End
                        if ($ymd == $dateEnd && $holiday->getEnd()->format('H:i') == '23:00') {
                            $nbConges = $nbConges + 1;
                        }
                        // si les jours sont intermédiaires aux jours start et end, on ajoute un jour
                        if ($ymd != $dateStart && $ymd != $dateEnd) {
                            $nbConges = $nbConges + 1;
                        }
                    }
                }
            }
        }
        $return['nbjours'] = $nbConges;
        return $return;
    }

    #[Route("/holiday/delete/{id}", name: "app_holiday_delete")]

    // supprimer un congés
    function deleteHoliday($id, Holiday $holiday)
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);

        // Si on est pas le dépositaire ou le décideur pas accés à la modification
        if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};

        // bloquer la modification si le congés est déjà traité
        if ($this->holiday_lock($id) == true) {return $this->redirectToRoute('app_holiday_list');};

        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre congés a été supprimé !';
        $html = $this->renderView('mails/DeleteHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id, $object, $html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'Un congés a été supprimé ! inutile de le traiter';
        $html = $this->renderView('mails/DeleteHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id, $object, $html);

        // Supprimer le congés
        $entityManager = $this->entityManager;
        $entityManager->remove($holiday);
        $entityManager->flush();

        $this->addFlash('message', 'Suppression de congés effectué avec succés');
        return $this->redirectToRoute('app_holiday_list');
    }

    #[Route("/conges/holiday/accept/{id}", name: "app_holiday_accept", methods: ["GET"])]

    // accepter un congés
    function acceptHoliday($id)
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);
        $statut = $this->repoStatuts->findOneBy(['id' => 3]);

        $holiday->setTreatmentedAt(new DateTime())
            ->setTreatmentedBy($this->getUser())
            ->setHolidayStatus($statut);
        $em = $this->entityManager;
        $em->persist($holiday);
        $em->flush();

        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre demande de congés a été acceptée !';
        $html = $this->renderView('mails/AcceptHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id, $object, $html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'La validation du congés a bien été pris en compte !';
        $html = $this->renderView('mails/AcceptDecideurHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id, $object, $html);

        $this->addFlash('message', 'Le congés a été Accepté');
        return $this->redirectToRoute('app_holiday_list');
    }

    #[Route("/conges/holiday/refuse/{id}", name: "app_holiday_refuse", methods: ["GET"])]

    // refuser un congés
    function refuseHoliday($id)
    {
        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);

        $holiday->setTreatmentedAt(new DateTime())
            ->setTreatmentedBy($this->getUser())
            ->setHolidayStatus($this->repoStatuts->findOneBy(['id' => 4]));
        $em = $this->entityManager;
        $em->persist($holiday);
        $em->flush();

        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre demande de congés n\'a pas été acceptée';
        $html = $this->renderView('mails/RefuseHoliday.html.twig', ['holiday' => $holiday]);
        $this->holiday_send_mail($role, $id, $object, $html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'Le refus de congés a bien été pris en compte !';
        $html = $this->renderView('mails/RefuseDecideurHoliday.html.twig', ['holiday' => $holiday]);
        $this->holiday_send_mail($role, $id, $object, $html);

        $this->addFlash('danger', 'Le congés a été Refusé');
        return $this->redirectToRoute('app_holiday_list');
    }

    function countDaysIntoThisPeriod($start, $startCp, $ss, $end, $endCp, $se)
    {
        $sStart = false;
        $sEnd = false;

        if ($ss == 'Journée' | $ss == 'Matin') {
            $sStart = true;
        }
        if ($se == 'Journée' | $se == 'Aprés Midi') {
            $sEnd = true;
        }
        $vacationDays = 0;
        $holidays = [];
        $jsonUrl = "https://etalab.github.io/jours-feries-france-data/json/metropole.json";
        $ferierJson = file_get_contents($jsonUrl);
        $ferierData = json_decode($ferierJson, true);
        $holidays = array_keys($ferierData);

        $vacationDays = $this->countVacationDays($start, $startCp, $sStart, $end, $endCp, $sEnd, $holidays);

        return $vacationDays;
    }

    function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6); // 6 corresponds to Saturday, 7 to Sunday
    }

    function countVacationDays($start, $startCp, $ss, $end, $endCp, $se, $holidays)
    {
        $start = (new DateTime($start))->format('Y-m-d');
        $end = (new DateTime($end))->format('Y-m-d');
        $startCp = (new DateTime($startCp))->format('Y-m-d');
        $endCp = (new DateTime($endCp))->format('Y-m-d');
        $currentDay = (new DateTime($startCp))->format('Y-m-d');
        $vacationDays = 0;
        while ($currentDay <= $endCp) {
            if (!$this->isWeekend($currentDay) && !in_array($currentDay, $holidays)) {
                if ($currentDay >= $start && $currentDay <= $end) {
                    if (($currentDay == $startCp && $ss == false) | ($currentDay == $endCp && $se == false)) {
                        if ($startCp == $endCp && $currentDay == $startCp) {
                            $vacationDays = $vacationDays + 0.5;
                        } elseif ($startCp != $endCp) {
                            $vacationDays = $vacationDays + 0.5;
                        }
                    } else {
                        $vacationDays = $vacationDays + 1;
                    }
                }
            }
            $currentDay = (new DateTime($currentDay))->modify('+1 day')->format('Y-m-d');
        }

        return $vacationDays;
    }

    // Contrôle d'accés, Si on est pas le dépositaire ou le décideur pas accés
    function holiday_access($holidayId)
    {
        $access = true;
        $holiday = $this->repoHoliday->findOneBy(['id' => $holidayId]);
        if (($holiday->getUser()->getId() != $this->getUser()->getId()) and !$this->isGranted('ROLE_CONGES')) {
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            $access = false;
        }
        return $access;
    }

    // contrôle et modification des chevauchements
    function holiday_overlaps()
    {

    }

    // congés vérrouillé, bloquer la modification de date si le congés est déjà traité
    function holiday_lock($holidayId)
    {
        $lock = false;
        $repo = $this->entityManager->getRepository(Holiday::class);
        $holiday = $repo->find($holidayId);

        if ($holiday->getTreatmentedBy() != null) {
            $this->addFlash('danger', 'Vous ne pouvez plus modifier ce congés celui ci a été traité');
            $lock = true;
        }
        return $lock;
    }

    // envoie de mail
    function holiday_send_mail($role, $id, $object, $html)
    {
        // initialiser le mail utilisateur
        $userMail = '';
        // envoyer un mail à tous les décideurs
        if ($role == 'decisionnaire') {
            $decisionnairesMails = $this->repoHoliday->getMailDecideurConges();

            for ($i = 0; $i < count($decisionnairesMails); $i++) {
                $MailsList[] = new Address($decisionnairesMails[$i]['email']);
            }

            //dd($MailsList);
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to(...$MailsList)
                ->priority(Email::PRIORITY_HIGH)
                ->subject($object)
                ->html($html);

            $this->mailerInterface->send($email);
        }
        // envoyer un mail au dépositaire
        if ($role == 'depositaire') {
            // Chercher le mail du dépositaire du congés
            $userMail = $this->repoUser->getFindEmail($id);
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($userMail)
                ->priority(Email::PRIORITY_HIGH)
                ->subject($object)
                ->html($html);

            $this->mailerInterface->send($email);
        }

    }

    // générer un fichier Excel qui sera envoyé par mail à l'utilisateur
    function getDataRecapConges($start, $end): array
    {
        $list = [];
        $donnees = [];

        $donnees = $this->repoHoliday->getVacationTypeListByUsers($start, $end);
        // Créer un nouveau tableau contenant des enregistrements uniques basés sur "nom" et "email"
        $donnees = array_reduce($donnees, function ($carry, $item) {
            $key = $item['pseudo'] . '_' . $item['email'];

            if (!isset($carry[$key])) {
                $carry[$key] = array(
                    "pseudo" => $item['pseudo'],
                    "email" => $item['email'],
                );
            }

            return $carry;
        }, []);

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];
            $list[] = [
                $donnee['pseudo'],
                $donnee['email'],
            ];
        }
        return $list;
    }

    // Mise en forme des slices
    function sliceTraduct($slice)
    {
        if ($slice == 'DAY') {
            return 'Journée';
        } elseif ($slice == 'AM') {
            return 'Matin';
        } elseif ($slice == 'PM') {
            return 'Aprés Midi';
        }
    }

    // générer un fichier Excel qui sera envoyé par mail à l'utilisateur
    function getDataListConges($start, $end): array
    {
        $list = [];
        $donnees = [];

        $donnees = $this->repoHoliday->getListeCongesDurantPeriode($start, $end);
        // TODO Modifier la page fermeture pour qu'elle utilise le même procédé (ajouter des colonnes)

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];
            $sd = $this->sliceTraduct($donnee['sliceStart']);
            $se = $this->sliceTraduct($donnee['sliceEnd']);

            $compter = $this->countDaysIntoThisPeriod($start, $donnee["startCp"], $sd, $end, $donnee['endCp'], $se);
            // les dates du formulaire
            $donnee['periodeStart'] = $start;
            $donnee['periodeEnd'] = $end;
            // le nombre de jours comptés
            $donnee['NbeJoursPeriode'] = $compter;

            $list[] = [
                $donnee['nom'],
                $donnee['startCp'],
                $sd,
                $donnee['endCp'],
                $se,
                $donnee['createdAtHoliday'],
                $donnee['treatmentedAt'],
                $donnee['treatmentedBy_id'],
                $donnee['nbJours'],
                $donnee['typeCp'],
                $donnee['statut'],
                strip_tags($donnee['details']), // pour ne pas avoir les balises HTML
                $donnee['periodeStart'],
                $donnee['periodeEnd'],
                $donnee['NbeJoursPeriode'],
            ];
        }
        return $list;
    }

    function get_export_excel($start, $end)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('detail');
        // Entête de colonne
        $sheet->getCell('A5')->setValue('Salarié');
        $sheet->getCell('B5')->setValue('Date Début');
        $sheet->getCell('C5')->setValue('Tranche Début');
        $sheet->getCell('D5')->setValue('Date Fin');
        $sheet->getCell('E5')->setValue('Tranche Fin');
        $sheet->getCell('F5')->setValue('Créé le');
        $sheet->getCell('G5')->setValue('Traité le');
        $sheet->getCell('H5')->setValue('Traité par');
        $sheet->getCell('I5')->setValue('Nbre Jours');
        $sheet->getCell('J5')->setValue('Type');
        $sheet->getCell('K5')->setValue('Statut');
        $sheet->getCell('L5')->setValue('Détails');
        $sheet->getCell('M5')->setValue('Période début interrogée');
        $sheet->getCell('N5')->setValue('Période fin interrogée');
        $sheet->getCell('O5')->setValue('Nbe Jours période interrogée');

        // Increase row cursor after header write
        $sheet->fromArray($this->getDataListConges($start, $end), null, 'A6', true);
        $dernLign = count($this->getDataListConges($start, $end)) + 5;

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $dd = $start;
        $df = $end;
        $nomFichier = 'Détail congés du ' . $dd . ' au ' . $df . ' le ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);

        // Le style du tableau
        $styleArray = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle("A5:O{$dernLign}")->applyFromArray($styleArray);

        // Le style de l'entête
        $styleEntete = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '9B5BCA',
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle("A5:O5")->applyFromArray($styleEntete);

        $sheet->getStyle("A5:O{$dernLign}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Espacement automatique sur toutes les colonnes sauf la A
        $sheet->setAutoFilter("A5:O{$dernLign}");
        $sheet->getColumnDimension('A')->setWidth(90, 'pt');
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);

        // Create a new worksheet called "My Data"
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'resume');

        // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
        $spreadsheet->addSheet($myWorkSheet, 0);

        $sheetResume = $spreadsheet->getSheetByName('resume');

        // Entête de colonne
        $sheetResume->getCell('A5')->setValue('Pseudo');
        $sheetResume->getCell('B5')->setValue('Email');
        $sheetResume->getCell('C5')->setValue('Congés Payés');
        $sheetResume->getCell('D5')->setValue('RTT');
        $sheetResume->getCell('E5')->setValue('Sans Solde');
        $sheetResume->getCell('F5')->setValue('Familiale');
        $sheetResume->getCell('G5')->setValue('Maternité');
        $sheetResume->getCell('H5')->setValue('Décés');
        $sheetResume->getCell('I5')->setValue('Déménagement');
        $sheetResume->getCell('J5')->setValue('Arrêt de travail');
        $sheetResume->getCell('K5')->setValue('Arrêt Covid');
        $sheetResume->getCell('L5')->setValue('Autre');
        $sheetResume->getCell('M5')->setValue('Total');

        // Extraire les valeurs uniques de la colonne spécifiée
        $donnees = $this->repoHoliday->getVacationTypeListByUsers($start, $end);
        $donnees = array_reduce($donnees, function ($carry, $item) {
            $key = $item['pseudo'] . '_' . $item['email'];

            if (!isset($carry[$key])) {
                $carry[$key] = array(
                    "pseudo" => $item['pseudo'],
                    "email" => $item['email'],
                );
            }

            return $carry;
        }, []);
        $sheetResume->fromArray($donnees, null, 'A6', true);
        $dernLignResume = count($donnees) + 5;

        for ($i = 6; $i <= $dernLignResume; $i++) {
            $sheetResume->setCellValue("C" . $i, "=SUMIFS(detail!O:O,detail!J:J,C5,detail!A:A,A" . $i . ")"); // Congés Payés
            $sheetResume->setCellValue("D" . $i, "=SUMIFS(detail!O:O,detail!J:J,D5,detail!A:A,A" . $i . ")"); // RTT
            $sheetResume->setCellValue("E" . $i, "=SUMIFS(detail!O:O,detail!J:J,E5,detail!A:A,A" . $i . ")"); // Sans Solde
            $sheetResume->setCellValue("F" . $i, "=SUMIFS(detail!O:O,detail!J:J,F5,detail!A:A,A" . $i . ")"); // Familiale
            $sheetResume->setCellValue("G" . $i, "=SUMIFS(detail!O:O,detail!J:J,G5,detail!A:A,A" . $i . ")"); // Maternité
            $sheetResume->setCellValue("H" . $i, "=SUMIFS(detail!O:O,detail!J:J,H5,detail!A:A,A" . $i . ")"); // Décés
            $sheetResume->setCellValue("I" . $i, "=SUMIFS(detail!O:O,detail!J:J,I5,detail!A:A,A" . $i . ")"); // Déménagement
            $sheetResume->setCellValue("J" . $i, "=SUMIFS(detail!O:O,detail!J:J,J5,detail!A:A,A" . $i . ")"); // Arrêt de travail
            $sheetResume->setCellValue("K" . $i, "=SUMIFS(detail!O:O,detail!J:J,K5,detail!A:A,A" . $i . ")"); // Arrêt Covid
            $sheetResume->setCellValue("L" . $i, "=SUMIFS(detail!O:O,detail!J:J,L5,detail!A:A,A" . $i . ")"); // Autre
            $sheetResume->setCellValue("M" . $i, "=SUM(C" . $i . ":L" . $i . ")"); // Total
        }

        $nomFichierResume = 'Résumé des congés du ' . $dd . ' au ' . $df . ' le ' . $dateTime;

        // Titre de la feuille
        $sheetResume->getCell('A1')->setValue($nomFichierResume);
        $sheetResume->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheetResume->getCell('A1')->getStyle()->getFont()->setUnderline(true);

        // Le style du tableau
        $styleArrayCompte = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $spreadsheet->getSheetByName('resume')->getStyle("A5:M{$dernLignResume}")->applyFromArray($styleArrayCompte);

        // Le style de l'entête
        $styleEnteteCompte = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '9B5BCA',
                ],
            ],
        ];

        $spreadsheet->getSheetByName('resume')->getStyle("A5:M5")->applyFromArray($styleEnteteCompte);
        $sheet->getStyle("A1:M{$dernLignResume}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheetResume->getColumnDimension('A')->setWidth(90, 'pt');
        $sheetResume->getColumnDimension('B')->setAutoSize(true);
        $sheetResume->getColumnDimension('C')->setAutoSize(true);
        $sheetResume->getColumnDimension('D')->setAutoSize(true);
        $sheetResume->getColumnDimension('E')->setAutoSize(true);
        $sheetResume->getColumnDimension('F')->setAutoSize(true);
        $sheetResume->getColumnDimension('G')->setAutoSize(true);
        $sheetResume->getColumnDimension('H')->setAutoSize(true);
        $sheetResume->getColumnDimension('I')->setAutoSize(true);
        $sheetResume->getColumnDimension('J')->setAutoSize(true);
        $sheetResume->getColumnDimension('K')->setAutoSize(true);
        $sheetResume->getColumnDimension('L')->setAutoSize(true);
        $sheetResume->getColumnDimension('M')->setAutoSize(true);

        $sheetResume->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        // Return the excel file as an attachment

        $chemin = 'doc/CP/';
        $fichier = $chemin . '/' . $fileName;
        $writer->save($fichier);
        return $fichier;
    }

    function sendMail($start, $end)
    {
        // envoyer un mail
        $excel = $this->get_export_excel($start, $end);
        $html = $this->renderView('mails/sendMailSvgConges.html.twig');
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->getUser()->getEmail())
            ->subject('Sauvegarde des congés du ' . $start . ' au ' . $end)
            ->html($html)
            ->attachFromPath($excel);
        $this->mailer->send($email);
        unlink($excel);

        $this->addFlash('message', 'Consultez votre boite mail....');
        return $this->redirectToRoute('app_holiday_new_closing');
    }

}
