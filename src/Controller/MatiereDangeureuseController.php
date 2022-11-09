<?php

namespace App\Controller;

use App\Repository\Divalto\ArtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatiereDangeureuseController extends AbstractController
{
    /**
     * @Route("/matiere/dangeureuse", name="app_matiere_dangeureuse")
     */
    public function index(ArtRepository $repo): Response
    {
        $articles = $repo->StockBlobMatiereDangeureuse();
        //dd(stream_get_contents($articles['blob']));

        return $this->render('matiere_dangeureuse/index.html.twig', [
            'articles' => $articles,
            'title' => 'Matiéres Dangeureuses'
        ]);
    }
}
