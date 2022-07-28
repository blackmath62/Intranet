<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Form\HolidayType;
use RecursiveArrayIterator;
use App\Entity\Main\Holiday;
use RecursiveIteratorIterator;
use App\Form\ImposeVacationType;
use Symfony\Component\Mime\Email;
use App\Form\StatesDateFilterType;
use Symfony\Component\Mime\Address;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\HolidayRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\statusHolidayRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
*/

class HolidayController extends AbstractController
{

    private $mailerInterface;
    private $repoHoliday;
    private $repoStatuts;
    private $repoUser;

    public function __construct(MailerInterface $mailerInterface, HolidayRepository $repoHoliday,statusHolidayRepository $repoStatuts, UsersRepository $repoUser)
    {
        $this->mailerInterface = $mailerInterface;
        $this->repoHoliday = $repoHoliday;
        $this->repoStatuts = $repoStatuts;
        $this->repoUser = $repoUser;
    }

    /**
     * @Route("/holiday", name="app_holiday_list")
     */
    public function index(Request $request)
    {              
        $holidays = $this->repoHoliday->findAll();
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $users = $this->repoUser->findBy(['closedAt' => NULL]);
        
         // Calendrier des congés
         $events = $this->repoHoliday->findBy(['holidayStatus' => 3]);
         $rdvs = [];
         
         foreach($events as $event){
             $id = $event->getId();
             $userId = $this->repoHoliday->getUserIdHoliday($id);
             $user = $this->repoUser->findOneBy(['id' => $userId]);
             $pseudo = $user->getPseudo();
             $color = $user->getService()->getColor();
             $textColor = $user->getService()->getTextColor();
             
             $rdvs[] = [
                 'id' => $event->getId(),
                 'start' => $event->getStart()->format('Y-m-d H:i:s'),
                 'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                 'title' => 'Congés ' . $pseudo,
                 'backgroundColor' => $color,
                 'borderColor' => '#FFFFFF',
                 'textColor' => $textColor,
                ];
            }
            
            
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        // On ajoute les fériers au calendrier des congés
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, TRUE)),
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
            for ($ligAnnee=$annee; $ligAnnee <$annee2 ; $ligAnnee++) { 
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
            'data' => $data
        ]);
    }

    
    /**
     * @Route("/conges/holiday/fermeture", name="app_holiday_new_closing", methods={"GET","POST"})
     */
    public function newClosing(Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        $form = $this->createForm(ImposeVacationType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid() ){
            $listUsers = $form->getData()['user'];
            // adapter l'heure de start à la tranche de journée selectionnée
                $sliceStart = $form->getData()['sliceStart'];
                $start = $form->getData()['start'];
                if ($sliceStart == 'PM') {
                    $start = $start->modify("+14 hours");
                }
                // adapter l'heure de End à la tranche de journée selectionnée
                $sliceEnd = $form->getData()['sliceEnd'];
                $end = $form->getData()['end'];
                if ($sliceEnd == 'AM') {
                    $end = $end->modify("+12 hours");
                }elseif ($sliceEnd == 'PM') {
                    $end = $end->modify("+18 hours");
                }elseif ($sliceEnd == 'DAY') {
                    $end = $end->modify("+23 hours");
                }
            //intégrer le congés pour chaque utilisateurs
            foreach ($listUsers as $value) {
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
                    $em = $this->getDoctrine()->getManager();
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
            $em = $this->getDoctrine()->getManager();
                    $em->persist($majHoliday);
                    $em->flush();

            }
            $this->addFlash('message', 'Le congés a bien été enregistré' );
            return $this->redirectToRoute('app_holiday_list');

        }
        $users = $this->repoUser->findAll();
        $listCountConges = '';
        $formDates = $this->createForm(StatesDateFilterType::class);
        $formDates->handleRequest($request);
        if($formDates->isSubmitted() && $formDates->isValid()){
            $start = $formDates->getData()['startDate']->format('Y-m-d');
            $end = $formDates->getData()['endDate']->format('Y-m-d');
            $listCountConges = $this->repoHoliday->getVacationTypeListByUsers($start, $end);
        }

        return $this->render('holiday/closing.html.twig',[
            'form' => $form->createView(),
            'users' => $users,
            'formDates' => $formDates->createView(),
            'listCountConges' => $listCountConges,
            ]);
    }
    
    
    /**
     * @Route("/holiday/new", name="app_holiday_new", methods={"GET","POST"})
     * @Route("/holiday/edit/{id}", name="app_holiday_edit", methods={"GET","POST"})
     */

    public function Holiday($id=null, Holiday $holiday = null, Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        if ($id) {
            // Si on est pas le dépositaire ou le décideur pas accés à la modification
            if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};
            
            // bloquer la modification si le congés est déjà traité
            if ($this->holiday_lock($id) == true) {return $this->redirectToRoute('app_holiday_list');};    
        }else{
            $holiday = new Holiday;
        }

        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);        
        if($form->isSubmitted() && $form->isValid() ){
            // vérifier si cette utilisateur n'a pas déjà déposé durant cette période
            if ($id) {
                $utilisateur = $holiday->getUser();
                $result = $this->repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $utilisateur->getId(), $holiday->getId());
            }else {
                $holiday_id = 0;
                $utilisateur = $this->getUser();
                $result = $this->repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $utilisateur->getId(), $holiday_id);
            }
            if ($result) {
                $this->addFlash('danger', 'Vous avez déjà posé du ' . $result[0]['start'] . ' au ' . $result[0]['end'] );
                return $this->redirectToRoute('app_holiday_list');
            }
            // Liste de congés dans le même interval de date d'un service
            $overlaps = $this->repoHoliday->getOverlapHoliday($holiday->getStart(), $holiday->getEnd(),$utilisateur->getService()->getId());
            // On bascule les statuts des congés pour les mettres en chevauchement
            for ($ligOverlaps=0; $ligOverlaps <count($overlaps) ; $ligOverlaps++) { 
                if ($overlaps[$ligOverlaps]['statutId'] == 2 ) {
                    $statut = $this->repoStatuts->findOneBy(['id' => 1]);
                    $holiday->setHolidayStatus($statut);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($holiday);
                    $em->flush();
                }
            }
            // Nombre de personne dans un service
            $countService['totalUsersService'] = count($this->repoUser->findBy(['service' => $utilisateur->getService()->getId() ]));

            // Nombre de personne unique en congés durant cette période
            $personService = array();
            for ($ligService=0; $ligService <count($overlaps) ; $ligService++) { 
                $personService[$ligService]['user'] = $overlaps[$ligService]['users_id'];
            }
            $countService['totalUsersServiceInTime'] = count(array_values(array_unique($personService, SORT_REGULAR)));
            
            // Le nombre de personne présentent dans le service durant la période

            $countService['nbPersonPresent'] = $countService['totalUsersService'] - $countService['totalUsersServiceInTime'];

            // création d'une demande de congés
            if ($countService['totalUsersServiceInTime'] > 1) {
                $statut = $this->repoStatuts->findOneBy(['id' => 1]);
            }else {
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
            }elseif ($sliceEnd == 'PM') {
                $end = $end->modify("+18 hours");
            }elseif ($sliceEnd == 'DAY') {
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();

            // récupérer le dernier id de congés
            $lastHoliday = $this->repoHoliday->getLastHoliday();
            $holiday = $this->repoHoliday->findOneBy(['id' => $lastHoliday['holiday_id']]);
            // envoie d'un email au dépositaire
            $role = 'depositaire';
            if (!$id) {
                $object = 'Votre demande de congés a bien été prise en compte !';
                $message_flash = 'Demande de congés déposé avec succès';
                $id = $holiday->getId();
            }else{
                $object = 'Votre modification de congés a bien été prise en compte !';
                $message_flash = 'Modification de congés effectué avec succès';
            }
            $html = $this->renderView('mails/requestHoliday.html.twig', ['holiday' => $holiday]);
            $this->holiday_send_mail($role, $id,$object,$html);

            // envoie d'un email aux décisionnaires
            $role = 'decisionnaire';
            if (!$id) {
                $object = 'Une nouvelle demande de congés a été déposé !';
                $id = $holiday->getId();
            }else{
                $object = 'Une modification de congés a été effectuée !';
            }
            $html = $this->renderView('mails/requestDecideurHoliday.html.twig', ['holiday' => $holiday, 'overlaps' => $overlaps,'countService' => $countService ]);
            $this->holiday_send_mail($role, $id,$object,$html);

            $this->addFlash('message', $message_flash);
            return $this->redirectToRoute('app_holiday_list');
        }    

        return $this->render('holiday/cp.html.twig',[
        'form' => $form->createView()]
        );
    }

    /**
     * @Route("/holiday/show/{id}", name="app_holiday_show", methods={"GET"})
     */
    // Voir un congés
    public function showHoliday($id, Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);

        // Si on est pas le dépositaire ou le décideur pas accés à la modification
        if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};
        
        return $this->render('holiday/show.html.twig',[
            'holiday' => $holiday
        ]);
    }
    // compter le nombre de jour de congés déposés 
    public function countHolidayDay($holiday){

        // déclaration des variables
        $dateStart = $holiday->getStart()->format('Y-m-d');
        $dateEnd = $holiday->getEnd()->format('Y-m-d');
        $dd = new DateTime($holiday->getStart()->format('Y') . '-' . $holiday->getStart()->format('m') . '-' . $holiday->getStart()->format('d') . '00:00');
        $df = new DateTime($holiday->getEnd()->format('Y') . '-' . $holiday->getEnd()->format('m') . '-' . $holiday->getEnd()->format('d') . '23:00');
        $interval = DateInterval::createFromDateString('1 days');
        $days = new DatePeriod($dd,$interval, $df);
        //$dateTimeStart = new DateTime($holiday->getStart()->format('Y-m-d'));
        //$dateTimeEnd = new DateTime($holiday->getEnd()->format('Y-m-d'));
        $sliceStart = $holiday->getSliceStart();
        $sliceEnd = $holiday->getSliceEnd();    

        // compter le nombre de jour déposer pour cette période
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        
        // Liste des fériers 
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);
        $ferierDurantConges = array();
        foreach ($jsonIterator as $key => $val) {
            if ($key >= $dateStart AND $key <= $dateEnd ) {
                $ferierDurantConges[] = $key;
            }
        }
        
        $nbConges = 0;
        $return= [];
              
        foreach ($days as $dt) {
                // le jour qui est actuellement dans la boucle
                // compter le nombre de jour de congés déposé hors weekend
                if ($dt->format('N') != 6 AND $dt->format('N') != 7 ) 
                {
                    $ymd = $dt->format('Y-m-d');
                    $searchFerier = in_array($ymd, $ferierDurantConges,true);
                    // Ne pas tenir compte des jours fériers
                    if ($searchFerier == false) 
                    {
                    // si le jour start et le jour end sont identiques  
                    if ($dateStart == $dateEnd) 
                    {
                        if (($sliceStart == 'DAY' && $sliceEnd == 'AM')|($sliceStart == 'PM' && $sliceEnd == 'DAY')) {
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
                    }else 
                    {   
                        // si le jour start est différent du jour end
                        // si on est sur le jour de start ou le jour end et qu'il ne s'agit pas de journée compléte
                        // l'aprés midi en date start
                        if ($ymd == $dateStart && $holiday->getStart()->format('H:i') == '14:00' ) {
                            $nbConges = $nbConges + 0.5;
                        }
                        // si on est sur le jour de démarrage et que le slice est sur DAY
                        // la journée compléte en start
                        if ($ymd == $dateStart && $holiday->getStart()->format('H:i') == '00:00') {
                            $nbConges = $nbConges + 1;
                        }
                        // la matinée en End
                        if ($ymd == $dateEnd && $holiday->getEnd()->format('H:i') == '12:00' ) {
                            $nbConges = $nbConges + 0.5;
                        }
                        // l'aprés midi en End
                        if ($ymd == $dateEnd && $holiday->getEnd()->format('H:i') == '18:00' ) {
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

    /**
     * @Route("/holiday/delete/{id}", name="app_holiday_delete")
     */
    // supprimer un congés
    public function deleteHoliday($id, Holiday $holiday, Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);
        
        // Si on est pas le dépositaire ou le décideur pas accés à la modification
        if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};

        // bloquer la modification si le congés est déjà traité
        if ($this->holiday_lock($id) == true) {return $this->redirectToRoute('app_holiday_list');};

        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre congés a été supprimé !';
        $html = $this->renderView('mails/DeleteHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id,$object,$html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'Un congés a été supprimé ! inutile de le traiter';
        $html = $this->renderView('mails/DeleteHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id,$object,$html);

        // Supprimer le congés
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($holiday);
        $entityManager->flush();

        $this->addFlash('message', 'Suppression de congés effectué avec succés');
        return $this->redirectToRoute('app_holiday_list');
    }

    /**
     * @Route("/conges/holiday/accept/{id}", name="app_holiday_accept", methods={"GET"})
     */
    // accepter un congés
    public function acceptHoliday($id, Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);
        $statut = $this->repoStatuts->findOneBy(['id' => 3]);
        
        
        $holiday->setTreatmentedAt(new DateTime())
        ->setTreatmentedBy($this->getUser())
        ->setHolidayStatus($statut);
        $em = $this->getDoctrine()->getManager();
        $em->persist($holiday);
        $em->flush();
        
        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre demande de congés a été acceptée !';
        $html = $this->renderView('mails/AcceptHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id,$object,$html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'La validation du congés a bien été pris en compte !';
        $html = $this->renderView('mails/AcceptDecideurHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id,$object,$html);

        $this->addFlash('message', 'Le congés a été Accepté');
        return $this->redirectToRoute('app_holiday_list');
    }


    /**
     * @Route("/conges/holiday/refuse/{id}", name="app_holiday_refuse", methods={"GET"})
     */
    // refuser un congés
    public function refuseHoliday($id, Request $request)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $holiday = $this->repoHoliday->findOneBy(['id' => $id]);
            
        $holiday->setTreatmentedAt(new DateTime())
                ->setTreatmentedBy($this->getUser())
                ->setHolidayStatus($this->repoStatuts->findOneBy(['id' => 4]));
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();


        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre demande de congés n\'a pas été acceptée';
        $html = $this->renderView('mails/RefuseHoliday.html.twig', ['holiday' => $holiday]);
        $this->holiday_send_mail($role, $id,$object,$html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'Le refus de congés a bien été pris en compte !';
        $html = $this->renderView('mails/RefuseDecideurHoliday.html.twig', ['holiday' => $holiday]);
        $this->holiday_send_mail($role, $id,$object,$html);

        $this->addFlash('danger', 'Le congés a été Refusé');
        return $this->redirectToRoute('app_holiday_list');
    }

    // Contrôle d'accés, Si on est pas le dépositaire ou le décideur pas accés
    public function holiday_access($holidayId)
    {
        $access = true;
        $holiday = $this->repoHoliday->findOneBy(['id' => $holidayId]);
        if ( ($holiday->getUser()->getId() != $this->getUser()->getId() ) and !$this->isGranted('ROLE_CONGES') ){
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            $access = false;
        }        
        return $access;
    }
    

    // contrôle et modification des chevauchements
    public function holiday_overlaps()
    {

    }

    // congés vérrouillé, bloquer la modification de date si le congés est déjà traité
    public function holiday_lock($holidayId)
    {
        $lock = false;
        $repo = $this->getDoctrine()->getRepository(Holiday::class);
        $holiday = $repo->find($holidayId);

        if ($holiday->getTreatmentedBy() <> null) {
            $this->addFlash('danger', 'Vous ne pouvez plus modifier ce congés celui ci a été traité');
            $lock = true;
        } 
        return $lock;
    }

    // autorité sur les congés
    public function holiday_authority()
    {

    }

    // envoie de mail 
    public function holiday_send_mail($role, $id,$object,$html)
    {
        // initialiser le mail utilisateur
        $userMail = '';
        // envoyer un mail à tous les décideurs
        if ($role == 'decisionnaire'){
            $decisionnairesMails = $this->repoHoliday->getMailDecideurConges();
            
            for ($i=0; $i <count($decisionnairesMails) ; $i++) { 
                $MailsList = [
                    new Address($decisionnairesMails[$i]['email']),
                ];
            }    
                $email = (new Email())
                ->from('intranet@groupe-axis.fr')
                ->to(...$MailsList)
                ->priority(Email::PRIORITY_HIGH)
                ->subject($object)
                ->html($html);
        
                $this->mailerInterface->send($email);
        }
        // envoyer un mail au dépositaire
        if ($role == 'depositaire'){
            // Chercher le mail du dépositaire du congés
            $userMail = $this->repoUser->getFindEmail($id)['email'] ;   
            $email = (new Email())
            ->from('intranet@groupe-axis.fr')
            ->to($userMail)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($object)
            ->html($html);
    
            $this->mailerInterface->send($email);
        }
        // envoyer un mail à toute la société TODO

    }
}
