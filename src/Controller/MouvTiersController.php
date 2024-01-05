<?php

namespace App\Controller;

use App\Form\AddPicturesOrDocsType;
use App\Form\RetraitMarchandiseEanType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\AffairesRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use App\Service\EanScannerService;
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

    #[Route("/mouv/tiers/index/{chantier}", name: "app_mouv_tiers")]
    // index retrait/retour de marchandise chantier
    public function index(ArtRepository $repo, Request $request, RetraitMarchandisesEanRepository $repoRetrait, $chantier = null): Response
    {
        $dos = 1;
        $produit = "";
        $historique = [];

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
            //dd($form->getData());
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
                $historique[$ligHisto]['location'] = $histo[$ligHisto]->getLocation();
            }
        }
        $productData = "";
        return $this->render('mouv_tiers/index.html.twig', [
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
                $html = $this->renderView('mouv_tiers/mails/listeRetraitProduits.html.twig', ['historiques' => $historique, 'commentaire' => $request->request->get('ta'), 'chantier' => $chantier]);
                $d = new DateTime();
                $destinataires = [$this->getUser()->getEmail()];
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to(...$destinataires)
                    ->subject('Liste des produits retiré pour ' . $chantier . " par " . $this->getUser()->getPseudo() . " le " . $d->format('d-m-Y H:i:s'))
                    ->html($html);
                $this->mailer->send($email);
            }
        } else {
            $this->addFlash('danger', 'Pas de chantier à cloturer');
            return $this->redirectToRoute('app_mouv_tiers');
        }

        $this->addFlash('message', 'Retrait cloturé avec succès');
        return $this->redirectToRoute('app_mouv_tiers');
    }
}
