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

        /*foreach ($articles as $key => $value) {
            dd($value['blob']);
        }*/

        return $this->render('matiere_dangeureuse/index.html.twig', [
            'articles' => $articles,
            'title' => 'Matiéres Dangeureuses'
        ]);
    }

    private function blobToString($data): string
    {
        $details = '';
        while(!feof($data)){
            $details.= fread($data, 1024);
        }
        rewind($data);
        return $details;
    }
}
