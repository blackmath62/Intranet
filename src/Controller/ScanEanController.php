<?php

namespace App\Controller;

use App\Form\AddPicturesOrDocsType;
use App\Form\GeneralSearchType;
use App\Form\PrintEmplType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\MailListRepository;
use App\Service\EanScannerService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class ScanEanController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $entityManager;
    private $eanScannerService;
    private $productFormService;
    private $parameterBag;
    private $imageService;

    public function __construct(
        EanScannerService $eanScannerService,
        ManagerRegistry $registry,
        MailListRepository $repoMail,
        MailerInterface $mailer,
        ParameterBagInterface $parameterBag,
        ProductFormService $productFormService,
        ImageService $imageService
    ) {
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->eanScannerService = $eanScannerService;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->entityManager = $registry->getManager();
        $this->parameterBag = $parameterBag;
        $this->productFormService = $productFormService;
        $this->imageService = $imageService;
        //parent::__construct();
    }

    #[Route("/search/products", name: "app_search_products")]
    // chercher des produits par désignation ou référence avec la loupe
    public function search_products(ImageService $imageService, ArtRepository $repo, Request $request): Response
    {
        $dos = 1;

        $formAddPicturesOrDocs = $this->createForm(AddPicturesOrDocsType::class);
        $formAddPicturesOrDocs->handleRequest($request);

        if ($formAddPicturesOrDocs->isSubmitted() && $formAddPicturesOrDocs->isValid()) {
            $files = $formAddPicturesOrDocs->get('files')->getData();
            $type = $formAddPicturesOrDocs->get('type')->getData();
            $ref = $formAddPicturesOrDocs->get('reference')->getData();
            foreach ($files as $file) {
                // Logique pour traiter chaque fichier
                $filename = $type . $ref . '_' . $this->getUser()->getPseudo() . '_' . $file->getClientOriginalName();
                $cheminRelatif = '//SRVSOFT/FicJoints_R/achat_vente/articles/' . $dos . '/' . strtolower($ref);
                // Vérifier si le répertoire existe
                if (!file_exists($cheminRelatif)) {
                    // Créer le répertoire avec les permissions 0777 (modifiable selon vos besoins)
                    mkdir($cheminRelatif, 0777, true);
                }
                $filepath = $cheminRelatif . '/' . $filename;

                // Vérifier si le fichier existe déjà
                if (!file_exists($filepath)) {
                    if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                        // Appeler la méthode du service pour compresser et redimensionner l'image
                        $processedImage = $imageService->compressAndResize($file, 1000000);
                        // Enregistrer l'image dans le répertoire souhaité
                        $processedImage->save($cheminRelatif . '/' . $filename); // Ajustez le chemin selon vos besoins
                    } else {
                        $file->move($cheminRelatif, $filename);
                    }
                    $this->addFlash('success', 'Le fichier ' . $file->getClientOriginalName() . ' a été ajouté avec succés.');
                } else {
                    // Ajouter ici la logique en cas de fichier existant
                    $this->addFlash('danger', 'Le fichier ' . $file->getClientOriginalName() . ' existe déjà.');
                }
            }
        }
        return $this->render('scan_ean/rechercheProduits.html.twig', [
            'title' => 'Recherche produits',
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
            'productFormScript' => $this->productFormService->getProductFormScript(),
        ]);
    }

    #[Route("/ajax/product/delete/file/{dos}/{ref}/{file}", name: "app_product_delete_file")]
    // supprimer un fichier dans le dossier d'un article
    public function productDeleteFile($dos, $ref, $file)
    {

        $filePath = '//SRVSOFT/FicJoints_R/achat_vente/articles/' . $dos . '/' . strtolower($ref) . '/' . $file;

        // Vérifiez si le fichier existe avant de le supprimer
        if (file_exists($filePath)) {
            unlink($filePath);
            $response = new Response("Fichier supprimé avec succès", Response::HTTP_OK);
        } else {
            $response = new Response("Le fichier n'existe pas", Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    #[Route("/ajax/product/add/file/{dos}/{type}/{ref}", name: "app_product_add_file")]
    // Ajouter un fichier dans le dossier d'un article
    public function productAddFile(Request $request, ImageService $imageService, $dos, $type, $ref)
    {
        // Récupérer le fichier depuis la requête
        $file = $request->files->get('addFile');

        // Logique pour traiter chaque fichier
        $filename = $type . $ref . '_' . $this->getUser()->getPseudo() . ' ' . $file->getClientOriginalName();
        $cheminRelatif = '//SRVSOFT/FicJoints_R/achat_vente/articles/' . $dos . '/' . strtolower($ref);
        // Vérifier si le répertoire existe
        if (!file_exists($cheminRelatif)) {
            // Créer le répertoire avec les permissions 0777 (modifiable selon vos besoins)
            mkdir($cheminRelatif, 0777, true);
        }
        $filepath = $cheminRelatif . '/' . $filename;

        // Vérifier si le fichier existe déjà
        if (!file_exists($filepath)) {
            if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                // Appeler la méthode du service pour compresser et redimensionner l'image
                $processedImage = $imageService->compressAndResize($file, 1000000);
                // Enregistrer l'image dans le répertoire souhaité
                $processedImage->save($cheminRelatif . '/' . $filename); // Ajustez le chemin selon vos besoins
            } else {
                $file->move($cheminRelatif, $filename);
            }
            $this->addFlash('success', 'Le fichier ' . $file->getClientOriginalName() . ' a été ajouté avec succés.');
            $response = new Response("Fichier ajouté avec succès", Response::HTTP_OK);
        } else {
            // Ajouter ici la logique en cas de fichier existant
            $this->addFlash('danger', 'Le fichier ' . $file->getClientOriginalName() . ' existe déjà.');
            $response = new Response("Le fichier existe déjà", Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    #[Route("/scan/ean/ajax/{dos}/{ean}", name: "app_mouv_tiers_ajax")]
    // récupére une fiche produit pour l'afficher sur les pages
    public function retourProduitAjax(ArtRepository $repo, UrlGeneratorInterface $urlGenerator, $dos = null, $ean = null): JsonResponse
    {
        if ($dos === null) {
            $dos = 1; // Valeur par défaut
        }

        $location = [];
        $produit = $repo->getEanStock($dos, $ean);
        $location = $repo->getStockByLocation($dos, $produit['ean']);
        $imageExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtensions = ['pdf', 'docx'];
        $pictures = [];
        $files = [];

        if ($produit) {
            //$directory = "//SRVSOFT/FicJoints_R/achat_vente/articles/" . $dos . "/" . strtolower($produit['ref']);
            $directory = $this->parameterBag->get('fic_joints_Divalto') . '/' . $dos . '/' . strtolower($produit['ref']);

            // Vérifier si le dossier existe
            if (is_dir($directory)) {
                $allFiles = scandir($directory);

                // Filtrer les fichiers et dossiers spéciaux (.) et (..)
                $allFiles = array_filter($allFiles, function ($file) {
                    return $file !== "." && $file !== "..";
                });

                foreach ($allFiles as $file) {
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $cheminRelatif = 'https://192.168.50.244/fichiers/' . $dos . '/' . strtolower($produit['ref']) . '/' . $file;

                    if (strpos($file, 'photo_') === 0) {
                        $pictures[] = $cheminRelatif;
                    }
                    if (strpos($file, 'ft_') === 0) {
                        $files[] = $cheminRelatif;
                    }
                    if (strpos($file, 'photo_') !== 0 && strpos($file, 'ft_') !== 0 && in_array($extension, $fileExtensions)) {
                        $files[] = $cheminRelatif;
                    }
                    if (strpos($file, 'photo_') !== 0 && strpos($file, 'ft_') !== 0 && in_array($extension, $imageExtensions)) {
                        $pictures[] = $cheminRelatif;
                    }
                }
            }

            return new JsonResponse([
                'ref' => $produit['ref'],
                'sref1' => $produit['sref1'],
                'sref2' => $produit['sref2'],
                'designation' => $produit['designation'],
                'ean' => $produit['ean'],
                'uv' => $produit['uv'],
                'stock' => $location,
                'ferme' => $produit['ferme'],
                'pictures' => $pictures,
                'files' => $files,
            ]);
        } else {
            // Aucun produit trouvé, retournez une réponse avec une indication d'échec.
            return new JsonResponse(['ean' => null]);
        }
    }

    #[Route("/emplacement/scan/ajax/{dos}/{emplacement}", name: "app_emplacement_scan_ajax")]
    // vérifier que l'emplacement existe
    public function EmplacementAjax(ArtRepository $repo, $dos = null, $emplacement = null): Response
    {
        $dos = 1;
        $empl = "";
        if ($emplacement) {
            $empl = $repo->getEmpl($dos, $emplacement);
        }
        return new JsonResponse(['empl' => $empl]);
    }

    #[Route("/produit/print/{emplacement}", name: "app_scan_emplacement_print")]
    // imprimer les étiquettes produits
    public function print(Request $request, ArtRepository $repo, $emplacement = null)
    {

        $dos = 1;
        $produits = "";

        $form = $this->createForm(GeneralSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_numeric($form->getData()['search']) && strlen($form->getData()['search']) == 13) {
                $produits = $repo->getSearchArt($dos, $form->getData()['search'], 'EAN', null);
            } else {
                $produits = $repo->getSearchArt($dos, $form->getData()['search'], 'REF', null);
            }
        }

        return $this->render('scan_ean/print.html.twig', [
            'title' => 'Imprimer',
            'form' => $form->createView(),
            'produits' => $produits,
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
        ]);
    }

    #[Route("/products/search/ajax/{dos}/{search}/{checkProd}", name: "app_products_search")]
    // rechercher un produit avec Ajax pour l'afficher sur la page
    public function productsSearch(ArtRepository $repo, $dos, $search, $checkProd)
    {
        if ($dos === null) {
            $dos = 1; // Valeur par défaut
        }

        $produits = "";
        $produits = $repo->getSearchArt($dos, $search, 'REF', $checkProd);

        $imageExtensions = ['jpg', 'jpeg', 'png'];
        $result = [];

        foreach ($produits as $produit) {
            $pictures = [];
            $directory = $this->parameterBag->get('fic_joints_Divalto') . '/' . $dos . '/' . strtolower($produit['ref']);

            // Vérifier si le dossier existe
            if (is_dir($directory)) {
                $allFiles = scandir($directory);

                // Filtrer les fichiers et dossiers spéciaux (.) et (..)
                $allFiles = array_filter($allFiles, function ($file) {
                    return $file !== "." && $file !== "..";
                });

                foreach ($allFiles as $file) {
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $cheminRelatif = 'https://192.168.50.244/fichiers/' . $dos . '/' . strtolower($produit['ref']) . '/' . $file;

                    if (strpos($file, 'photo_') === 0 || (strpos($file, 'photo_') !== 0 && strpos($file, 'ft_') !== 0 && in_array($extension, $imageExtensions))) {
                        $pictures[] = $cheminRelatif;
                    }
                }
            }

            $result[] = [
                'ref' => $produit['ref'],
                'sref1' => $produit['sref1'],
                'sref2' => $produit['sref2'],
                'designation' => $produit['designation'],
                'ean' => $produit['ean'],
                'uv' => $produit['uv'],
                'stock' => $produit['stock'],
                'ferme' => $produit['ferme'],
                'pictures' => $pictures,
            ];
        }

        return new JsonResponse($result);
    }

    // Impression des étiquettes d'emplacement
    #[Route("/impression/interval/emplacements", name: "app_print_empl")]

    public function impressionEmplacement(ArtRepository $repo, Request $request): Response
    {
        $dos = 1;

        $form = $this->createForm(PrintEmplType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $empl1 = $repo->getEmpl($dos, $form->getData()['empl1']);
            $empl2 = $repo->getEmpl($dos, $form->getData()['empl2']);
            if ($empl1 && $empl2) {
                $this->addFlash('message', 'Les emplacements sont valides');
                return $this->redirectToRoute('app_send_pdf_etiquette_emplacement', ['dos' => $dos, 'empl1' => $empl1, 'empl2' => $empl2]);
            } else {
                $this->addFlash('danger', 'Emplacement invalide');
                return $this->redirectToRoute('app_print_empl');
            }

        }
        return $this->render('scan_ean/printEmpl.html.twig', [
            'title' => 'Print Empl',
            'form' => $form->createView(),
        ]);
    }

    #[Route("/impression/imprimante", name: "app_imprimante_ajax")]
    // ça ne sert à rien du tout, il faut que je vois s'il est possible de le supprimer sans faire bugger
    public function checkPrinter(): JsonResponse
    {
        $result = ['success' => false, 'response' => 'Erreur exécution...'];

        $workingFolder = 'C:\wamp64\www\Intranet\bin\\';

        $fileContent = file_get_contents($workingFolder . 'file_attente.txt');
        $decodedContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-16LE');

        $result['success'] = true;

        $tableau = [];
        $cut = explode(PHP_EOL, $decodedContent);

        $n_line = 0;
        foreach ($cut as $key => $line) {
            if (trim($line) !== '' & strlen(trim($line)) > 3) {
                $n_line++;
                if (1 === $n_line) {
                    $rows = explode('    ', $line);
                    foreach ($rows as $row => $cell) {
                        $tableau[$key][$row] = trim($cell);
                    }
                } else {
                    if (2 !== $n_line) {
                        $col[1] = explode(' ', $line)[0];
                        $col[2] = explode(' ', $line)[1];
                        $col[3] = explode('        ', $line)[2];
                        $col[4] = explode('     ', explode('... ', $line)[1])[0];
                        $col[5] = explode('  ', explode('      ', $line)[2])[0];
                        $col[6] = explode('  ', explode('      ', $line)[2])[1];
                        $col[7] = '';
                        $col[8] = explode('  ', explode('      ', $line)[2])[2];
                        $tableau[$key] = $col;
                    }
                }
            }
        }

        $response = $this->render('scan_ean/printQueue.html.twig', [
            'content' => $tableau,
        ]);

        $result['response'] = $response->getContent();
        // En cas de bug tableau faire :
        // $result['response'] = $decodedContent;

        return new JsonResponse($result, 200);
    }

}
