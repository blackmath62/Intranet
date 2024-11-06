<?php

namespace App\Controller;

use App\Controller\StatsAchatController;
use App\Form\SearchAndFouType;
use App\Repository\Divalto\ArtRepository;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[IsGranted("ROLE_USER")]

class ArticleBlobController extends AbstractController
{
    #[Route("/article/blob", name: "app_article_blob")]

    public function index(ArtRepository $repo, Request $request, StatsAchatController $mef): Response
    {
        $arts = [];
        $form = $this->createForm(SearchAndFouType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $fous = $mef->miseEnForme($form->getData()['fournisseurs']);
            $articles = $repo->ArticlesOuvertBlob($search, $fous);

            foreach ($articles as $value) {
                $document = '';
                $formatter = new HtmlFormatter();
                try {
                    $document = new Document("{" . $value['blob'] . "}");
                } catch (Throwable $th) {
                }

                if ($value['tva'] == 1) {
                    $tva = 20;
                }
                switch ($value['tva']) {
                    case 1:
                        $tva = 20;
                        break;
                    case 2:
                        $tva = 10;
                        break;
                    case 7:
                        $tva = 5.5;
                        break;

                    default:
                        $tva = 'Pas de TVA';
                        break;
                }

                if (!$document) {
                    $arts[] = [
                        'ref' => $value['ref'],
                        'sref1' => $value['sref1'],
                        'sref2' => $value['sref2'],
                        'designation' => $value['designation'],
                        'uv' => $value['uv'],
                        'famille' => $value['famille'],
                        'metier' => $value['metier'],
                        'tva' => $tva,
                        'conf' => $value['conf'],
                        'tiers' => $value['tiers'],
                        'stock' => $value['stock'],
                        'blob' => '',
                    ];
                } else {
                    $arts[] = [
                        'ref' => $value['ref'],
                        'sref1' => $value['sref1'],
                        'sref2' => $value['sref2'],
                        'designation' => $value['designation'],
                        'uv' => $value['uv'],
                        'famille' => $value['famille'],
                        'metier' => $value['metier'],
                        'tva' => $tva,
                        'conf' => $value['conf'],
                        'tiers' => $value['tiers'],
                        'stock' => $value['stock'],
                        'blob' => $formatter->Format($document),
                    ];
                }
            }
        }

        return $this->render('article_blob/index.html.twig', [
            'title' => 'Article ouvert Blob',
            'articles' => $arts,
            'search' => $form->createView(),
        ]);
    }
}
