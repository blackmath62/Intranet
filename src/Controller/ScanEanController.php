<?php

namespace App\Controller;

use App\Entity\Main\AlimentationEmplacement;
use App\Form\AddPicturesOrDocsType;
use App\Form\AlimentationEmplacementEanType;
use App\Form\GeneralSearchType;
use App\Form\PrintEmplType;
use App\Form\RetraitMarchandiseEanType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\AlimentationEmplacementRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use App\Service\EanScannerService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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

    #[Route("/scan/ean/{chantier}", name: "app_scan_ean")]

    public function index(ArtRepository $repo, Request $request, RetraitMarchandisesEanRepository $repoRetrait, $chantier = null): Response
    {
        $dos = 1;
        $produit = "";
        $historique = [];

        $formAddPicturesOrDocs = $this->createForm(AddPicturesOrDocsType::class);
        $formAddPicturesOrDocs->handleRequest($request);

        if ($formAddPicturesOrDocs->isSubmitted() && $formAddPicturesOrDocs->isValid()) {
            //dd($formAddPicturesOrDocs->getData());
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
                        $processedImage = $this->imageService->compressAndResize($file, 1000000);
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

        $form = $this->createForm(RetraitMarchandiseEanType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produit = $repo->getEanStock($dos, $form->getData()->getEan());
            if ($produit) {
                $retrait = $form->getData();
                $chantier = $form->getData()->getChantier();
                $retrait->setCreatedAt(new \DateTime())
                    ->setCreatedBy($this->getUser());
                $em = $this->entityManager;
                $em->persist($retrait);
                $em->flush();
                $this->addFlash('message', 'Produit ajouté avec succés');
                return $this->redirectToRoute('app_scan_ean', ['chantier' => $chantier]);
            } else {
                $chantier = $form->getData()->getChantier();
                $this->addFlash('danger', 'Produit introuvable');
                return $this->redirectToRoute('app_scan_ean', ['chantier' => $chantier]);
            }
        }
        if ($chantier) {
            $histo = $repoRetrait->findBy(['chantier' => $chantier, 'sendAt' => null]);
            for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
                $prod = $repo->getEanStock($dos, $histo[$ligHisto]->getEan());
                $historique[$ligHisto]['id'] = $histo[$ligHisto]->getId();
                $historique[$ligHisto]['ref'] = $prod['ref'];
                $historique[$ligHisto]['sref1'] = $prod['sref1'];
                $historique[$ligHisto]['sref2'] = $prod['sref2'];
                $historique[$ligHisto]['designation'] = $prod['designation'];
                $historique[$ligHisto]['uv'] = $prod['uv'];
                $historique[$ligHisto]['ean'] = $prod['ean'];
                $historique[$ligHisto]['qte'] = $histo[$ligHisto]->getQte();
                $historique[$ligHisto]['stockFaux'] = $histo[$ligHisto]->getStockFaux();
            }
        }
        $productData = "";
        return $this->render('scan_ean/index.html.twig', [
            'title' => 'Retrait produits',
            'form' => $form->createView(),
            'productData' => $productData,
            'produit' => $produit,
            'chantier' => $chantier,
            "historiques" => $historique,
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
            'productFormScript' => $this->productFormService->getProductFormScript(),
        ]);
    }

    #[Route("/search/products", name: "app_search_products")]

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

    #[Route("/scan/ean/delete/{id}/{chantier}", name: "app_scan_ean-delete")]

    public function delete(RetraitMarchandisesEanRepository $repo, $id, $chantier = null)
    {
        $retrait = $repo->findOneBy(['id' => $id]);
        $em = $this->entityManager;
        $em->remove($retrait);
        $em->flush();

        $this->addFlash('message', 'Article supprimée avec succès');
        return $this->redirectToRoute('app_scan_ean', ['chantier' => $chantier]);
    }

    #[Route("/scan/ean/all/delete/{chantier}", name: "app_scan_ean_delete_all")]

    public function deleteAll($chantier, RetraitMarchandisesEanRepository $repo)
    {
        //dd($chantier);
        $retrait = $repo->findBy(['chantier' => $chantier, 'sendAt' => null]);
        foreach ($retrait as $value) {
            $em = $this->entityManager;
            $em->remove($value);
            $em->flush();
        }

        //$route = $request->attributes->get('_route');
        // $this->setTracking($route);

        $this->addFlash('message', 'Chantier ' . $chantier . ' supprimée avec succès');
        return $this->redirectToRoute('app_scan_ean');
    }

    #[Route("/scan/ns", name: "app_scan_ean_ns")]

    public function ns(RetraitMarchandisesEanRepository $repo)
    {
        $ns = $repo->getRetraiNonSoumis();

        // $route = $request->attributes->get('_route');
        // $this->setTracking($route);

        return $this->render('scan_ean/RetraitNonSoumis.html.twig', [
            'title' => 'Retrait produits',
            'ns' => $ns,
        ]);
    }

    #[Route("/scan/send/ean/{chantier}", name: "app_scan_ean-send")]

    public function send(Request $request, RetraitMarchandisesEanRepository $repo, ArtRepository $repoArt, $chantier = null)
    {
        $dos = 1;
        if ($chantier) {
            $histo = $repo->findBy(['chantier' => $chantier, 'sendAt' => null]);
            for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
                $prod = $repoArt->getEanStock($dos, $histo[$ligHisto]->getEan());
                $historique[$ligHisto]['id'] = $histo[$ligHisto]->getId();

                $basculeSend = $repo->findOneBy(['id' => $histo[$ligHisto]->getId()]);
                $basculeSend->setSendAt(new DateTime());
                $em = $this->entityManager;
                $em->persist($basculeSend);
                $em->flush();

                $historique[$ligHisto]['ref'] = $prod['ref'];
                $historique[$ligHisto]['sref1'] = $prod['sref1'];
                $historique[$ligHisto]['sref2'] = $prod['sref2'];
                $historique[$ligHisto]['designation'] = $prod['designation'];
                $historique[$ligHisto]['uv'] = $prod['uv'];
                $historique[$ligHisto]['ean'] = $prod['ean'];
                $historique[$ligHisto]['qte'] = $histo[$ligHisto]->getQte();
                $historique[$ligHisto]['stockFaux'] = $histo[$ligHisto]->getStockFaux();
            }
            // envoyer un mail si il y a des infos à envoyer
            if (count($historique) > 0) {
                // envoyer un mail
                $html = $this->renderView('mails/listeRetraitProduits.html.twig', ['historiques' => $historique, 'commentaire' => $request->request->get('ta'), 'chantier' => $chantier]);
                $d = new DateTime();
                $destinataires = ['adeschodt@lhermitte.fr', 'adefaria@lhermitte.fr'];
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to(...$destinataires)
                    ->subject('Liste des produits retiré pour ' . $chantier . " par " . $this->getUser()->getPseudo() . " le " . $d->format('d-m-Y H:i:s'))
                    ->html($html);
                $this->mailer->send($email);
            }
        } else {
            $this->addFlash('danger', 'Pas de chantier à cloturer');
            return $this->redirectToRoute('app_scan_ean');
        }

        /*$route = $request->attributes->get('_route');
        $this->setTracking($route);*/

        $this->addFlash('message', 'Retrait cloturé avec succès');
        return $this->redirectToRoute('app_scan_ean');
    }

    #[Route("/scan/ean/ajax/{dos}/{ean}", name: "app_scan_ean_ajax")]
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

    public function EmplacementAjax(ArtRepository $repo, $dos = null, $emplacement = null): Response
    {
        $dos = 1;
        $empl = "";
        if ($emplacement) {
            $empl = $repo->getEmpl($dos, $emplacement);
        }
        return new JsonResponse(['empl' => $empl]);
    }

    // Alimentation d'emplacement
    #[Route("/emplacement/scan/ean/{emplacement}", name: "app_scan_ean_alim_empl")]

    public function alimentationEmplacement(ArtRepository $repo, Request $request, AlimentationEmplacementRepository $repoRetrait, $emplacement = null): Response
    {
        $dos = 1;
        $produit = "";
        $historique = [];

        $form = $this->createForm(AlimentationEmplacementEanType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produit = $repo->getEanStock($dos, $form->getData()->getEan());
            $empl = $repo->getEmpl($dos, $form->getData()->getEmplacement());
            if (!$empl) {
                $this->addFlash('danger', 'Emplacement introuvable');
                return $this->redirectToRoute('app_scan_ean_alim_empl', ['emplacement' => $emplacement]);
            }
            if ($produit) {
                $retrait = $form->getData();
                $emplacement = $form->getData()->getEmplacement();
                $retrait->setCreatedAt(new \DateTime())
                    ->setCreatedBy($this->getUser())
                    ->setQte($form->getData()->getQte())
                ;
                $em = $this->entityManager;
                $em->persist($retrait);
                $em->flush();
                $this->addFlash('message', 'Produit ajouté avec succés');
                return $this->redirectToRoute('app_scan_ean_alim_empl', ['emplacement' => $emplacement]);
            } else {
                $emplacement = $form->getData()->getEmplacement();
                $this->addFlash('danger', 'Produit introuvable');
                return $this->redirectToRoute('app_scan_ean_alim_empl', ['emplacement' => $emplacement]);
            }
        }
        $formAddPicturesOrDocs = $this->createForm(AddPicturesOrDocsType::class);
        $formAddPicturesOrDocs->handleRequest($request);

        if ($formAddPicturesOrDocs->isSubmitted() && $formAddPicturesOrDocs->isValid()) {
            //dd($formAddPicturesOrDocs->getData());
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
                        $processedImage = $this->imageService->compressAndResize($file, 1000000);
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

        if ($emplacement) {
            $histo = $repoRetrait->findBy(['emplacement' => $emplacement, 'sendAt' => null]);
            for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
                $prod = $repo->getEanStock($dos, $histo[$ligHisto]->getEan());
                $historique[$ligHisto]['id'] = $histo[$ligHisto]->getId();
                $historique[$ligHisto]['emplacement'] = $histo[$ligHisto]->getEmplacement();
                $historique[$ligHisto]['ref'] = $prod['ref'];
                $historique[$ligHisto]['sref1'] = $prod['sref1'];
                $historique[$ligHisto]['sref2'] = $prod['sref2'];
                $historique[$ligHisto]['designation'] = $prod['designation'];
                $historique[$ligHisto]['uv'] = $prod['uv'];
                $historique[$ligHisto]['ean'] = $prod['ean'];
                $historique[$ligHisto]['qte'] = $histo[$ligHisto]->getQte();
                $historique[$ligHisto]['oldLocation'] = $histo[$ligHisto]->getOldLocation();
            }
        }
        return $this->render('scan_ean/alim_empl_scan.html.twig', [
            'title' => 'Alim Empl',
            'form' => $form->createView(),
            'produit' => $produit,
            'emplacement' => $emplacement,
            "historiques" => $historique,
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
            'productFormScript' => $this->productFormService->getProductFormScript(),
        ]);
    }

    #[Route("/emplacement/scan/ean/all/delete/{emplacement}", name: "app_emplacement_scan_ean_delete_all")]

    public function emplacementDeleteAll($emplacement, AlimentationEmplacementRepository $repo)
    {
        //dd($emplacement);
        $empl = $repo->findBy(['emplacement' => $emplacement, 'sendAt' => null]);
        foreach ($empl as $value) {
            $em = $this->entityManager;
            $em->remove($value);
            $em->flush();
        }

        // $route = $request->attributes->get('_route');
        //  $this->setTracking($route);

        $this->addFlash('message', 'Emplacement ' . $emplacement . ' supprimée avec succès');
        return $this->redirectToRoute('app_scan_ean_alim_empl');
    }

    #[Route("/emplacement/scan/ean/delete/{id}/{emplacement}", name: "app_emplacement_scan_ean-delete")]

    public function deleteEmplacement(AlimentationEmplacementRepository $repo, $id, $emplacement = null)
    {
        $empl = $repo->findOneBy(['id' => $id]);
        $em = $this->entityManager;
        $em->remove($empl);
        $em->flush();

        //  $route = $request->attributes->get('_route');
        //  $this->setTracking($route);

        $this->addFlash('message', 'Article supprimée avec succès');
        return $this->redirectToRoute('app_scan_ean_alim_empl', ['emplacement' => $emplacement]);
    }

    #[Route("/emplacement/scan/ns", name: "app_scan_emplacement_ns")]

    public function emplacementNs(AlimentationEmplacementRepository $repo)
    {
        $ns = $repo->getEmplacementNonSoumis();

        return $this->render('scan_ean/alim_empl_scan_ns.html.twig', [
            'title' => 'Empl NS',
            'ns' => $ns,
        ]);
    }

    #[Route("/emplacement/scan/send/ean", name: "app_emplacement_scan_ean-send")]

    public function EmplacementsSend(Request $request, AlimentationEmplacementRepository $repo, ArtRepository $repoArt)
    {
        $dos = 1;
        $piece = [];
        $i = 0;
        $historique = [];

        $indexColonnesStock = [
            'FICHE',
            'DOSSIER',
            'ETABLISSEMENT',
            'REF_PIECE',
            'CODE_TIERS',
            'CODE_OP',
            'DEPOT',
            'DEPOT_DESTINATION',
            'ENT.PIDT',
            'ENT.PIREF',
            'NO_SOUS_LIGNE',
            'REFERENCE',
            'SREF1',
            'SREF2',
            'DESIGNATION',
            'REF_FOURNISSEUR',
            'MOUV.OP',
            'QUANTITE',
            'MOUV.PPAR',
            'MOUV.PUB',
            'EMPLACEMENT',
            'EMPLACEMENT_DESTINATION',
            'SERIE',
            'QUANTITE_VTL',
        ];

        $histo = $repo->findBy(['sendAt' => null]);
        // construire l'historique en allant chercher les infos sur l'article grâce à l'EAN
        for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
            $prod = $repoArt->getEanStock($dos, $histo[$ligHisto]->getEan());
            $basculeSend = $repo->findOneBy(['id' => $histo[$ligHisto]->getId()]);
            // $basculeSend->setSendAt(new DateTime())
            ;
            $em = $this->entityManager;
            $em->persist($basculeSend);
            $em->flush();

            $historique[$ligHisto]['emplacement'] = $histo[$ligHisto]->getEmplacement();
            $historique[$ligHisto]['ref'] = $prod['ref'];
            $historique[$ligHisto]['sref1'] = $prod['sref1'];
            $historique[$ligHisto]['sref2'] = $prod['sref2'];
            $historique[$ligHisto]['designation'] = $prod['designation'];
            $historique[$ligHisto]['uv'] = $prod['uv'];
            $historique[$ligHisto]['ean'] = $prod['ean'];
            $historique[$ligHisto]['qte'] = $histo[$ligHisto]->getQte();
            $historique[$ligHisto]['oldLocation'] = $histo[$ligHisto]->getOldLocation();

            // Entrée en stock => JI
            // Alimentation du MOUV
            $piece[$i] = array_fill_keys($indexColonnesStock, ''); // Initialise toutes les colonnes à ''
            $piece[$i]['FICHE'] = 'MOUV';
            $piece[$i]['REFERENCE'] = $prod['ref']; // MOUV
            $piece[$i]['SREF1'] = $prod['sref1']; // MOUV
            $piece[$i]['SREF2'] = $prod['sref2']; // MOUV
            $piece[$i]['DESIGNATION'] = $prod['designation']; // MOUV
            $piece[$i]['REF_FOURNISSEUR'] = ''; // MOUV
            $piece[$i]['MOUV.OP'] = 'JI'; // MOUV
            $piece[$i]['QUANTITE'] = $histo[$ligHisto]->getQte(); // MOUV
            $piece[$i]['MOUV.PPAR'] = ''; // MOUV
            $piece[$i]['MOUV.PUB'] = ''; // MOUV

            // Alimentation du MVTL
            $i++;
            $piece[$i] = array_fill_keys($indexColonnesStock, ''); // Initialise toutes les colonnes à ''
            $piece[$i]['FICHE'] = 'MVTL';
            $piece[$i]['EMPLACEMENT'] = $histo[$ligHisto]->getEmplacement(); // MVTL
            $piece[$i]['SERIE'] = ''; // MVTL
            $piece[$i]['QUANTITE_VTL'] = $histo[$ligHisto]->getQte(); // MVTL
            $i++;

            // Sortir le stock de l'emplacement d'origine si nécéssaire
            if ($histo[$ligHisto]->getOldLocation() != 'Add') {
                // Sortie de stock => II
                // Alimentation du MOUV
                $piece[$i] = array_fill_keys($indexColonnesStock, ''); // Initialise toutes les colonnes à ''
                $piece[$i]['FICHE'] = 'MOUV';
                $piece[$i]['REFERENCE'] = $prod['ref']; // MOUV
                $piece[$i]['SREF1'] = $prod['sref1']; // MOUV
                $piece[$i]['SREF2'] = $prod['sref2']; // MOUV
                $piece[$i]['DESIGNATION'] = $prod['designation']; // MOUV
                $piece[$i]['REF_FOURNISSEUR'] = ''; // MOUV
                $piece[$i]['MOUV.OP'] = 'II'; // MOUV
                $piece[$i]['QUANTITE'] = $histo[$ligHisto]->getQte(); // MOUV
                $piece[$i]['MOUV.PPAR'] = ''; // MOUV
                $piece[$i]['MOUV.PUB'] = ''; // MOUV

                // Alimentation du MVTL
                $i++;
                $piece[$i] = array_fill_keys($indexColonnesStock, ''); // Initialise toutes les colonnes à ''
                $piece[$i]['FICHE'] = 'MVTL';
                $piece[$i]['EMPLACEMENT'] = $histo[$ligHisto]->getOldLocation(); // MVTL
                $piece[$i]['SERIE'] = ''; // MVTL
                $piece[$i]['QUANTITE_VTL'] = $histo[$ligHisto]->getQte(); // MVTL
                $i++;
            }

        }

        // Créer les fichiers Excel
        if ($piece) {
            $mouvXlsx = $this->get_export_excel_stock('Stock ', $piece);
        }

        // envoyer un mail si il y a des infos à envoyer
        if ($historique) {
            // envoyer un mail
            $html = $this->renderView('scan_ean/mails/AlimentationEmplacementScan.html.twig', ['commentaire' => $request->request->get('ta')]);
            $d = new DateTime();
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($this->getUser()->getEmail())
                ->subject('Soumission d\'alimentation d\'emplacement par ' . $this->getUser()->getPseudo() . " le " . $d->format('d-m-Y H:i:s'))
                ->html($html)
                ->attachFromPath($mouvXlsx);
            $this->mailer->send($email);
        } else {
            $this->addFlash('danger', 'Pas d\'emplacement à cloturer');
            return $this->redirectToRoute('app_scan_ean_alim_empl');
        }

        $this->addFlash('message', 'Soumission effectuée avec succès');
        return $this->redirectToRoute('app_scan_ean_alim_empl');
    }

    #[Route("/produit/print/{emplacement}", name: "app_scan_emplacement_print")]

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

    // Impression étiquette d'emplacement
    #[Route("/impression/emplacement", name: "app_print_empl")]

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

