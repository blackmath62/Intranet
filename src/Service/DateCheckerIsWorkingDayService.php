<?php
namespace App\Service;

class DateCheckerIsWorkingDayService
{
    private $holidays;

    public function __construct()
    {
        // Récupérer les jours fériés en JSON depuis le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        // Convertir le JSON en tableau associatif PHP
        $this->holidays = json_decode($ferierJson, true);
    }

    public function isHoliday($date)
    {
        $formattedDate = $date->format('Y-m-d'); // Adapter le format selon celui utilisé dans $this->holidays
        return isset($this->holidays[$formattedDate]);
    }

    public function isWeekend($date)
    {
        $dayOfWeek = date('N', strtotime($date->format('Y-m-d'))); // Renvoie 1 pour lundi, 2 pour mardi, ..., 7 pour dimanche
        return $dayOfWeek >= 6; // Samedi ou dimanche
    }

    public function isWorkingDay($date)
    {
        return !$this->isHoliday($date) && !$this->isWeekend($date);
    }
}
