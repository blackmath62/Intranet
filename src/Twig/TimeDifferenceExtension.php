<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeDifferenceExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('calculateTimeDifference', [$this, 'calculateTimeDifference']),
        ];
    }

    public function calculateTimeDifference(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        $interval = $start->diff($end);

        return $interval->h + ($interval->i / 60); // Retourne la différence en heures (y compris les minutes converties)
    }
}
