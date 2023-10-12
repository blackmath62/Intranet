<?php
namespace App\Service;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Twig extends AbstractExtension
{
    /*private $geocoder;

    public function __construct(StatefulGeocoder $geocoder)
    {
    $this->geocoder = $geocoder;
    }*/

    public function getFilters()
    {
        return [
            new TwigFilter('calculateTimeDifference', [$this, 'calculateTimeDifference']),
            new TwigFilter('time_diff', [$this, 'calcTimeDiff']),
            /*new TwigFilter('calculate_distance', [$this, 'calculateDistance']),*/
        ];
    }

    /*public function calculateDistance($address1, $address2)
    {
    try {
    $location1 = $this->geocoder->geocode($address1)->first();
    $location2 = $this->geocoder->geocode($address2)->first();

    return $location1->getCoordinates()->distanceTo($location2->getCoordinates());
    } catch (\Exception $e) {
    return null; // Vous pouvez personnaliser le comportement en cas d'erreur
    }
    }*/

    public function getFunctions() // Ajoutez cette fonction
    {
        return [
            new TwigFunction('now', [$this, 'getCurrentDateTime']),
        ];
    }

    public function calculateTimeDifference(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        $interval = $start->diff($end);

        return $interval->h + ($interval->i / 60); // Retourne la différence en heures (y compris les minutes converties)
    }

    public function calcTimeDiff($createdAt, $updatedAt = null)
    {
        if ($updatedAt === null) {
            $updatedAt = new DateTime();
        }

        // Calculez la différence entre les deux dates ici
        $interval = $updatedAt->diff($createdAt);

        // Vous pouvez personnaliser le format de sortie ici
        // Par exemple, vous pouvez renvoyer le nombre de jours :
        return $interval->format('%a jours');
    }
}
