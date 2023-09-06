<?php

namespace App\Controller;

use App\Form\ProfileUserType;
use App\Form\StatesDateFilter2Type;
use App\Form\StatesDateFilterType;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\InterventionFicheMonteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @IsGranted("ROLE_USER")
 */

class ProfileUserController extends AbstractController
{
    /**
     * @Route("/profile/user", name="app_profile_user")
     */
    public function index(Request $request, SluggerInterface $slugger, HolidayRepository $repoHoliday, InterventionFicheMonteurRepository $repoInterventions)
    {
        // tracking user page for stats
        // $tracking = $request->attributes->get('_route');
        // $this->setTracking($tracking);
        $interventions = '';
        $user = $this->getUser();
        $form = $this->createForm(ProfileUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupére le fichier dans le formulaire
            $userImg = $form->getData();
            $file = $form->get('img')->getData();
            //dd($file);

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('doc_profiles'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'Filename' property to store the PDF file name
                // instead of its contents
                $userImg->setImg($newFilename);
            } else {
                $userImg->setImg('AdminLTELogo.png');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_profile_user');

        }
        $acceptes = "";
        $refuses = "";
        $attentes = "";
        $detailsAttentes = "";
        $detailsRefuses = "";
        $detailsAcceptes = "";

        $formDates = $this->createForm(StatesDateFilterType::class);
        $formDates->handleRequest($request);
        if ($formDates->isSubmitted() && $formDates->isValid()) {
            $start = $formDates->getData()['startDate']->format('Y-m-d');
            $end = $formDates->getData()['endDate']->format('Y-m-d');
            $acceptes = $repoHoliday->getCountCongesAccepted($this->getUser()->getid(), $start, $end);
            $refuses = $repoHoliday->getCountCongesRefused($this->getUser()->getid(), $start, $end);
            $attentes = $repoHoliday->getCountCongesWait($this->getUser()->getid(), $start, $end);
            $detailsAttentes = $repoHoliday->getListCongesWait($this->getUser()->getid(), $start, $end);
            $detailsRefuses = $repoHoliday->getListCongesRefused($this->getUser()->getid(), $start, $end);
            $detailsAcceptes = $repoHoliday->getListCongesAccepted($this->getUser()->getid(), $start, $end);
        }
        $formFiches = $this->createForm(StatesDateFilter2Type::class);
        $formFiches->handleRequest($request);
        if ($formFiches->isSubmitted() && $formFiches->isValid()) {
            $start = $formFiches->getData()['startDate']->format('Y-m-d');
            $end = $formFiches->getData()['endDate']->format('Y-m-d');
            $interventions = $repoInterventions->findFichePeriode($this->getUser()->getId(), $start, $end);
        }
        return $this->render('profile_user/index.html.twig', [
            'controller_name' => 'ProfileUserController',
            'title' => 'gestion de compte',
            'profileUserForm' => $form->createView(),
            'user' => $user,
            'acceptes' => $acceptes,
            'refuses' => $refuses,
            'attentes' => $attentes,
            'detailsAttentes' => $detailsAttentes,
            'detailsRefuses' => $detailsRefuses,
            'detailsAcceptes' => $detailsAcceptes,
            'formFiches' => $formFiches->createView(),
            'formDates' => $formDates->createView(),
            'interventions' => $interventions,
        ]);
    }

}
