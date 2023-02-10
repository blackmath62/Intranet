<?php

namespace App\Controller;

use App\Entity\Main\Services;
use App\Form\EditServiceType;
use App\Repository\Main\ServicesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminServicesController extends AbstractController
{
    /**
     * @Route("/admin/services", name="app_admin_services")
     */

    public function index(Services $service = null, Request $request, ServicesRepository $repo, EntityManagerInterface $manager)
    {
        if (!$service) {
            $service = new Services();
        }
        $form = $this->createFormBuilder($service)
            ->add("title")
            ->add("color")
            ->add("textColor")
            ->add('fa')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setCreatedAt(new DateTime());
            $manager->persist($service);
            $manager->flush();
        }
        $services = $repo->findAll();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('admin_services/index.html.twig', [
            'controller_name' => 'AdminserviceController',
            'services' => $services,
            'formService' => $form->createView(),
            'title' => "Administration des services",
        ]);
    }

    /**
     * @Route("/admin/service/delete/{id}",name="app_delete_service")
     */
    public function deleteservice($id, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Services::class);
        $serviceId = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($serviceId);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->redirect($this->generateUrl('app_admin_services'));
    }
    /**
     * @Route("/admin/service/edit/{id}",name="app_edit_service")
     */
    public function editservice(Services $service, Request $request)
    {
        $form = $this->createForm(EditServiceType::class, $service);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_admin_services');

        }
        return $this->render('admin_services/edit_services.html.twig', [
            'serviceEditForm' => $form->createView(),
            'services' => $service,
            'title' => 'Edition des services',
        ]);
    }
}
