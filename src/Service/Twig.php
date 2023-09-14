<?php
namespace App\Service;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Twig extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('calculateTimeDifference', [$this, 'calculateTimeDifference']),
            new TwigFilter('time_diff', [$this, 'calcTimeDiff']),
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
