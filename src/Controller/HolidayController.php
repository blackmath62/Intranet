<?php

namespace App\Controller;

use DateTime;
use ArrayObject;
use App\Form\HolidayType;
use App\Entity\Main\Users;
use RecursiveArrayIterator;
use App\Entity\Main\Holiday;
use RecursiveIteratorIterator;
use Symfony\Component\Mime\Email;
use App\Entity\Main\statusHoliday;
use Twig\Extensions\DateExtension;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\CalendarRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\statusHolidayRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_INFORMATIQUE")
*/

class HolidayController extends AbstractController
{

    private $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    /**
     * @Route("/holiday", name="app_holiday_list")
     */
    public function index(HolidayRepository $repo, Request $request)
    {              
        $data = $repo->findBy([], ['id' => 'DESC']);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('holiday/index.html.twig', [
            'data' => $data
        ]);
    }

     /**
     * @Route("/holiday/new", name="app_holiday_new", methods={"GET","POST"})
     * @Route("/holiday/edit/{id}", name="app_holiday_edit", methods={"GET","POST"})
     */

    public function Holiday($id=null, Holiday $holiday = null, Request $request, statusHolidayRepository $statuts, MailerInterface $mailerInterface, UsersRepository $repoUser, HolidayRepository $repoHoliday)
    {
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
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if($form->isSubmitted() && $form->isValid() ){
            // vérifier si cette utilisateur n'a pas déjà déposé durant cette période
            $result = $repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $this->getUser()->getId(), $holiday->getId());
            if ($result) {
                $this->addFlash('danger', 'Vous avez déjà posé du ' . $result[0]['start'] . ' au ' . $result[0]['end'] );
                unset($result);
                return $this->redirectToRoute('app_holiday_list');
            }
            
            // Liste de congés dans le même interval de date d'un service
            $overlaps = $repoHoliday->getOverlapHoliday($holiday->getStart(), $holiday->getEnd(),$this->getUser()->getService()->getId());
            // On bascule les statuts des congés pour les mettres en chevauchement
            for ($ligOverlaps=0; $ligOverlaps <count($overlaps) ; $ligOverlaps++) { 
                if ($overlaps[$ligOverlaps]['statutId'] == 2 ) {
                    $statut = $statuts->findOneBy(['id' => 1]);
                    $holiday->setHolidayStatus($statut);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($holiday);
                    $em->flush();
                }
            }
            // Nombre de personne dans un service
            $countService['totalUsersService'] = count($repoUser->findBy(['service' =>$this->getUser()->getService()->getId() ]));

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
                $statut = $statuts->findOneBy(['id' => 1]);
            }else {
                $statut = $statuts->findOneBy(['id' => 2]);
            }
            $holiday->setCreatedAt(new DateTime())
            ->setHolidayStatus($statut)
            ->addUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();

            // récupérer le dernier id de congés
            $holiday = $repoHoliday->getLastHoliday();

            // envoie d'un email au dépositaire
            $role = 'depositaire';
            if (!$id) {
                $object = 'Votre demande de congés a bien été prise en compte !';
                $message_flash = 'Demande de congés déposé avec succès';
            }else{
                $object = 'Votre modification de congés a bien été prise en compte !';
                $message_flash = 'Modification de congés effectué avec succès';
            }
            $html = $this->renderView('mails/requestHoliday.html.twig', ['holiday' => $holiday]);
            $mail = $this->holiday_send_mail($role, $id,$object,$html);

            // envoie d'un email aux décisionnaires
            $role = 'decisionnaire';
            if (!$id) {
                $object = 'Une nouvelle demande de congés a été déposé !';
            }else{
                $object = 'Une modification de congés a été effectuée !';
            }
            $html = $this->renderView('mails/requestDecideurHoliday.html.twig', ['holiday' => $holiday, 'overlaps' => $overlaps,'countService' => $countService ]);
            $mail = $this->holiday_send_mail($role, $id,$object,$html);

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
    public function showHoliday($id, Request $request, HolidayRepository $repo, IdeaBoxController $ideaBoxController)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $repo->findOneBy(['id' => $id]);

        // Si on est pas le dépositaire ou le décideur pas accés à la modification
        if ($this->holiday_access($id) == false) {return $this->redirectToRoute('app_holiday_list');};
                   
