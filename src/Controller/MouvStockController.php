<?php

namespace App\Controller;

use App\Form\AddPicturesOrDocsType;
use App\Form\AlimentationEmplacementEanType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\AlimentationEmplacementRepository;
use App\Repository\Main\MailListRepository;
use App\Service\EanScannerService;
use App\Service\GenImportXlsxDivaltoService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class MouvStockController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $entityManager;
    private $eanScannerService;
    private $productFormService;
    private $imageService;

    public function __construct(
        EanScannerService $eanScannerService,
        ManagerRegistry $registry,
        MailListRepository $repoMail,
        MailerInterface $mailer,
        ProductFormService $productFormService,
        ImageService $imageService
    ) {
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->eanScannerService = $eanScannerService;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->entityManager = $registry->getManager();
        $this->productFormService = $productFormService;
        $this->imageService = $imageService;
        //parent::__construct();
    }

    //  return $this->render('mouv_stock/index.html.twig', [

    #[Route("/mouv/stock/send", name: "app_mouv_stock_send")]

    public function mouvStockSend(Request $request, AlimentationEmplacementRepository $repo, ArtRepository $repoArt, GenImportXlsxDivaltoService $ImportXlsxService)
    {
        $dos = 1;
        $piece = [];
        $i = 0; // piece
        $is = 0; // info Stock
        $id = 0; // info Depôt
        $historique = [];
        $donneesDepot = [];
        $donneesStock = [];
        $mouvXlsx = [];
        $infoDepotStock = [];

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

        $indexColonnesInfoStock = [
            'DOSSIER',
            'REFERENCE',
            'DEPOT',
            'NATURESTOCK',
            'NUMERONOTE',
            'DATEINV',
            'FAMILLEINV',
            'PERIODEINV',
            'INVIMPPAGCOD',
            'INVPAGNB',
            'CRITEREINV',
            'Anomalies',
            'Alertes',
        ];

        $indexColonnesInfoDepot = [
            'DOSSIER',
            'REFERENCE',
            'SREFERENCE1',
            'SREFERENCE2',
            'DEPOT',
            'NATURESTOCK',
            'NUMERONOTE',
            'EMPLACEMENT',
            'STLGTSORCOD',
            'FLAGORDOSMC',
            'FAMRANGEMENT',
            'ELIGIBLESLOTTING',
            'WMPROFILCOD',
            'WMEMPLPREP',
            'RESJRNB',
            'WMEMPCONTNB',
            'Anomalies',
            'Alertes',
        ];

        $histo = $repo->findBy(['sendAt' => null]);
        // construire l'historique en allant chercher les infos sur l'article grâce à l'EAN
        for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
            $prod = $repoArt->getEanStock($dos, $histo[$ligHisto]->getEan());
            $basculeSend = $repo->findOneBy(['id' => $histo[$ligHisto]->getId()]);
            $basculeSend->setSendAt(new DateTime())
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
            $i++;

            // Alimentation du MVTL
            $piece[$i] = array_fill_keys($indexColonnesStock, ''); // Initialise toutes les colonnes à ''
            $piece[$i]['FICHE'] = 'MVTL';
            $piece[$i]['EMPLACEMENT'] = $histo[$ligHisto]->getEmplacement(); // MVTL
            $piece[$i]['SERIE'] = ''; // MVTL
            $piece[$i]['QUANTITE_VTL'] = $histo[$ligHisto]->getQte(); // MVTL
            $i++;

            // Sortir le stock de l'emplacement d'origine si nécéssaire
            if ($histo[$ligHisto]->getOldLocation() != 'Add' && $histo[$ligHisto]->getOldLocation() != '') {
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

            // Alimenter le tableau pour l'info Stock
            $donneesStock[$is] = array_fill_keys($indexColonnesInfoStock, ''); // Initialise toutes les colonnes à ''
            $donneesStock[$is]['DOSSIER'] = 1;
            $donneesStock[$is]['REFERENCE'] = $prod['ref'];
            $donneesStock[$is]['DEPOT'] = 2;
            $is++;

            // Alimenter le tableau pour l'info Depot
            $donneesDepot[$id] = array_fill_keys($indexColonnesInfoDepot, ''); // Initialise toutes les colonnes à ''
            $donneesDepot[$id]['DOSSIER'] = 1;
            $donneesDepot[$id]['REFERENCE'] = $prod['ref'];
            $donneesDepot[$id]['SREFERENCE1'] = $prod['sref1'];
            $donneesDepot[$id]['SREFERENCE2'] = $prod['sref2'];
            $donneesDepot[$id]['DEPOT'] = 2;
            $donneesDepot[$id]['EMPLACEMENT'] = $histo[$ligHisto]->getEmplacement();
            $id++;
        }

        // Créer les fichiers Excel
        if ($piece) {
            $mouvXlsx = $ImportXlsxService->get_export_excel_stock('Stock ', $piece);
        }

        if ($donneesStock || $donneesDepot) {
            $infoDepotStock = $ImportXlsxService->get_export_excel_info_stock_depot('Table article', $donneesStock, $donneesDepot);
        }

        // envoyer un mail si il y a des infos à envoyer
        if ($historique) {
            // envoyer un mail
            $html = $this->renderView('mouv_stock/mails/AlimentationEmplacementScan.html.twig', ['commentaire' => $request->request->get('ta')]);
            $d = new DateTime();
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($this->getUser()->getEmail())
                ->subject('Fichiers d\'import régularisation de stock par ' . $this->getUser()->getPseudo() . " le " . $d->format('d-m-Y H:i:s'))
                ->html($html)
                ->attachFromPath($mouvXlsx);
            if ($infoDepotStock) {
                $email->attachFromPath($infoDepotStock); // Deuxième pièce jointe
            }
            $this->mailer->send($email);
            if ($mouvXlsx) {
                unlink($mouvXlsx);
            }
            if ($infoDepotStock) {
                unlink($infoDepotStock);
            }
        } else {
            $this->addFlash('danger', 'Pas d\'emplacement à cloturer');
            return $this->redirectToRoute('app_mouv_stock');
        }

        $this->addFlash('message', 'Soumission effectuée avec succès');
        return $this->redirectToRoute('app_mouv_stock');
    }

    // Alimentation d'emplacement
    #[Route("/mouv/stock/index/{emplacement}", name: "app_mouv_stock")]
    public function index(ArtRepository $repo, Request $request, AlimentationEmplacementRepository $repoRetrait, $emplacement = null): Response
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
                return $this->redirectToRoute('app_mouv_stock', ['emplacement' => $emplacement]);
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
                return $this->redirectToRoute('app_mouv_stock', ['emplacement' => $emplacement]);
            } else {
                $emplacement = $form->getData()->getEmplacement();
                $this->addFlash('danger', 'Produit introuvable');
                return $this->redirectToRoute('app_mouv_stock', ['emplacement' => $emplacement]);
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
        return $this->render('mouv_stock/alim_empl_scan.html.twig', [
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

    #[Route("/mouv/stock/delete/all/{emplacement}", name: "app_mouv_stock_emplacement_delete_all")]
    // Supprimer toutes les lignes sur cet emplacement
    public function emplacementDeleteAll($emplacement, AlimentationEmplacementRepository $repo)
    {
        $empl = $repo->findBy(['emplacement' => $emplacement, 'sendAt' => null]);
        foreach ($empl as $value) {
            $em = $this->entityManager;
            $em->remove($value);
            $em->flush();
        }

        $this->addFlash('message', 'Emplacement ' . $emplacement . ' supprimée avec succès');
        return $this->redirectToRoute('app_mouv_stock');
    }

    #[Route("/mouv/stock/delete/{id}/{emplacement}", name: "app_mouv_stock_emplacement_delete_id")]
    // supprimer une ligne sur cette emplacement
    public function deleteEmplacementOneId(AlimentationEmplacementRepository $repo, $id, $emplacement = null)
    {
        $empl = $repo->findOneBy(['id' => $id]);
        $em = $this->entityManager;
        $em->remove($empl);
        $em->flush();

        $this->addFlash('message', 'Article supprimée avec succès');
        return $this->redirectToRoute('app_mouv_stock', ['emplacement' => $emplacement]);
    }

    #[Route("/mouv/stock/ns", name: "app_mouv_stock_emplacement_ns")]
    // Afficher les emplacements qui n'ont pas été send
    public function emplacementNs(AlimentationEmplacementRepository $repo)
    {
        $ns = $repo->getEmplacementNonSoumis();

        return $this->render('mouv_stock/alim_empl_scan_ns.html.twig', [
            'title' => 'Empl NS',
            'ns' => $ns,
        ]);
    }
}