// générer un fichier Excel qui sera envoyé par mail à l'utilisateur
    public function getData($donnees): array
    {
        $list = [];

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['FICHE'],
                $donnee['DOSSIER'],
                $donnee['ETABLISSEMENT'],
                $donnee['REF_PIECE'],
                $donnee['TYPE_TIERS'],
                $donnee['TYPE_PIECE'],
                $donnee['CODE_TIERS'],
                $donnee['CODE_OP'],
                $donnee['DEPOT'],
                $donnee['PIDT'],
                $donnee['PIREF'],
                $donnee['NO_SOUS_LIGNE'],
                $donnee['REFERENCE'],
                $donnee['SREF1'],
                $donnee['SREF2'],
                $donnee['DESIGNATION'],
                $donnee['REF_FOURNISSEUR'],
                $donnee['MOUV.OP'],
                $donnee['EMPLACEMENT'],
                $donnee['SERIE'],
                $donnee['QUANTITE_VTL'],
            ];
        }
        return $list;
    }

    public function get_export_excel($typePiece, $donnees)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Piece');
        // Entête de colonne
        $sheet->getCell('A3')->setValue('FICHE'); //IPAR
        $sheet->getCell('B3')->setValue('DOSSIER'); //IPAR
        $sheet->getCell('C3')->setValue('ETABLISSEMENT'); //IPAR
        $sheet->getCell('D3')->setValue('REF_PIECE'); //IPAR
        $sheet->getCell('E3')->setValue('TYPE_TIERS'); //IPAR
        $sheet->getCell('F3')->setValue('TYPE_PIECE'); //IPAR
        $sheet->getCell('G3')->setValue('CODE_TIERS'); //ENT
        $sheet->getCell('H3')->setValue('CODE_OP'); //ENT
        $sheet->getCell('I3')->setValue('DEPOT'); //ENT
        $sheet->getCell('J3')->setValue('ENT.PIDT'); //ENT
        $sheet->getCell('K3')->setValue('ENT.PIREF'); //ENT
        $sheet->getCell('L3')->setValue('NO_SOUS_LIGNE'); //MOUV
        $sheet->getCell('M3')->setValue('REFERENCE'); //MOUV
        $sheet->getCell('N3')->setValue('SREF1'); //MOUV
        $sheet->getCell('O3')->setValue('SREF2'); //MOUV
        $sheet->getCell('P3')->setValue('DESIGNATION'); //MOUV
        $sheet->getCell('Q3')->setValue('REF_FOURNISSEUR'); //MOUV
        $sheet->getCell('R3')->setValue('MOUV.OP'); //MOUV
        $sheet->getCell('S3')->setValue('QUANTITE'); //MOUV
        $sheet->getCell('T3')->setValue('MOUV.PPAR'); //MOUV
        $sheet->getCell('U3')->setValue('MOUV.PUB'); //MOUV
        $sheet->getCell('V3')->setValue('EMPLACEMENT'); //MVTL
        $sheet->getCell('W3')->setValue('SERIE'); //MVTL
        $sheet->getCell('X3')->setValue('QUANTITE_VTL'); //MVTL
        $sheet->getCell('Y3')->setValue('ERREUR');

        // Information sur la piéce IPAR ET ENT
        $sheet->getCell('A4')->setValue('IPAR'); //IPAR
        $sheet->getCell('B4')->setValue('1'); //IPAR DOSSIER
        $sheet->getCell('C4')->setValue(''); //IPAR ETABLISSEMENT
        $sheet->getCell('D4')->setValue(''); //IPAR REF_PIECE
        $sheet->getCell('E4')->setValue('C'); //IPAR TYPE_TIERS
        $sheet->getCell('F4')->setValue('1'); //IPAR TYPE_PIECE
        $sheet->getCell('A5')->setValue('ENT'); //ENT
        $sheet->getCell('G5')->setValue('CodeTier'); //ENT CODE_TIERS
        $sheet->getCell('H5')->setValue('C'); //ENT CODE_OP
        $sheet->getCell('I5')->setValue('2'); //ENT DEPOT
        $sheet->getCell('J5')->setValue(new DateTime()); //ENT ENT.PIDT
        $sheet->getCell('K5')->setValue('Import xlsx par ' . $this->getUser()->getPseudo()); //ENT ENT.PIREF

        // Increase row cursor after header write
        $sheet->fromArray($this->getData($donnees), null, 'A6', true);

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = $typePiece . 'Import xlsx par ' . $this->getUser()->getPseudo() . ' Piece alim empl le ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('C1')->setValue('Intégration de pièce: Nouvelle création');
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);

        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        // Return the excel file as an attachment

        $chemin = 'doc/Logistique/';
        $fichier = $chemin . '/' . $fileName;
        $writer->save($fichier);
        return $fichier;
    }

    public function get_export_excel_stock($typePiece, $donnees)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Piece');
        // Entête de colonne
        $sheet->getCell('A3')->setValue('FICHE'); //IPAR
        $sheet->getCell('B3')->setValue('DOSSIER'); //IPAR
        $sheet->getCell('C3')->setValue('ETABLISSEMENT'); //IPAR
        $sheet->getCell('D3')->setValue('REF_PIECE'); //IPAR
        $sheet->getCell('E3')->setValue('CODE_TIERS'); //ENT
        $sheet->getCell('F3')->setValue('CODE_OP'); //ENT
        $sheet->getCell('G3')->setValue('DEPOT'); //ENT
        $sheet->getCell('H3')->setValue('DEPOT_DESTINATION'); //ENT
        $sheet->getCell('I3')->setValue('ENT.PIDT'); //ENT
        $sheet->getCell('J3')->setValue('ENT.PIREF'); //ENT
        $sheet->getCell('K3')->setValue('NO_SOUS_LIGNE'); //MOUV
        $sheet->getCell('L3')->setValue('REFERENCE'); //MOUV
        $sheet->getCell('M3')->setValue('SREF1'); //MOUV
        $sheet->getCell('N3')->setValue('SREF2'); //MOUV
        $sheet->getCell('O3')->setValue('DESIGNATION'); //MOUV
        $sheet->getCell('P3')->setValue('REF_FOURNISSEUR'); //MOUV
        $sheet->getCell('Q3')->setValue('MOUV.OP'); //MOUV
        $sheet->getCell('R3')->setValue('QUANTITE'); //MOUV
        $sheet->getCell('S3')->setValue('MOUV.PPAR'); //MOUV
        $sheet->getCell('T3')->setValue('MOUV.PUB'); //MOUV
        $sheet->getCell('U3')->setValue('EMPLACEMENT'); //MVTL
        $sheet->getCell('V3')->setValue('EMPLACEMENT_DESTINATION'); //MVTL
        $sheet->getCell('W3')->setValue('SERIE'); //MVTL
        $sheet->getCell('X3')->setValue('QUANTITE_VTL'); //MVTL
        $sheet->getCell('Y3')->setValue('ERREUR');

        // Information sur la piéce IPAR ET ENT
        $sheet->getCell('A4')->setValue('IPAR'); //IPAR
        $sheet->getCell('B4')->setValue('1'); //IPAR DOSSIER
        $sheet->getCell('C4')->setValue(''); //IPAR ETABLISSEMENT
        $sheet->getCell('D4')->setValue(''); //IPAR REF_PIECE
        $sheet->getCell('A5')->setValue('ENT'); //ENT
        $sheet->getCell('E5')->setValue('I0000000'); //ENT CODE_TIERS
        $sheet->getCell('F5')->setValue('JI'); //ENT CODE_OP
        $sheet->getCell('G5')->setValue('2'); //ENT DEPOT
        $sheet->getCell('H5')->setValue('2'); //ENT DEPOT_DESTINATION
        $sheet->getCell('I5')->setValue(new DateTime()); //ENT ENT.PIDT
        $sheet->getCell('J5')->setValue('Import xlsx par ' . $this->getUser()->getPseudo()); //ENT ENT.PIREF

        // Increase row cursor after header write
        $sheet->fromArray($this->getData_stock($donnees), null, 'A6', true);

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = 'Import ' . $typePiece . ' ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('C1')->setValue('Mise à jour via Import du Stock');
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);

        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        // Return the excel file as an attachment

        $chemin = 'doc/Logistique/';
        $fichier = $chemin . '/' . $fileName;
        $writer->save($fichier);
        return $fichier;
    }

    // générer un fichier Excel qui sera envoyé par mail à l'utilisateur
    public function getData_stock($donnees): array
    {
        $list = [];

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['FICHE'],
                $donnee['DOSSIER'],
                $donnee['ETABLISSEMENT'],
                $donnee['REF_PIECE'],
                $donnee['CODE_TIERS'],
                $donnee['CODE_OP'],
                $donnee['DEPOT'],
                $donnee['DEPOT_DESTINATION'],
                $donnee['ENT.PIDT'],
                $donnee['ENT.PIREF'],
                $donnee['NO_SOUS_LIGNE'],
                $donnee['REFERENCE'],
                $donnee['SREF1'],
                $donnee['SREF2'],
                $donnee['DESIGNATION'],
                $donnee['REF_FOURNISSEUR'],
                $donnee['MOUV.OP'],
                $donnee['QUANTITE'],
                $donnee['MOUV.PPAR'],
                $donnee['MOUV.PUB'],
                $donnee['EMPLACEMENT'],
                $donnee['EMPLACEMENT_DESTINATION'],
                $donnee['SERIE'],
                $donnee['QUANTITE_VTL'],
            ];
        }
        return $list;
    }

}
