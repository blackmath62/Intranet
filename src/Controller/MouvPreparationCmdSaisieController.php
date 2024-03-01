<?php

namespace App\Controller;

use App\Entity\Main\MouvPreprationCommandeSaisie;
use App\Form\AddPicturesOrDocsType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\MouvPreparationCmdAdminRepository;
use App\Repository\Main\MouvPreprationCommandeSaisieRepository;
use App\Service\BlogFormaterService;
use App\Service\EanScannerService;
use App\Service\GenImportXlsxDivaltoService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use datetime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class MouvPreparationCmdSaisieController extends AbstractController
{
    private $repoEnt;
    private $productFormService;
    private $eanScannerService;
    private $repoSaisie;
    private $entityManager;
    private $repoMail;
    private $repoPreparationAdmin;
    private $mailEnvoi;
    private $blobFormater;
    private $importXlsxDivaltoService;
    private $mailer;

    public function __construct(
        ProductFormService $productFormService,
        EanScannerService $eanScannerService,
        EntRepository $repoEnt,
        ManagerRegistry $registry,
        MailListRepository $repoMail,
        MailerInterface $mailer,
        MouvPreparationCmdAdminRepository $repoPreparationAdmin,
        BlogFormaterService $blobFormater,
        GenImportXlsxDivaltoService $importXlsxDivaltoService,
        MouvPreprationCommandeSaisieRepository $repoSaisie) {
        $this->repoMail = $repoMail;
        $this->repoEnt = $repoEnt;
        $this->repoSaisie = $repoSaisie;
        $this->entityManager = $registry->getManager();
        $this->importXlsxDivaltoService = $importXlsxDivaltoService;
        $this->mailer = $mailer;
        $this->blobFormater = $blobFormater;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->productFormService = $productFormService;
        $this->repoPreparationAdmin = $repoPreparationAdmin;
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
            $product['note'] = $this->blobFormater->getFormate($product['note']);
        }

        return $this->render('mouv_preparation_cmd_saisie/index.html.twig', [
            'title' => 'Saisie de préparation',
            'products' => $products,
            'productFormScript' => $this->productFormService->getProductFormScript(),
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
        ]);
    }

    #[Route('/mouv/preparation/cmd/saisie/get/prepared/{enregistrement}', name: 'app_mouv_preparation_cmd_saisie_get_prepared')]
    public function getPrepared($enregistrement, MouvPreprationCommandeSaisieRepository $repo)
    {
        $prepared = $repo->findBy(['enregistrement' => $enregistrement]);

        // Formater les données pour les renvoyer au format JSON
        $formattedPrepared = [];
        foreach ($prepared as $item) {
            $formattedPrepared[] = [
                'id' => $item->getId(),
                'empl' => $item->getEmplacement(),
                'qte' => $item->getQte(),
            ];
        }

        // Retourner les données formatées en tant que réponse JSON
        return new JsonResponse($formattedPrepared);
    }

    #[Route('/mouv/preparation/cmd/saisie/get/somme/{enregistrement}', name: 'app_mouv_preparation_cmd_saisie_get_somme')]
    public function getSommeEnregistrement($enregistrement)
    {
        $somme = 0;
        $elements = $this->repoSaisie->findBy(['enregistrement' => $enregistrement, 'sendAt' => null]);

        foreach ($elements as $element) {
            $somme += $element->getQte(); // Remplacez getQte() par la méthode appropriée pour obtenir la quantité de chaque élément
        }
        // Retourner les données formatées en tant que réponse JSON
        return new JsonResponse($somme);
    }

    // ajouter ou modifier une préparation d'un produit
    #[Route('/mouv/preparation/cmd/saisie/set/prepared/{enrNo}/{cdNo}/{qte}/{emplacement}/{id}', name: 'app_mouv_preparation_cmd_saisie_set_prepared')]
    public function setPrepared(EntityManagerInterface $em, MouvPreprationCommandeSaisieRepository $repo, $enrNo, $cdNo, $qte, $emplacement, $id = null)
    {
        $prepared = "";
        if ($id == null) {
            $prepared = new MouvPreprationCommandeSaisie;
        } else {
            $prepared = $repo->findBy(['id' => $id]);
        }
        $prepared->setCreatedAt(new datetime())
            ->setCmd($cdNo)
            ->setEmplacement($emplacement)
            ->setEnregistrement($enrNo)
            ->setPreparateur($this->getUser()->getPseudo())
            ->setQte($qte);
        $em->persist($prepared);
        $em->flush();

        return new Response();
    }

    #[Route('/mouv/preparation/cmd/saisie/delete/prepared/{id}', name: 'app_mouv_preparation_cmd_saisie_delete_prepared')]
    public function deletePrepared($id, EntityManagerInterface $em, MouvPreprationCommandeSaisieRepository $repo)
    {
        $prepared = $repo->findOneBy(['id' => $id]);
        if (!$prepared) {
            throw $this->createNotFoundException('Prepared non trouvé avec ID: ' . $id);
        }

        $em->remove($prepared);
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

    #[Route("/mouv/preparation/cmd/saisie/send/{cmd}/{status}", name: "app_mouv_preparation_cmd_saisie_send")]
    // Envoyer la commande par mail au format excel
    public function sendCmd($cmd, $status): Response
    {
        $products = $this->repoSaisie->findBy(['cmd' => $cmd, 'sendAt' => null]);
        $cmdDivalto = $this->repoEnt->getMouvCmdListWithoutFilter($cmd);
        $tiers = $cmdDivalto[0]['tiers'];
        $i = 0;
        $donnees = [];
        foreach ($cmdDivalto as &$productDivalto) {
            $productDivalto['levy'] = [];
            foreach ($products as $productPrepare) {
                if ($productDivalto['enrNo'] == $productPrepare->getEnregistrement() && $productDivalto['cdNo'] == $productPrepare->getCmd()) {
                    $comment = '';
                    // On verifie si le produit a un code EAN
                    if (!$productDivalto['ean']) {
                        $comment = 'Pas de code EAN sur le produit';
                    } else {
                        // on verifie s'il y a suffisament de stock sur cet emplacement
                        $qteEmpl = $this->repoEnt->getQteEanInLocation(1, $productDivalto['ean'], $productPrepare->getEmplacement());
                        if ($productPrepare->getQte() <= $qteEmpl) {
                        } else {
                            $comment = 'Stock informatique insuffisant sur cet emplacement';
                        }
                    }
                    $productDivalto['levy'][] = [
                        'empl' => $productPrepare->getEmplacement(),
                        'qte' => $productPrepare->getQte(),
                        'comment' => $comment,
                    ];
                    // On Prépare le tableau pour l'import Excel
                    $indexColonnes = $this->importXlsxDivaltoService->getEnteteMouvValidationTiers();
                    // Alimentation du MOUV
                    $donnees[$i] = array_fill_keys($indexColonnes, ''); // Initialise toutes les colonnes à ''
                    $donnees[$i]['FICHE'] = 'MOUV';
                    $donnees[$i]['ENRNO'] = $productPrepare->getEnregistrement();
                    $donnees[$i]['REFERENCE'] = $productDivalto['ref']; // MOUV
                    $donnees[$i]['SREF1'] = $productDivalto['sref1']; // MOUV
                    $donnees[$i]['SREF2'] = $productDivalto['sref2']; // MOUV
                    $donnees[$i]['DESIGNATION'] = $productDivalto['designation']; // MOUV
                    $donnees[$i]['MOUV.OP'] = $productDivalto['op']; // MOUV
                    $donnees[$i]['QUANTITE'] = $productPrepare->getQte(); // MOUV
                    $donnees[$i]['MOUV.PPAR'] = ''; // MOUV
                    $donnees[$i]['MOUV.PUB'] = ''; // MOUV

                    // Alimentation du MVTL
                    $i++;
                    $donnees[$i] = array_fill_keys($indexColonnes, ''); // Initialise toutes les colonnes à ''
                    $donnees[$i]['FICHE'] = 'MVTL';
                    $donnees[$i]['VTLNO'] = '';
                    $donnees[$i]['EMPLACEMENT'] = $productPrepare->getEmplacement(); // MVTL
                    $donnees[$i]['QUANTITE_VTL'] = $productPrepare->getQte(); // MVTL
                    $i++;

                    $productPrepare->setSendAt(new DateTime());
                    $em = $this->entityManager;
                    $em->persist($productPrepare);
                    $em->flush();

                }

            }
            if ($productDivalto['note']) {
                $productDivalto['note'] = $this->blobFormater->getFormate($productDivalto['note']);
            }
        }

        $textHeadearAndFooter = $this->repoEnt->getTextHeaderAndFooter($cmd);
        if ($textHeadearAndFooter['nDb']) {
            $textHeadearAndFooter['nDb'] = $this->blobFormater->getFormate($textHeadearAndFooter['nDb']);
        }
        if ($textHeadearAndFooter['nFb']) {
            $textHeadearAndFooter['nFb'] = $this->blobFormater->getFormate($textHeadearAndFooter['nFb']);
        }

        // envoyer un mail
        // envoyer un mail si il y a des infos à envoyer
        $erreur = '';
        if (count($donnees) > 0) {
            if ($tiers) {
                $mouvXlsx = $this->importXlsxDivaltoService->get_export_excel_mouv_tiers_validation($cmd, $donnees, $tiers);
            }
        } else {
            $erreur = 'ERREUR, contacter l\'administrateur';
        }
        $html = $this->renderView('mouv_preparation_cmd_saisie/mail/sendCmd.html.twig', ['products' => $cmdDivalto, 'textHeadearAndFooter' => $textHeadearAndFooter]);
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to('clerat@lhermitte.fr')
            ->subject('Saisie de la commande ' . $cmd . ' par ' . $this->getUser()->getPseudo() . " " . $erreur)
            ->html($html);
        if (!$erreur) {
            $email->attachFromPath($mouvXlsx);
            $this->setPreparedAt($cmd, $status);
        }
        $this->mailer->send($email);
        if (!$erreur) {
            unlink($mouvXlsx);
        }

        // mettre les sendAt des enregistrements à jour

        $this->addFlash('message', 'Envoi de la commande effectué avec succès !');
        return $this->redirectToRoute('app_mouv_preparation_cmd');
    }

    #[Route("/mouv/prep/cmd/saisie/prepared/at/{cmd}/{value}", name: "app_mouv_prep_cmd_saisie_prepared_at")]
    // Basculer la commande en préparé si tous les produits sont traités
    public function setPreparedAt($cmd, $value)
    {
        $cmdPrepare = $this->repoPreparationAdmin->findOneBy(['cdNo' => $cmd]);
        if ($value == 0) {
            $cmdPrepare->setPreparedAt(null);
        } elseif ($value == 1) {
            $cmdPrepare->setPreparedAt(new DateTime());
        }

        $em = $this->entityManager;
        $em->persist($cmdPrepare);
        $em->flush();

        return;
    }

}
