<?php

namespace App\Controller;

use App\Repository\Divalto\EntRepository;
use App\Repository\Main\MouvPreparationCmdAdminRepository;
use App\Repository\Main\UsersRepository;
use App\Service\BlogFormaterService;
use App\Service\ProductFormService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class MouvPreparationCmdController extends AbstractController
{
    private $repoEnt;
    private $productFormService;
    private $blobFormater;
    private $entityManager;
    private $repoPreparationAdmin;
    private $repoUsers;

    public function __construct(
        ProductFormService $productFormService,
        EntRepository $repoEnt,
        ManagerRegistry $registry,
        UsersRepository $repoUsers,
        BlogFormaterService $blobFormater,
        MouvPreparationCmdAdminRepository $repoPreparationAdmin, ) {
        $this->repoEnt = $repoEnt;
        $this->blobFormater = $blobFormater;
        $this->repoUsers = $repoUsers;
        $this->entityManager = $registry->getManager();
        $this->productFormService = $productFormService;
        $this->repoPreparationAdmin = $repoPreparationAdmin;
    }

    #[Route('/mouv/preparation/cmd', name: 'app_mouv_preparation_cmd')]
    public function index(Request $request): Response
    {
        $dos = 1;
        // Récupération des paramètres de tri et de recherche depuis la requête
        $tri = $request->query->get('tri');
        $q = $request->query->get('q');

        $cmds = $this->repoPreparationAdmin->findBy(['preparedBy' => $this->getUser(), 'preparedAt' => null]);
        $i = 0;
        $filtreCmds = "";
        foreach ($cmds as $value) {
            if ($i == 0) {
                $filtreCmds = "'" . $value->getcdNo() . "'";
            } else {
                $filtreCmds = $filtreCmds . ",'" . $value->getcdNo() . "'";
            }
            $i++;
        }

        // Récupération de la liste des commandes
        $listCmds = $this->repoEnt->getListMouvPreparationCmd($filtreCmds);

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
            $cmd['nDb'] = $this->blobFormater->getFormate($cmd['nDb']);
            $cmd['nFb'] = $this->blobFormater->getFormate($cmd['nFb']);
        }

        return $this->render('mouv_preparation_cmd/index.html.twig', [
            'listCmds' => $listCmds,
            'title' => 'Liste commandes à préparer',
        ]);
    }
}
