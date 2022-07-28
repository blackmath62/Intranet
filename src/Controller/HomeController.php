<?php

namespace App\Controller;

use DateTime;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use App\Repository\Main\ControlesAnomaliesRepository;
use App\Repository\Main\NewsRepository;
use App\Repository\Main\UsersRepository;
use App\Repository\Main\HolidayRepository;
use App\Repository\Main\TrackingsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class HomeController extends AbstractController
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
    public function index(Request $request, UsersRepository $repoUser, TrackingsRepository $repoTracking, HolidayRepository $holidayRepo, UsersRepository $userRepo, NewsRepository $repoNews)
    {
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $users = $repoUser->findBy(['closedAt' => NULL]);
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
             $start = $event->getStart()->format('Y-m-d H:i:s');
             $end = $event->getEnd()->format('Y-m-d H:i:s');
             if ($event->getStart()->format('Y-m-d') == $event->getEnd()->format('Y-m-d') && $event->getStart()->format('H:i') == '00:00' && $event->getEnd()->format('H:i') == '23:00') {
                $start = $event->getStart()->format('Y-m-d');
                $end = $event->getEnd()->format('Y-m-d');
             }

             $rdvs[] = [
                 'id' => $event->getId(),
                 'start' => $start,
                 'end' => $end,
                 'title' => 'Congés ' . $pseudo,
                 'backgroundColor' => $color,
                 'borderColor' => '#FFFFFF',
                 'textColor' => $textColor,
                ];
            }
            
        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        // On ajoute les fériers au calendrier des congés
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);
        foreach ($jsonIterator as $key => $val) {
            $rdvs[] = [
                'id' => '',
                'start' => $key,
                'end' => $key,
                'title' => $val,
                'backgroundColor' => '#404040',
                'borderColor' => '#FFFFFF',
                'textColor' => '#FFFFFF',
            ];
        }
        // Les anniversaires des utilisateurs
        
        foreach ($users as $key => $value) {
            $annif = $value->getBornAt()->format('m-d');
            $annee = date("Y") - 1;
            $annee2 = date("Y") + 3;
            for ($ligAnnee=$annee; $ligAnnee <$annee2 ; $ligAnnee++) { 
                $anniversaire = $ligAnnee . '-' . $annif;
                
                $rdvs[] = [
                    'id' => '',
                    'start' => $anniversaire,
                    'end' => $anniversaire,
                    'title' => 'Anniversaire ' . $value->getPseudo(),
                    'backgroundColor' => '#FF9BFF',
                    'borderColor' => '#D7D7D7',
                    'textColor' => '#FFFFFF',
                ];
            }
        }
        
         $data = json_encode($rdvs);

        $news = $repoNews->getNews();

        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'users' => $users,
            'tracks' => $track,
            'data' => $data,
            'news' => $news
        ]);
    }
    // déterminer depuis combien de temps un utilisateur est connecté
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