        // compter le nombre de jour déposer pour cette période , il est surement préférable de faire une page séparé pour compter les congés
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        
        // Liste des fériers 
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);
        $ferierDurantConges = array();
        foreach ($jsonIterator as $key => $val) {
            if ($key >= $holiday->getStart()->format('Y-m-d') AND $key <= $holiday->getEnd()->format('Y-m-d') ) {
                $ferierDurantConges[] = $key;
            }
        }    
        
        $debut_jour = $holiday->getStart()->format('d');
        $debut_mois = $holiday->getStart()->format('m');
        $debut_annee = $holiday->getStart()->format('Y');
        $debut_heure = $holiday->getStart()->format('H');
        $debut_minute = $holiday->getStart()->format('i');
        $debut_seconde = $holiday->getStart()->format('s');

        $fin_jour = $holiday->getEnd()->format('d');
        $fin_mois = $holiday->getEnd()->format('m');
        $fin_annee = $holiday->getEnd()->format('Y');
        $fin_heure = $holiday->getEnd()->format('H');
        $fin_minute = $holiday->getEnd()->format('i');
        $fin_seconde = $holiday->getEnd()->format('s');

        $debut_date = mktime($debut_heure, $debut_minute, $debut_seconde, $debut_mois, $debut_jour, $debut_annee);
        $fin_date = mktime($fin_heure, $fin_minute, $fin_seconde, $fin_mois, $fin_jour, $fin_annee);
        $nbConges = 0;
        for($i = $debut_date; $i <= $fin_date; $i+=86400)
        {  
            // essayer de voir pour compter le nombre de CP via le nombre de seconde
            // compter le nombre de jour de congés déposé hors weekend
            if (date("N",$i) != 6 AND date("N",$i) != 7 ) {
                        $nbConges++;
                        echo 'je passe par là';
                // déduire les jours fériers durant la période              
                if ($ferierDurantConges) {
                    foreach ($ferierDurantConges as $key => $value) {
                        if (date("Y-m-d",$i) == $value ) {
                            $nbConges = $nbConges - 1;
                        }
                    }
                }
            }
        }
        
        return $this->render('holiday/show.html.twig',[
            'holiday' => $holiday,
            'nbConges' => $nbConges,
        ]);
    }

    /**
     * @Route("/holiday/delete/{id}", name="app_holiday_delete")
     */
    // supprimer un congés
    public function deleteHoliday($id, Request $request, Holiday $holiday, HolidayRepository $repo)
    {
        $holiday = $repo->findOneBy(['id' => $id]);
        
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
    public function acceptHoliday($id, Request $request, HolidayRepository $repo, statusHolidayRepository $statuts, MailerInterface $mailerInterface, UsersRepository $repoUser)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $repo->findOneBy(['id' => $id]);
        $statut = $statuts->findOneBy(['id' => 3]);
        
        
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
    public function refuseHoliday($id, Request $request, HolidayRepository $repo, statusHolidayRepository $statuts)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $holiday = $repo->findOneBy(['id' => $id]);
        $statut = $statuts->findOneBy(['id' => 4]);
            
        $holiday->setTreatmentedAt(new DateTime())
                ->setTreatmentedBy($this->getUser())
                ->setHolidayStatus($statut);
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();


        // envoie d'un email au dépositaire
        $role = 'depositaire';
        $object = 'Votre demande de congés n\'a pas été acceptée';
        $html = $this->renderView('mails/RefuseHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id,$object,$html);

        // envoie d'un email aux décisionnaires
        $role = 'decisionnaire';
        $object = 'Le refus de congés a bien été pris en compte !';
        $html = $this->renderView('mails/RefuseDecideurHoliday.html.twig', ['holiday' => $holiday]);
        $mail = $this->holiday_send_mail($role, $id,$object,$html);

        $this->addFlash('danger', 'Le congés a été Refusé');
        return $this->redirectToRoute('app_holiday_list');
    }

    // Contrôle d'accés, Si on est pas le dépositaire ou le décideur pas accés
    public function holiday_access($holidayId)
    {
        $access = true;
        $repo = $this->getDoctrine()->getRepository(Holiday::class);
        $holiday = $repo->getUserIdHoliday($holidayId);
        
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
        //dd($lock);
        return $lock;
    }

    // autorité sur les congés
    public function holiday_authority()
    {

    }

    // envoie de mail 
    public function holiday_send_mail($role, $id,$object,$html)
    {
        $repoHoliday = $this->getDoctrine()->getRepository(Holiday::class);
        $repoUser = $this->getDoctrine()->getRepository(Users::class);
        // initialiser le mail utilisateur
        $userMail = '';
        // envoyer un mail à tous les décideurs
        if ($role == 'decisionnaire'){
            $decisionnairesMails = $repoHoliday->getMailDecideurConges();
            for ($i=0; $i <count($decisionnairesMails) ; $i++) { 
                $userMail = $decisionnairesMails[$i]['email'];
                $email = (new Email())
                ->from('intranet@groupe-axis.fr')
                ->to($userMail)
                ->priority(Email::PRIORITY_HIGH)
                ->subject($object)
                ->html($html);
        
                $this->mailerInterface->send($email);
            }    
        }
        // envoyer un mail au dépositaire
        if ($role == 'depositaire'){
            $userMail = $repoUser->getFindEmail($id)['email'] ;
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
