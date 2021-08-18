<?php

namespace App\Controller;

use DateTime;
use App\Form\IdeaBoxType;
use App\Entity\Main\IdeaBox;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\IdeaBoxRepository;
use App\Repository\Main\TrackingsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class IdeaBoxController extends AbstractController
{
    /**
     * @Route("/php_info", name="app_php_info")
     */
    public function getPhpInfo()
    {
        return new Response('<html><body>'.phpinfo().'</body></html>');
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, IdeaBoxRepository $repo, UsersRepository $repoUser, TrackingsRepository $repoTracking, HolidayRepository $holidayRepo, UsersRepository $userRepo)
    {

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $users = $repoUser->findAll();
        $track = $repoTracking->getLastConnect();
        // balayer chaque utilisateur
        
        for ($ligTrack=0; $ligTrack <count($track) ; $ligTrack++) { 
            // vérifier s'il est actuellement en congés
            $inHoliday = $holidayRepo->getUserActuallyHoliday($track[$ligTrack]['user_id']);
            if ($inHoliday) {
                $track[$ligTrack]['inHoliday'] = 'En congés';
            }else {
                $track[$ligTrack]['inHoliday'] = '';
            }
            $track[$ligTrack]['ago'] = $this->time_elapsed_string($track[$ligTrack]['createdAt']);
        }
         // Calendrier des congés
         $events = $holidayRepo->findBy(['holidayStatus' => 3]);
         $rdvs = [];
 
         foreach($events as $event){
            $id = $event->getId();
            $userId = $holidayRepo->getUserIdHoliday($id);
            $user = $userRepo->findOneBy(['id' => $userId]);
            $pseudo = $user->getPseudo();
            $color = $user->getService()->getColor();
            $textColor = $user->getService()->getTextColor();
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => 'Congés ' . $pseudo,
                'backgroundColor' => $color,
                'borderColor' => '#FFFFFF',
                'textColor' => $textColor,
            ];
         }
         $data = json_encode($rdvs);
 


        return $this->render('idea_box/index.html.twig', [
            'title' => 'Accueil',
            'users' => $users,
            'tracks' => $track,
            'data' => $data
        ]);
    }

    /**
     * @Route("/idea/show/{id}", name="app_idea_show")
     */

    public function IdeaShow(int $id, IdeaBoxRepository $repo, Request $request)
    {
        $idea = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('idea_box/idea_show.html.twig', [
            'idea' => $idea,
            'title' => 'Idea View'
        ]);
    }

    /**
     * @Route("/idea/edit/{id}", name="app_idea_edit")
     */

    public function IdeaEdit(int $id, IdeaBoxRepository $repo, Request $request)
    {
        $idea = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $form = $this->createForm(IdeaBoxType::class, $idea);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $idea = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($idea);
            $em->flush();
            
            $this->addFlash('message', 'Idée Modifiée avec succés');
            return $this->redirectToRoute('app_idea_show', ['id' => $id ]);
        }
        return $this->render('idea_box/idea_edit.html.twig', [
            'ideaForm' => $form->createView(),
            'title' => 'Idea Edit'
        ]);
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        
        $string = array(
            'y' => 'année',
            'm' => 'mois',
            'w' => 'semaine',
            'd' => 'jour',
            'h' => 'heure',
            'i' => 'minute',
            's' => 'seconde',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k ) {
                    if ($v == 'mois') {
                        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
                    }else {
                        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                    }
            } else {
                unset($string[$k]);
            }
        }
                
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? ' il y a ' . implode(', ', $string) : 'en ligne';
    }
}
