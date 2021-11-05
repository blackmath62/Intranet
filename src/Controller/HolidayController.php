<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Form\HolidayType;
use RecursiveArrayIterator;
use App\Entity\Main\Holiday;
use RecursiveIteratorIterator;
use App\Form\ClosingSocityType;
use App\Form\ImposeVacationType;
use Symfony\Component\Mime\Email;
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
 * @IsGranted("ROLE_INFORMATIQUE")
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
        $data = $this->repoHoliday->findBy([], ['id' => 'DESC']);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('holiday/index.html.twig', [
            'data' => $data
        ]);
    }

    
    /**
     * @Route("/holiday/fermeture", name="app_holiday_new_closing", methods={"GET","POST"})
     */
    public function newClosing(Request $request, Holiday $holiday = null)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = new Holiday;

        $form = $this->createForm(ImposeVacationType::class, $holiday);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            dd($form->getData());
        }
        return $this->render('holiday/closing.html.twig',[
            'form' => $form->createView()
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
            $result = $this->repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $this->getUser()->getId(), $holiday->getId());
            if ($result) {
                $this->addFlash('danger', 'Vous avez déjà posé du ' . $result[0]['start'] . ' au ' . $result[0]['end'] );
                return $this->redirectToRoute('app_holiday_list');
                dd('On ne va pas plus loin que ça');
            }
            // Liste de congés dans le même interval de date d'un service
            $overlaps = $this->repoHoliday->getOverlapHoliday($holiday->getStart(), $holiday->getEnd(),$this->getUser()->getService()->getId());
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
            $countService['totalUsersService'] = count($this->repoUser->findBy(['service' =>$this->getUser()->getService()->getId() ]));

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
            //dd($nbJours);
            $nbJ = $nbJours['nbjours'];
            dd($nbJours);
            $holiday->setCreatedAt(new DateTime())
                    ->setStart($start)
                    ->setEnd($end)
                    ->setNbJours($nbJ)
                    ->setHolidayStatus($statut)
                    ->addUser($this->getUser());
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
        $dateTimeStart = new DateTime($holiday->getStart()->format('Y-m-d'));
        $dateTimeEnd = new DateTime($holiday->getEnd()->format('Y-m-d'));
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
        $texte1 = '';
        $texte3 = '';
        $texte4 = '';
        $texte5 = '';
        $texte2 = '';
        $texte6 = '';
        $texte7 = '';
            // le probléme vient de cette boucle, apparement il ne prendre pas en compte le dernier jour, c'est bizarre .... à voir si le probléme ne vient pas de modify qui modifie surement sur la variable de maniére permanente
            foreach (new DatePeriod($dateTimeStart, new DateInterval('P1D') /* pas d'un jour */, $dateTimeEnd->modify('+1 day')) as $dt) {
            // le jour qui est actuellement dans la boucle
            // compter le nombre de jour de congés déposé hors weekend
            if ($dt->format('N') != 6 AND $dt->format('N') != 7 ) 
            {
                $ymd = $dt->format('Y-m-d');
                $texte1 = $ymd . " n\'est pas un jour du weekend" . "</br>";
                $searchFerier = in_array($ymd, $ferierDurantConges,true);
                // Ne pas tenir compte des jours fériers
                if ($searchFerier == false) 
                {
                    $texte2 = $ymd . " n\'est pas un jour ferier";
                    // si le jour start et le jour end sont identiques    
                    if ($dateTimeStart->format('Y-m-d') == $dateTimeEnd->format('Y-m-d')) 
                    {
                        // s'ils sont tous les 2 le matin ou l'aprés midi, on ne compte qu'une demi journée
                        if ($sliceStart != 'AM' && $sliceEnd != 'AM' | $sliceStart != 'PM' && $sliceEnd != 'PM') {
                            $nbConges = 0.5;
                            $texte3 = $ymd . ' j\'ajoute 0.5 jour, dans jour identique tous les 2 matins ou aprés midi';
                            // S'ils sont tous les 2 Journée ou l'un matin l'autre aprés midi on ajoute un jour
                        }elseif ($sliceStart == 'DAY' && $sliceEnd == 'DAY' | $sliceStart == 'AM' && $sliceEnd == 'PM') {
                            $nbConges = 1;
                            $texte4 = $ymd . ' j\'ajoute 1 jour, dans jour identique journée compléte';
                        }
                    }else 
                    {
                        // si le jour start est différent du jour end
                        // si on est sur le jour de start ou le jour end et qu'il ne s'agit pas de journée compléte
                        if ($ymd == $dateTimeStart->format('Y-m-d') && $sliceStart != 'DAY'  | $ymd == $dateTimeEnd->format('Y-m-d') && $sliceEnd != 'DAY' ) {
                            $nbConges = $nbConges + 0.5;
                            $texte5 = $ymd . ' j\'ajoute 0.5 jour, dans jours différentes  et différents de journée compléte';
                        }elseif ($ymd == $dateTimeStart->format('Y-m-d') && $sliceStart == 'DAY'  | $ymd == $dateTimeEnd->format('Y-m-d') && $sliceEnd == 'DAY' ) {
                        // Si il s'agit de journée compléte dans les 2 cas on ajoute 1 journée
                            $nbConges++;
                            $texte6 = $ymd . ' j\'ajoute 1 jour, dans jours différentes  demi journée';
                        }
                        // si les jours sont intermédiaires aux jours start et end, on ajoute un jour
                        if ($ymd != $dateTimeStart->format('Y-m-d') && $ymd != $dateTimeEnd->format('Y-m-d')) {
                            
                            $nbConges++;
                            $texte7 = $ymd . ' est différent de ' . $dateTimeEnd->format('Y-m-d') . ' j\'ajoute 1 jour, dans jours différents  interval';
                        }    
                    }      
                }
            }
        }
        $return['texte'] = $texte1 . ', ' . $texte2 . ', ' . $texte3 . ', ' . $texte4 . ', ' . $texte5 . ', ' . $texte6 . ', ' . $texte7;
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
     * @Route("/holiday/accept/{id}", name="app_holiday_accept", methods={"GET"})
     * @IsGranted("ROLE_CONGES")
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
     * @Route("/holiday/refuse/{id}", name="app_holiday_refuse", methods={"GET"})
     * @IsGranted("ROLE_CONGES")
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
        $holiday = $this->repoHoliday->getUserIdHoliday($holidayId);
        
        if ( ($holiday['users_id'] != $this->getUser()->getId() ) and !$this->isGranted('ROLE_CONGES') ){
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            $access = false;
        }        
        return $access;
    }
    
    // Compter le nombre de jours de congés
    public function holiday_count()
    {

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
