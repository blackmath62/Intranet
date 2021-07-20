<?php

namespace App\Controller;

use App\Repository\Main\HolidayTypesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractController
{
    
    
    /**
     * @Route("/holiday/ancien", name="app_holiday_old")
     */
    public function index(Request $request, HolidayTypesRepository $repoType): Response
    {
        
        $holidayTypes = $repoType->findAll();
        
        return $this->render('holiday/index.html.twig', [
            'controller_name' => 'HolidayController',
            'title' => 'Vacances',
            'holidayTypes' => $holidayTypes
        ]);
    }
}
