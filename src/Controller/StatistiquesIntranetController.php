<?php

namespace App\Controller;

use App\Repository\Main\TrackingsRepository;
use App\Repository\Main\UsersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]

class StatistiquesIntranetController extends AbstractController
{
    #[Route("/admin/statistiques/intranet", name: "app_admin_statistiques")]
    #[Route("/admin/statistiques/intranet/{id}", name: "app_admin_statistiques_user")]

    public function index(TrackingsRepository $repo, UsersRepository $repoUsers, $id = null, $usager = null): Response
    {
        if ($id) {
            $data = $repo->getUserStatesIntranet($id);
            $usager = $repoUsers->findOneBy(['id' => $id])->getPseudo();
        } else {
            $data = $repo->getStatesIntranet();
        }

        for ($ligPage = 0; $ligPage < count($data); $ligPage++) {
            $page[] = $data[$ligPage]['Page'];
            $count[] = $data[$ligPage]['CountPage'];
            $color[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ', ' . rand(0, 255) . ', 1)';
        }
        $countData = count($data);
        $users = $repoUsers->findAll();

        return $this->render('statistiques_intranet/index.html.twig', [
            'titre' => 'States Intranet',
            'datas' => $data,
            'page' => json_encode($page),
            'color' => json_encode($color),
            'count' => json_encode($count),
            'users' => $users,
            'usager' => $usager,
            'countData' => $countData,
        ]);
    }

}
