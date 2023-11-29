<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FichierController extends AbstractController
{
    /**
     * @Route("/fichiers/{chemin}", name="afficher_fichier")
     */
    public function afficherFichier(string $chemin): Response
    {
        // Construire le chemin complet vers le fichier
        $cheminComplet = 'file://///192.168.50.242/' . $chemin;

        // Vérifier si le fichier existe
        if (!file_exists($cheminComplet)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        // Utiliser BinaryFileResponse pour renvoyer le fichier
        return new BinaryFileResponse($cheminComplet);
    }
}
