<?php

namespace App\Controller;

use App\Form\AddPicturesOrDocsType;
use App\Form\RetraitMarchandiseEanType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\AffairesRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use App\Service\EanScannerService;
use App\Service\GenImportXlsxDivaltoService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class MouvTiersController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $entityManager;
    private $eanScannerService;
    private $productFormService;
    private $importXlsxDivaltoService;
    private $imageService;
    private $repoAffaire;

    public function __construct(
        EanScannerService $eanScannerService,
        ManagerRegistry $registry,
        MailListRepository $repoMail,
        MailerInterface $mailer,
        GenImportXlsxDivaltoService $importXlsxDivaltoService,
        ProductFormService $productFormService,
        ImageService $imageService,
        AffairesRepository $repoAffaire,
    ) {
        $this->mailer = $mailer;
        $this->repoAffaire = $repoAffaire;
        $this->importXlsxDivaltoService = $importXlsxDivaltoService;
        $this->repoMail = $repoMail;
        $this->eanScannerService = $eanScannerService;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->entityManager = $registry->getManager();
        $this->productFormService = $productFormService;
        $this->imageService = $imageService;
        //parent::__construct();
    }

    #[Route("/mouv/tiers/index/{chantier}", name: "app_mouv_tiers")]
    // index retrait/retour de marchandise chantier
    public function index(ArtRepository $repo, Request $request, RetraitMarchandisesEanRepository $repoRetrait, $chantier = null): Response
    {
        $dos = 1;
        $produit = "";
        $panier = [];

        // Pour pouvoir ajouter plusieurs fichiers en même temps
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
                $qte = $form->getData()->getQte();
                if ($form->get('retour')->getData() == true) {
                    $qte = -1 * $qte;
                }
                $retrait->setCreatedAt(new \DateTime())
                    ->setQte($qte)
                    ->setCreatedBy($this->getUser());
                $em = $this->entityManager;
                $em->persist($retrait);
                $em->flush();
                $this->addFlash('message', 'Produit ajouté avec succés');
                return $this->redirectToRoute('app_mouv_tiers', ['chantier' => $chantier]);
            } else {
                $chantier = $form->getData()->getChantier();
                $this->addFlash('danger', 'Produit introuvable');
                return $this->redirectToRoute('app_mouv_tiers', ['chantier' => $chantier]);
            }
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Données érronés, vos données n\'ont pas été enregistrées');
            return $this->redirectToRoute('app_mouv_tiers', ['chantier' => $chantier]);
        }
        if ($chantier) {
            $produitsPanier = $repoRetrait->findBy(['chantier' => $chantier, 'sendAt' => null]);
            for ($ligPanier = 0; $ligPanier < count($produitsPanier); $ligPanier++) {
                $prod = $repo->getEanStock($dos, $produitsPanier[$ligPanier]->getEan());
                $panier[$ligPanier]['id'] = $produitsPanier[$ligPanier]->getId();
                $panier[$ligPanier]['ref'] = $prod['ref'];
                $panier[$ligPanier]['sref1'] = $prod['sref1'];
                $panier[$ligPanier]['sref2'] = $prod['sref2'];
                $panier[$ligPanier]['designation'] = $prod['designation'];
                $panier[$ligPanier]['uv'] = $prod['uv'];
                $panier[$ligPanier]['ean'] = $prod['ean'];
                $panier[$ligPanier]['qte'] = $produitsPanier[$ligPanier]->getQte();
                $panier[$ligPanier]['stockFaux'] = $produitsPanier[$ligPanier]->getStockFaux();
                $panier[$ligPanier]['location'] = $produitsPanier[$ligPanier]->getLocation();
            }
        }
        $productData = "";
        return $this->render('mouv_tiers/index.html.twig', [
            'title' => 'Retrait produits',
            'form' => $form->createView(),
            'productData' => $productData,
            'produit' => $produit,
            'chantier' => $chantier,
            "paniers" => $panier,
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'eanScannerScript' => $this->eanScannerService->getScannerScript(),
            'productFormScript' => $this->productFormService->getProductFormScript(),
        ]);
    }

    #[Route("/mouv/tiers/one/delete/{id}/{chantier}", name: "app_mouv_tiers-delete")]

    // Effacer une ligne sur un chantier
    public function delete(RetraitMarchandisesEanRepository $repo, $id, $chantier = null)
    {
        $retrait = $repo->findOneBy(['id' => $id]);
        $em = $this->entityManager;
        $em->remove($retrait);
        $em->flush();

        $this->addFlash('message', 'Article supprimée avec succès');
        return $this->redirectToRoute('app_mouv_tiers', ['chantier' => $chantier]);
    }

    #[Route("/mouv/tiers/all/delete/{chantier}", name: "app_mouv_tiers_delete_all")]
    // Effacer intégralement un chantier
    public function deleteAll($chantier, RetraitMarchandisesEanRepository $repo)
    {
        $retrait = $repo->findBy(['chantier' => $chantier, 'sendAt' => null]);
        foreach ($retrait as $value) {
            $em = $this->entityManager;
            $em->remove($value);
            $em->flush();
        }

        $this->addFlash('message', 'Chantier ' . $chantier . ' supprimée avec succès');
        return $this->redirectToRoute('app_mouv_tiers');
    }

    #[Route("/mouv/tiers/ns", name: "app_mouv_tiers_ns")]
    // liste des chantiers qui n'ont pas été envoyés
    public function ns(RetraitMarchandisesEanRepository $repo)
    {
        $ns = $repo->getRetraiNonSoumis();

        return $this->render('mouv_tiers/RetraitNonSoumis.html.twig', [
            'title' => 'Retrait produits',
            'ns' => $ns,
        ]);
    }

    #[Route("/mouv/tiers/ajax/{affaire}", name: "app_mouv_tiers_ajax_search_tiers")]
    public function searchTiersByAffaireAjax(AffairesRepository $repo, $affaire = null): Response
    {
        $tiers = false;

        if ($affaire) {
            $affaireEntity = $repo->findOneBy(['code' => $affaire]);

            if ($affaireEntity) {
                $tiers = $affaireEntity->getTiers() . ' - ' . $affaireEntity->getNom();
            }
        }

        return new JsonResponse(['tiers' => $tiers]);
    }

    #[Route("/mouv/tiers/send/{chantier}", name: "app_mouv_tiers-send")]
    // envoyer par mail le retrait/retour de marchandise pour un chantier
    public function send(Request $request, RetraitMarchandisesEanRepository $repo, ArtRepository $repoArt, $chantier = null)
    {
        $dos = 1;
        $i = 0;
        $sf = 0;
        $retrait = "";
        $tiers = "";
        $donnees = [];
        $stockfaux = [];
        $retrait = $this->repoAffaire->findOneBy(['code' => $chantier]);
        if ($retrait) {
            $tiers = $retrait->getTiers();
        }
        $typePiece = $this->importXlsxDivaltoService->getTypePiece($tiers);
        $indexColonnes = $typePiece['indexColonne'];
        if ($chantier) {

            $panier = $repo->findBy(['chantier' => $chantier, 'sendAt' => null]);
            for ($ligPanier = 0; $ligPanier < count($panier); $ligPanier++) {
                $prod = $repoArt->getEanStock($dos, $panier[$ligPanier]->getEan());

                $basculeSend = $repo->findOneBy(['id' => $panier[$ligPanier]->getId()]);
                $basculeSend->setSendAt(new DateTime());
                $em = $this->entityManager;
                $em->persist($basculeSend);
                $em->flush();

                if ($panier[$ligPanier]->getStockFaux() == 1) {
                    $stockfaux[$sf] = 'Un stock faux a été signalé sur l\'article ' . $prod['ref'] . ' - ' . $prod['sref1'] . ' - ' . $prod['sref2'] . ' - ' . $prod['designation'] . ' à l\'emplacement ' . $panier[$ligPanier]->getLocation();
                    $sf++;
                }

                // Alimentation du MOUV
                $donnees[$i] = array_fill_keys($indexColonnes, ''); // Initialise toutes les colonnes à ''
                $donnees[$i]['FICHE'] = 'MOUV';
                $donnees[$i]['REFERENCE'] = $prod['ref']; // MOUV
                $donnees[$i]['SREF1'] = $prod['sref1']; // MOUV
                $donnees[$i]['SREF2'] = $prod['sref2']; // MOUV
                $donnees[$i]['DESIGNATION'] = $prod['designation']; // MOUV
                if ($panier[$ligPanier]->getQte() < 0) {
                    $donnees[$i]['MOUV.OP'] = $typePiece['sortie']; // MOUV
                } else {
                    $donnees[$i]['MOUV.OP'] = $typePiece['entree']; // MOUV
                }

                $donnees[$i]['QUANTITE'] = abs($panier[$ligPanier]->getQte()); // MOUV
                $donnees[$i]['MOUV.PPAR'] = ''; // MOUV
                $donnees[$i]['MOUV.PUB'] = ''; // MOUV

                // Alimentation du MVTL
                $i++;
                $donnees[$i] = array_fill_keys($indexColonnes, ''); // Initialise toutes les colonnes à ''
                $donnees[$i]['FICHE'] = 'MVTL';
                $donnees[$i]['EMPLACEMENT'] = $panier[$ligPanier]->getLocation(); // MVTL
                $donnees[$i]['QUANTITE_VTL'] = abs($panier[$ligPanier]->getQte()); // MVTL
                $i++;
            }
            // envoyer un mail si il y a des infos à envoyer
            if (count($donnees) > 0) {
                if ($tiers) {
                    $mouvXlsx = $this->importXlsxDivaltoService->get_export_excel_mouv_tiers($typePiece['typePiece'], $donnees, $tiers);
                } else {
                    $mouvXlsx = $this->importXlsxDivaltoService->get_export_excel_stock($typePiece['typePiece'], $donnees);
                }
                // envoyer un mail
                $html = $this->renderView('mouv_tiers/mails/listeRetraitProduits.html.twig', ['stockfaux' => $stockfaux, 'commentaire' => $request->request->get('ta'), 'chantier' => $chantier, 'retrait' => $retrait]);
                $d = new DateTime();
                $destinataires = ['adeschodt@lhermitte.fr', 'adefaria@lhermitte.fr', 'clerat@lhermitte.fr'];
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to(...$destinataires)
                    ->subject('Import pour le ' . $chantier . " par " . $this->getUser()->getPseudo() . " le " . $d->format('d-m-Y H:i:s'))
                    ->attachFromPath($mouvXlsx)
                    ->html($html);
                $this->mailer->send($email);
            }
        } else {
            $this->addFlash('danger', 'Pas de chantier à cloturer');
            return $this->redirectToRoute('app_mouv_tiers');
        }

        $this->addFlash('message', 'Panier cloturé avec succès');
        return $this->redirectToRoute('app_mouv_tiers');
    }
}
