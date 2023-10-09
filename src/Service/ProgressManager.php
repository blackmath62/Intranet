<?php
namespace App\Service;

class ProgressManager
{
    private $totalIterations;
    private $currentIteration;

    public function __construct()
    {
        // Par défaut, le nombre total d'itérations est 0 et l'itération actuelle est 0.
        $this->totalIterations = 0;
        $this->currentIteration = 0;
    }

    public function initializeProgress($totalIterations)
    {
        $this->totalIterations = $totalIterations;
        $this->currentIteration = 0;
    }

    public function updateProgress()
    {
        // Mettre à jour la barre de progression en fonction de l'itération actuelle.
        $percentage = ($this->currentIteration / $this->totalIterations) * 100;
        return number_format($percentage, 2);
        // Code pour afficher ou envoyer ce pourcentage à l'utilisateur, par exemple, en utilisant une réponse JSON.
        // Vous pouvez également stocker ce pourcentage dans un fichier si nécessaire.
    }

    public function incrementIteration()
    {
        $this->currentIteration++;
    }

    public function finalizeProgress()
    {
        // Finaliser la barre de progression (mettre à 100 %)
        $this->currentIteration = $this->totalIterations;
        $this->updateProgress();
    }
}
