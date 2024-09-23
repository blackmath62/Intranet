<?php

namespace App\Controller;

use App\Form\DosTiersType;
use App\Repository\Divalto\CliRepository;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class BlobTranslateTiersController extends AbstractController
{
    #[Route('/blob/translate/tiers', name: 'app_blob_translate_tiers')]
    public function index(CliRepository $repo, Request $request): Response
    {

        $tiers = [];
        $form = $this->createForm(DosTiersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dos = $form->getData()['dos'];
            $typeTiers = $form->getData()['typeTiers'];

            $datas = $repo->translateBlobTiers($dos, $typeTiers);

            foreach ($datas as $value) {
                $formatter = new HtmlFormatter();

                try {
                    $blob = new Document($value['Blob']);
                    $blob = $formatter->Format($blob);
                } catch (Throwable $th) {
                    $blob = "";
                }
                try {
                    $blob2 = new Document($value['Blob2']);
                    $blob2 = $formatter->Format($blob2);
                } catch (Throwable $th) {
                    $blob2 = "";
                }
                try {
                    $blob3 = new Document($value['Blob3']);
                    $blob3 = $formatter->Format($blob3);
                } catch (Throwable $th) {
                    $blob3 = "";
                }
                try {
                    $blob4 = new Document($value['Blob4']);
                    $blob4 = $formatter->Format($blob4);
                } catch (Throwable $th) {
                    $blob4 = "";
                }
                $tiers[] = [
                    'tiers' => $value['Reference'],
                    'nom' => $value['Nom'],
                    'blob' => $blob,
                    'blob2' => $blob2,
                    'blob3' => $blob3,
                    'blob4' => $blob4,
                ];
            }
        }

        return $this->render('blob_translate_tiers/index.html.twig', [
            'datas' => $tiers,
            'form' => $form->createView(),
            'title' => 'Blob Tiers',
        ]);
    }
}
