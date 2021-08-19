<?php

namespace App\Controller;

use DateTime;
use ArrayObject;
use App\Form\HolidayType;
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
    /**
     * @Route("/holiday", name="app_holiday_list")
     */
    public function index(HolidayRepository $repo, Request $request, statusHolidayRepository $statuts, IdeaBoxController $ideaBoxController, TranslatorInterface $translator)
    {
        // liste des congés avec les services
        $holidayList = $repo->getListeCongesEtServices();        
        
        

        // mettre à jour les en attente de validation chevauchement et Libre
        /*$stat = 0;
        // on balaye la liste
        for ($ligHolidayList=0; $ligHolidayList <count($holidayList) ; $ligHolidayList++) {
            $id = $holidayList[$ligHolidayList]['id'];
            $holiday = $repo->findOneBy(['id' => $id ]);
            for ($ligHoliday=0; $ligHoliday <count($holidayList) ; $ligHoliday++) { 
                // si l'ID est différente
                if ($holidayList[$ligHolidayList]['id'] != $holidayList[$ligHoliday]['id']) {
                    // si le service est le même
                    if ($holidayList[$ligHolidayList]['service_id'] == $holidayList[$ligHoliday]['service_id']) {
                        // Si l'intervale de date se chevauche
                        if (($holidayList[$ligHolidayList]['start'] >= $holidayList[$ligHoliday]['start'] && $holidayList[$ligHolidayList]['start'] <= $holidayList[$ligHoliday]['end']) || ($holidayList[$ligHolidayList]['end'] >= $holidayList[$ligHoliday]['start'] && $holidayList[$ligHolidayList]['end'] <= $holidayList[$ligHoliday]['end'])    ) {
                            $stat++;
                        }
                        
                        if ($stat == 0) {
                            $statut = $statuts->findOneBy(['id' => 2]); // chevauchement
                            $holiday->setHolidayStatus($statut);
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($repo);
                            $em->flush();
                        }elseif ($stat >= 1) {
                            $statut = $statuts->findOneBy(['id' => 1]); // chevauchement
                            $holiday->setHolidayStatus($statut);
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($repo);
                            $em->flush();
                        }
                   }
                }
                
            }
        }*/
        
        
        $data = $repo->findBy([], ['id' => 'DESC']);
        
        /*for ($ligData=0; $ligData <count($data) ; $ligData++) { 
            $data[$ligData]['ago'] = $ideaBoxController->time_elapsed_string($data[$ligData]['createdAt']);
        }*/

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('holiday/index.html.twig', [
            'data' => $data
        ]);
    }

     /**
     * @Route("/holiday/new", name="app_holiday_new", methods={"GET","POST"})
     */

     // Dépôt d'un nouveau congés
    public function newHoliday(Request $request, statusHolidayRepository $statuts, MailerInterface $mailerInterface, UsersRepository $repoUser, HolidayRepository $repoHoliday)
    {
        $holiday = new Holiday;
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if($form->isSubmitted() && $form->isValid() ){
            // vérifier si cette utilisateur n'a pas déjà déposé durant cette période
            $result = $repoHoliday->getAlreadyInHolidayInThisPeriod($holiday->getStart(), $holiday->getEnd(), $this->getUser()->getId());
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
            //dd($overlaps);
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

            $holiday = $repoHoliday->getLastHoliday();
            $holiday = $repoHoliday->findOneBy(['id' => $holiday['id']]);
            
            $decideur = $repoHoliday->getMailDecideurConges();
            $mailDecideur = '';
            for ($ligdecideur=0; $ligdecideur <count($decideur) ; $ligdecideur++) { 
                    $mailDecideur = $decideur[$ligdecideur]['email'];
            // envoie d'un email au décideur
            $email = (new Email())
            ->from('intranet@groupe-axis.fr')
            ->to($mailDecideur)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Une demande de congés a été déposé')
            ->html($this->renderView('mails/requestHoliday.html.twig', ['holiday' => $holiday, 'overlaps' => $overlaps,'countService' => $countService ]));

            $mailerInterface->send($email);
            }

            $this->addFlash('message', 'Demande de congés déposé avec succès');
            return $this->redirectToRoute('app_holiday_list');
        }    

        return $this->render('holiday/new.html.twig',[
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
        //$holiday[0]['ago'] = $ideaBoxController->time_elapsed_string($holiday[0]['createdAt']);
        // Si on est pas le dépositaire ou le décideur pas accés
        $userHoliday = $repo->getUserIdHoliday($id);
        if ($userHoliday['users_id'] != $this->getUser()->getId() and !$this->isGranted('ROLE_CONGES') ){
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            return $this->redirectToRoute('app_holiday_list');  
        }
                   
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        // On ajoute les fériers au calendrier des congés
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);
        $ferierDurantConges = array();
        foreach ($jsonIterator as $key => $val) {
            //dd($holiday->getStart()->format('Y-m-d'));
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
            // compter le nombre de jour de congés déposé hors weekend
            if (date("N",$i) != 6 AND date("N",$i) != 7 ) {
                //compter le nombre d'heure
                // si le nombre d'heure entre 08h00 et 18h00 est supérieurs à 4 heures et que les jours de début et de fin ne sont pas identiques
                $hf = new DateTime($holiday->getStart()->format('Y-m-d') . '18:00:00');
                $hd = $holiday->getStart();
                $diff = $hf->diff($hd);
                if ($diff->h > 8) {
                    $nbConges++;
                }elseif ($diff->h <= 4 ) {
                    $nbConges = $nbConges + 0.5 ;
                }
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

    public function nbJoursConges()
    {

    }

    /**
     * @Route("/holiday/edit/{id}", name="app_holiday_edit", methods={"GET","POST"})
     */
    // modifier un congés
    public function editHoliday($id, Request $request, HolidayRepository $repo, MailerInterface $mailerInterface, UsersRepository $repoUser, statusHolidayRepository $statuts)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $repo->findOneBy(['id' => $id]);

        // Si on est pas le dépositaire pas accés
        $userHoliday = $repo->getUserIdHoliday($id);
        if ($userHoliday['users_id'] != $this->getUser()->getId() ){
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            return $this->redirectToRoute('app_holiday_list');  
        }

        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);
        if ($holiday->getHolidayStatus()->getId() == 3 or $holiday->getHolidayStatus()->getId() == 4) {
            $this->addFlash('danger', 'Vous ne pouvez plus modifier ce congés celui ci a été traité');
            return $this->redirectToRoute('app_holiday_list');
        }
            
            if($form->isSubmitted() && $form->isValid() ){
                // Liste de congés dans le même interval de date d'un service
            $overlaps = $repo->getOverlapHoliday($holiday->getStart(), $holiday->getEnd(),$this->getUser()->getService()->getId());

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
                        ->setHolidayStatus($statut);
                $em = $this->getDoctrine()->getManager();
                $em->persist($holiday);
                $em->flush();

                $holiday = $repo->findOneBy(['id' => $id]);

                $decideur = $repo->getMailDecideurConges();
                $mailDecideur = '';
                for ($ligdecideur=0; $ligdecideur <count($decideur) ; $ligdecideur++) { 
                        $mailDecideur = $decideur[$ligdecideur]['email'];
                        // envoie d'un email au décideur
                        $email = (new Email())
                        ->from('intranet@groupe-axis.fr')
                        ->to($mailDecideur)
                        ->priority(Email::PRIORITY_HIGH)
                        ->subject('Une modification de congés a été effectué')
                        ->html($this->renderView('mails/requestHoliday.html.twig', ['holiday' => $holiday, 'overlaps' => $overlaps,'countService' => $countService ]));
        
                        $mailerInterface->send($email);
                }

                $this->addFlash('message', 'Le congés a été modifié avec succès');
                return $this->redirectToRoute('app_holiday_list');
            }

        return $this->render('holiday/edit.html.twig',[
            'form' => $form->createView(),
            'holiday' => $holiday
        ]);

    }

    /**
     * @Route("/holiday/delete/{id}", name="app_holiday_delete")
     */
    // supprimer un congés
    public function deleteHoliday($id, Request $request, Holiday $holiday, HolidayRepository $repo)
    {
        
        // Si on est pas le dépositaire pas accés
        $userHoliday = $repo->getUserIdHoliday($id);
        if ($userHoliday['users_id'] != $this->getUser()->getId() ){
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            return $this->redirectToRoute('app_holiday_list');  
        }
        if ($holiday->getHolidayStatus()->getId() == 3 or $holiday->getHolidayStatus()->getId() == 4) {
            $this->addFlash('danger', 'Vous ne pouvez plus supprimer ce congés celui ci a été traité');
            return $this->redirectToRoute('app_holiday_list');
        }
            if ($this->isCsrfTokenValid('delete'.$holiday->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($holiday);
                $entityManager->flush();
            }
        $this->addFlash('danger', 'Vous ne pouvez plus supprimer ce congés celui ci a été traité');
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
        
        // envoie d'un email à l'utilisateur
        $mailingUser = $repoUser->getFindEmail($id);
        $email = (new Email())
        ->from('intranet@groupe-axis.fr')
        ->to($mailingUser['email'])
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Votre demande de congés a été acceptée !')
        ->html($this->renderView('mails/AcceptHoliday.html.twig', ['holiday' => $holiday]));

        $mailerInterface->send($email);

        $this->addFlash('message', 'Le congés a été Accepté');
        return $this->redirectToRoute('app_holiday_list');
    }


    /**
     * @Route("/holiday/refuse/{id}", name="app_holiday_refuse", methods={"GET"})
     * @IsGranted("ROLE_CONGES")
     */
    // refuser un congés
    public function refuseHoliday($id, Request $request, HolidayRepository $repo, statusHolidayRepository $statuts, MailerInterface $mailerInterface, UsersRepository $repoUser)
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

         // envoie d'un email à l'utilisateur
         $mailingUser = $repoUser->getFindEmail($id);
         $email = (new Email())
         ->from('intranet@groupe-axis.fr')
         ->to($mailingUser['email'])
         ->priority(Email::PRIORITY_HIGH)
         ->subject('Votre demande de congés n\'a pas été acceptée')
         ->html($this->renderView('mails/RefuseHoliday.html.twig', ['holiday' => $holiday]));
 
         $mailerInterface->send($email);

        $this->addFlash('danger', 'Le congés a été Refusé');
        return $this->redirectToRoute('app_holiday_list');
    }

}
