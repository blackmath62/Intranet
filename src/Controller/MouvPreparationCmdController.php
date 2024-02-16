<?php

namespace App\Controller;

use App\Repository\Divalto\EntRepository;
use App\Service\BlogFormaterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MouvPreparationCmdController extends AbstractController
{
    #[Route('/mouv/preparation/cmd', name: 'app_mouv_preparation_cmd')]
    public function index(Request $request, EntRepository $repo, BlogFormaterService $blobFormater): Response
    {
        $dos = 1;
        // Récupération des paramètres de tri et de recherche depuis la requête
        $tri = $request->query->get('tri');
        $q = $request->query->get('q');

        // Récupération de la liste des commandes
        $listCmds = $repo->getListMouvPreparationCmd();

        // Logique de tri
        if ($tri === 'dateCmd_asc') {
            usort($listCmds, function ($a, $b) {
                return strtotime($a['dateCmd']) - strtotime($b['dateCmd']);
            });
        } elseif ($tri === 'dateCmd_desc') {
            usort($listCmds, function ($a, $b) {
                return strtotime($b['dateCmd']) - strtotime($a['dateCmd']);
            });
        }

        // Logique de recherche
        if ($q) {
            $listCmds = array_filter($listCmds, function ($cmd) use ($q) {
                // Votre logique de recherche ici
                // Retourne true si le terme de recherche est trouvé dans la commande
            });
        }
        // Formater les données des champs nDb et nFb
        foreach ($listCmds as &$cmd) {
            $cmd['nDb'] = $blobFormater->getFormate($cmd['nDb']);
            $cmd['nFb'] = $blobFormater->getFormate($cmd['nFb']);
        }

        return $this->render('mouv_preparation_cmd/index.html.twig', [
            'listCmds' => $listCmds,
            'title' => 'Liste commandes à préparer',
        ]);
    }
}
