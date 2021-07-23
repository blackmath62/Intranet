<?php

namespace App\Controller;

use DateTime;
use App\Form\HolidayType;
use App\Entity\Main\Holiday;
use Symfony\Component\Mime\Email;
use App\Entity\Main\statusHoliday;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\CalendarRepository;
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
    /**
     * @Route("/holiday", name="app_holiday_list")
     */
    public function index(HolidayRepository $repo, Request $request, statusHolidayRepository $statuts)
    {
        // liste des congés avec les services
        $holidayList = $repo->getListeCongesEtServices();
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
        
        
        $data = $repo->findAll();

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
    public function newHoliday(Request $request, statusHolidayRepository $statuts, MailerInterface $mailerInterface, UsersRepository $repoUser, HolidayRepository $repoHoliday)
    {
        $holiday = new Holiday;
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if($form->isSubmitted() && $form->isValid() ){
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
            // dd($overlaps);
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
    public function showHoliday($id, Request $request, HolidayRepository $repo)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $repo->findOneBy(['id' => $id]);
        // Si on est pas le dépositaire ou le décideur pas accés
        $userHoliday = $repo->getUserIdHoliday($id);
        if ($userHoliday['users_id'] != $this->getUser()->getId() and !$this->isGranted('ROLE_CONGES') ){
            $this->addFlash('danger', 'Vous n\'avez pas accés à ces données');
            return $this->redirectToRoute('app_holiday_list');  
        }
        return $this->render('holiday/show.html.twig',[
            'holiday' => $holiday
        ]);
    }

    /**
     * @Route("/holiday/edit/{id}", name="app_holiday_edit", methods={"GET","POST"})
     */
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
