<?php

namespace App\Controller;

use App\Repository\Divalto\ArtRepository;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class MatiereDangeureuseController extends AbstractController
{
    #[Route("/matiere/dangeureuse", name: "app_matiere_dangeureuse")]

    public function index(ArtRepository $repo): Response
    {
        $articles = $repo->StockBlobMatiereDangeureuse();

        foreach ($articles as $value) {
            $document = '';
            $formatter = new HtmlFormatter();
            try {
                $document = new Document($value['blob']);
            } catch (\Throwable $th) {
            }
            if (!$document) {
                $arts[] = [
                    'ref' => $value['ref'],
                    'sref1' => $value['sref1'],
                    'sref2' => $value['sref2'],
                    'designation' => $value['designation'],
                    'uv' => $value['uv'],
                    'famille' => $value['famille'],
                    'poin' => $value['poin'],
                    'code' => $value['code'],
                    'fournisseur' => $value['fournisseur'],
                    'stock' => $value['stock'],
                    'note' => $value['note'],
                    'blob' => 'Rien à afficher',
                ];
            } else {
                $arts[] = [
                    'ref' => $value['ref'],
                    'sref1' => $value['sref1'],
                    'sref2' => $value['sref2'],
                    'designation' => $value['designation'],
                    'uv' => $value['uv'],
                    'famille' => $value['famille'],
                    'poin' => $value['poin'],
                    'code' => $value['code'],
                    'fournisseur' => $value['fournisseur'],
                    'stock' => $value['stock'],
                    'note' => $value['note'],
                    'blob' => $formatter->Format($document),
                ];
            }
        }

        return $this->render('matiere_dangeureuse/index.html.twig', [
            'articles' => $arts,
            'title' => 'Matiéres Dangeureuses',
        ]);
    }
}
