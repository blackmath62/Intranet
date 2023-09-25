<?php

namespace App\Controller;

use App\Entity\Main\HolidayTypes;
use App\Form\EditHolidayTypesType;
use App\Repository\Main\HolidayTypesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted("ROLE_ADMIN")]

class AdminTypeHolidayController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route("/admin/type/holiday", name: "app_admin_types_holiday")]

    public function index(Request $request, HolidayTypesRepository $repo, EntityManagerInterface $manager): Response
    {
        $holidayType = new HolidayTypes();

        $form = $this->createFormBuilder($holidayType)
            ->add("name", TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un pseudo',
                    ]),
                ],
                'required' => true,
                'label' => 'Nom du type de congés',
            ])
            ->add('color', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir une couleur',
                    ]),
                ],
                'required' => true,
                'attr' => [
                    'class' => 'col-3 form-control my-colorpicker2',
                ],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $holidayType->setCreatedAt(new DateTime());
            $manager->persist($holidayType);
            $manager->flush();
        }
        $holidayTypes = $repo->findAll();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->render('admin_type_holiday/index.html.twig', [
            'holidayTypes' => $holidayTypes,
            'formholidayTypes' => $form->createView(),
            'title' => "Administration des Types de Congés",
        ]);
    }

    #[Route("/admin/type/holiday/delete/{id}", name: "app_delete_types_holiday")]

    public function deleteHolidayType($id, Request $request)
    {
        $repository = $this->entityManager->getRepository(HolidayTypes::class);
        $holidayId = $repository->find($id);

        $em = $this->entityManager;
        $em->remove($holidayId);
        $em->flush();

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        return $this->redirect($this->generateUrl('app_admin_types_holiday'));
    }

    #[Route("/admin/type/holiday/edit/{id}", name: "app_edit_types_holiday")]

    public function editSociete(HolidayTypes $holidayTypes, Request $request)
    {
        $form = $this->createForm(EditHolidayTypesType::class, $holidayTypes);
        $form->handleRequest($request);

        // tracking user page for stats
        //$tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;
            $em->persist($holidayTypes);
            $em->flush();

            $this->addFlash('message', 'Type de congés modifié avec succès');
            return $this->redirectToRoute('app_admin_types_holiday');

        }
        return $this->render('admin_type_holiday/edit_type_holiday.html.twig', [
            'holidayTypeEditForm' => $form->createView(),
            'holidayTypes' => $holidayTypes,
            'title' => 'Edition des Types de congés',
        ]);
    }
}
