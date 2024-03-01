<?php

namespace App\Controller;

use App\Entity\Main\MouvPreparationCmdAdmin;
use App\Repository\Divalto\EntRepository;
use App\Repository\Main\MouvPreparationCmdAdminRepository;
use App\Repository\Main\UsersRepository;
use App\Service\BlogFormaterService;
use App\Service\ProductFormService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class MouvPreparationCmdAdministrationController extends AbstractController
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

    #[Route('/mouv/preparation/cmd/administration', name: 'app_mouv_preparation_cmd_administration')]
    public function index(Request $request, UsersRepository $repoUsers, MouvPreparationCmdAdminRepository $repoPreparationAdmin, EntRepository $repo, BlogFormaterService $blobFormater): Response
    {
        $dos = 1;
        // Récupération des paramètres de tri et de recherche depuis la requête
        $tri = $request->query->get('tri');
        $q = $request->query->get('q');

        // Récupération de la liste des commandes
        $listCmds = $repo->getListMouvPreparationCmd(null);

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
            $prep = $repoPreparationAdmin->findOneBy(['cdNo' => $cmd['cmd']]);
            if (!$prep or $prep->getPreparedAt() == null) {
                $cmd['nDb'] = $blobFormater->getFormate($cmd['nDb']);
                $cmd['nFb'] = $blobFormater->getFormate($cmd['nFb']);
                if ($prep && $prep->getPreparedBy() != null) {
                    $cmd['preparateur']['id'] = $prep->getPreparedBy()->getId();
                    $cmd['preparateur']['preparedAt'] = $prep->getPreparedAt();
                } else {
                    $cmd['preparateur']['id'] = 9999;
                    $cmd['preparateur']['preparedAt'] = null;
                }
                $newListCmds[] = $cmd; // Ajouter l'élément au nouveau tableau
            }
        }
        $listCmds = $newListCmds;
        //dd($listCmds);

        $preparateurs = $repoUsers->findUsersByRole('ROLE_PREPARATEUR');

        // Ajouter le préparateur spécifique à la liste des préparateurs
        $preparateurs[] = [
            'id' => 9999, // L'ID spécifique que vous souhaitez utiliser
            'pseudo' => "Commandes à assigner", // Ajoutez d'autres données si nécessaire
        ];

        return $this->render('mouv_preparation_cmd_administration/index.html.twig', [
            'listCmds' => $listCmds,
            'preparateurs' => $preparateurs,
            'title' => 'Liste commandes à préparer',
        ]);
    }

    #[Route('/mouv/preparation/cmd/detail/{cmd}', name: 'app_mouv_preparation_detail')]
    public function detail($cmd)
    {
        $produits = $this->repoEnt->getMouvPreparationCmdList($cmd);
        // Formater les données des champs nDb et nFb
        foreach ($produits as &$produit) {
            $produit['note'] = $this->blobFormater->getFormate($produit['note']);
        }

        return new JsonResponse($produits);
    }

    #[Route('/mouv/preparation/cmd/move/{cmd}/{preparateur}', name: 'app_mouv_preparation_move')]
    public function move($cmd, $preparateur)
    {
        $commande = $this->repoPreparationAdmin->findOneBy(['cdNo' => $cmd]);
        if ($preparateur != 9999) {
            $preparateur = $this->repoUsers->findOneBy(['id' => $preparateur]);
        } else {
            $preparateur = null;
        }

        if (!$commande) {
            $commande = new MouvPreparationCmdAdmin;
        }
        $commande->setAssignedBy($this->getUser())
            ->setAssignedAt(new DateTime())
            ->setPreparedBy($preparateur)
            ->setPreparedAt(null)
            ->setCdNo($cmd);

        $em = $this->entityManager;
        $em->persist($commande);
        $em->flush();

        // Créer une réponse JSON pour indiquer que l'opération s'est déroulée avec succès
        $data = ['success' => true, 'message' => 'La commande a été déplacée avec succès.'];
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/mouv/preparation/cmd/count/cmd/{preparateur}', name: 'app_mouv_preparation_count_cmd')]
    public function get_count_cmd($preparateur)
    {
        $preparateur = $this->repoUsers->findOneBy(['id' => $preparateur]);
        $cmdsPreparateur = $this->repoPreparationAdmin->findBy(['preparedBy' => $preparateur, 'preparedAt' => null]);

        $countCmds = count($cmdsPreparateur);

        return new JsonResponse($countCmds);
    }
}
