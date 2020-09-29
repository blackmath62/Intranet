<?php

namespace App\Controller;

use App\Entity\Priorities;
use App\Form\PrioritiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminParameterController extends AbstractController
{
    /**
     * @Route("/admin/parameter", name="admin_parameter")
     */
    public function index(Request $request, Priorities $priority, EntityManagerInterface $manager)
    {
        $priority = new Priorities();
        $form = $this->createForm(PrioritiesType::class,$priority);
        $form->handleRequest($request);
        
        return $this->render('admin_parameter/index.html.twig', [
            'controller_name' => 'AdminParameterController',
            'formPriority' => $form
        ]);
    }
}
