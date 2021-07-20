<?php

namespace App\Controller\Main;

use App\Entity\Main\Holiday;
use App\Form\Main\HolidayType;
use App\Repository\Main\HolidayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/main/holiday")
 */
class HolidayController extends AbstractController
{
    /**
     * @Route("/", name="main_holiday_index", methods={"GET"})
     */
    public function index(HolidayRepository $holidayRepository): Response
    {
        return $this->render('main/holiday/index.html.twig', [
            'holidays' => $holidayRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="main_holiday_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $holiday = new Holiday();
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();

            return $this->redirectToRoute('main_holiday_index');
        }

        return $this->render('main/holiday/new.html.twig', [
            'holiday' => $holiday,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="main_holiday_show", methods={"GET"})
     */
    public function show(Holiday $holiday): Response
    {
        return $this->render('main/holiday/show.html.twig', [
            'holiday' => $holiday,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="main_holiday_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Holiday $holiday): Response
    {
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('main_holiday_index');
        }

        return $this->render('main/holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="main_holiday_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Holiday $holiday): Response
    {
        if ($this->isCsrfTokenValid('delete'.$holiday->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($holiday);
            $entityManager->flush();
        }

        return $this->redirectToRoute('main_holiday_index');
    }
}
