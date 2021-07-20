<?php

namespace App\Controller;

use DateTime;
use App\Form\CalendarType;
use App\Entity\Main\Holiday;
use App\Entity\Main\statusHoliday;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\CalendarRepository;
use App\Repository\Main\statusHolidayRepository;
use App\Repository\Main\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class MainController extends AbstractController
{
    /**
     * @Route("/holiday", name="app_holiday_list")
     */
    public function index(HolidayRepository $calendar, Request $request)
    {
        $data = $calendar->findAll();
        //dd($data);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('holiday/index.html.twig', [
            'data' => $data
        ]);
    }

     /**
     * @Route("/holiday/new", name="app_holiday_new")
     */
    public function depotHoliday(Request $request, statusHolidayRepository $statuts)
    {
        $holiday = new Holiday;
        $form = $this->createForm(CalendarType::class, $holiday);
        $form->handleRequest($request);
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if($form->isSubmitted() && $form->isValid() ){
            $statut = $statuts->findOneBy(['id' => 2]);
            $holiday->setCreatedAt(new DateTime())
            ->setHolidayStatus($statut)
            ->addUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();

            $this->addFlash('message', 'Demande de congés déposé avec succès');
            return $this->redirectToRoute('app_holiday_list');
        }    

        return $this->render('holiday/new.html.twig',[
        'form' => $form->createView()]
        );
    }

    /**
     * @Route("/holiday/show/{id}", name="app_holiday_show")
     */
    public function showHoliday($id, Request $request, HolidayRepository $repo)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $holiday = $repo->findOneBy(['id' => $id]);
        
        return $this->render('holiday/show.html.twig',[
            'holiday' => $holiday
        ]);
    }

    /**
     * @Route("/holiday/edit/{id}", name="app_holiday_edit")
     */
    public function editHoliday($id, Request $request, statusHolidayRepository $statuts)
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('holiday/edit.html.twig');

    }
}
