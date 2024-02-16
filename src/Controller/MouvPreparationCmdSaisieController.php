<?php

namespace App\Controller;

use App\Entity\Main\MouvPreprationCommandeSaisie;
use App\Form\AddPicturesOrDocsType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Main\MouvPreprationCommandeSaisieRepository;
use App\Service\EanScannerService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use datetime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MouvPreparationCmdSaisieController extends AbstractController
{
    private $repoEnt;
    private $productFormService;
    private $eanScannerService;

    public function __construct(
        ProductFormService $productFormService,
        EanScannerService $eanScannerService,
        EntRepository $repoEnt, ) {
        $this->repoEnt = $repoEnt;
        $this->productFormService = $productFormService;
        $this->eanScannerService = $eanScannerService;
    }

    #[Route('/mouv/preparation/cmd/saisie/{cdNo}', name: 'app_mouv_preparation_cmd_saisie')]
    public function index(ArtRepository $repoArt, ImageService $imageService, $cdNo, Request $request): Response
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
        $products = $this->repoEnt->getMouvPreparationCmdList($cdNo);

// Boucle sur chaque produit pour ajouter les données de stock
        foreach ($products as &$product) {
            // Obtenez les données de stock pour ce produit
            $stockData = $repoArt->getStockByLocation($dos, $product['ean']);

            // Initialiser le stock total du produit à zéro
            $totalStock = 0;

            // Créez un tableau pour stocker les données de stock formatées
            $formattedStock = [];

            // Boucle sur les données de stock et formatez-les selon vos besoins
            foreach ($stockData as $stockItem) {
                // Ajoutez la quantité de chaque élément de stock au stock total du produit
                $totalStock += $stockItem['qteStock'];

                // Créez un tableau associatif pour chaque élément de stock avec emplacement et quantité
                $stockItemFormatted = [
                    'empl' => $stockItem['empl'],
                    'qte' => $stockItem['qteStock'],
                ];

                // Ajoutez cet élément formaté au tableau de stock pour ce produit
                $formattedStock[] = $stockItemFormatted;
            }

            // Ajoutez le stock total du produit à la colonne "stockTotal"
            $product['stockTotal'] = $totalStock;

            // Ajoutez le tableau de stock formaté à la colonne "stock" de ce produit
            $product['stock'] = $formattedStock;
        }
        //dd($products);
        return $this->render('mouv_preparation_cmd_saisie/index.html.twig', [
            'title' => 'Saisie de préparation',
            'products' => $products,
            'productFormScript' => $this->productFormService->getProductFormScript(),
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
        ]);
    }

    #[Route('/mouv/preparation/cmd/saisie/get/avancement/{enregistrement}/{cmd}', name: 'app_mouv_preparation_cmd_saisie_get_avancement')]
    public function getAvancement($enregistrement, $cmd, MouvPreprationCommandeSaisieRepository $repo)
    {
        $avancement = $repo->findBy(['enregistrement' => $enregistrement, 'cmd' => $cmd]);

        // Formater les données pour les renvoyer au format JSON
        $formattedAvancement = [];
        foreach ($avancement as $item) {
            $formattedAvancement[] = [
                'id' => $item->getId(),
                'empl' => $item->getEmplacement(),
                'qte' => $item->getQte(),
            ];
        }

        // Retourner les données formatées en tant que réponse JSON
        return new JsonResponse($formattedAvancement);
    }

    // ajouter ou modifier un avancement de préparation d'un produit
    #[Route('/mouv/preparation/cmd/saisie/set/avancement/{enrNo}/{cdNo}/{qte}/{emplacement}/{id}', name: 'app_mouv_preparation_cmd_saisie_set_avancement')]
    public function setAvancement(EntityManagerInterface $em, MouvPreprationCommandeSaisieRepository $repo, $enrNo, $cdNo, $qte, $emplacement, $id = null)
    {
        $avancement = "";
        if ($id == null) {
            $avancement = new MouvPreprationCommandeSaisie;
        } else {
            $avancement = $repo->findBy(['id' => $id]);
        }
        $avancement->setCreatedAt(new datetime())
            ->setCmd($cdNo)
            ->setEmplacement($emplacement)
            ->setEnregistrement($enrNo)
            ->setPreparateur($this->getUser()->getPseudo())
            ->setQte($qte);
        $em->persist($avancement);
        $em->flush();

        return new Response();
        //return $this->redirectToRoute('app_mouv_preparation_cmd_saisie_get_avancement', ['enregistrement' => $avancement->getEnregistrement(), 'cmd' => $avancement->getCmd()]);
    }

    #[Route('/mouv/preparation/cmd/saisie/delete/avancement/{id}', name: 'app_mouv_preparation_cmd_saisie_delete_avancement')]
    public function deleteAvancement($id, EntityManagerInterface $em, MouvPreprationCommandeSaisieRepository $repo)
    {
        $avancement = $repo->findOneBy(['id' => $id]);
        if (!$avancement) {
            throw $this->createNotFoundException('Avancement non trouvé avec ID: ' . $id);
        }

        $em->remove($avancement);
        $em->flush();

        return new Response();
    }

    #[Route("/mouv/preparation/cmd/saisie/emplacement/scan/ajax/{dos}/{emplacement}", name: "app_mouv_preparation_cmd_saisie_emplacement_scan_ajax")]
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

}
