<?php

namespace App\Controller;

use App\Entity\Main\Icd;
use App\Form\MetierProdType;
use App\Form\OthersDocumentsType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\IcdRepository;
use App\Repository\Main\MailListRepository;
use App\Service\GenImportDivaltoXlsxService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Shuchkin\SimpleXLSX;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Throwable;

#[IsGranted("ROLE_ADMIN")]

class DernierAchatParProduitController extends AbstractController
{

    private $repoArt;
    private $entityManager;
    private $importDivaltoXlsxService;
    private $repoMail;
    private $mailEnvoi;
    private $mailer;

    public function __construct(
        ManagerRegistry $registry,
        ArtRepository $repoArt,
        GenImportDivaltoXlsxService $importDivaltoXlsxService,
        MailListRepository $repoMail,
        MailerInterface $mailer) {
        $this->repoArt = $repoArt;
        $this->entityManager = $registry->getManager();
        $this->importDivaltoXlsxService = $importDivaltoXlsxService;
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        //parent::__construct();
    }

    #[Route("/dernier/achat/par/produit/{dos}", name: "app_dernier_achat_par_produit")]
    #[Route("/with/dernier/achat/par/produit/{dos}/{produit}/{cmp}", name: "app_dernier_achat_par_produit_with")]

    public function index(ArtRepository $repo, Request $request, $dos, $produit = null, $cmp = null): Response
    {
        $produits = '';

        $form = $this->createForm(MetierProdType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produits = $repo->getAchatParProduit($dos, $form->getData()['produits']);
            $produit = '';
            $cmp = '';
        } else {
            if ($produit) {
                $produits = $repo->getAchatParProduit($dos, $produit);
            }
        }

        return $this->render('dernier_achat_par_produit/index.html.twig', [
            'produits' => $produits,
            'produit' => $produit,
            'title' => 'Achat produit',
            'form' => $form->createView(),
            'cmp' => $cmp,
        ]);
    }

    #[Route("/import/produit", name: "app_import_produit")]

    public function import(IcdRepository $repoIcd, Request $request, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(OthersDocumentsType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('file')->getData();

            if ($file) {
                // On récupére l'identifiant de la société
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('doc', $file->getClientOriginalName()),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $fichier = 'doc/' . $newFilename;
            }
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 0);

            $em = $this->entityManager;
            // suppression des anciennes données
            $produits = $repoIcd->findAll();
            foreach ($produits as $key => $value) {
                $em->remove($value);
            }
            $em->flush();

            if ($xlsx = SimpleXLSX::parse($fichier)) {
                $mode = 'standard';
                foreach ($xlsx->rows() as $key => $r) {

                    //dd($xlsx->rows());
                    // Si $pu = Coût revient ou $ref = Article je passe à la ligne suivante
                    if (($r[3] == 'Coût revient') || ($r[0] == 'Article') || ($r[1] == 'Total')) {
                        $ref = '';
                        $des = '';
                        $sref1 = '';
                        $sref2 = '';
                        $qte = '';
                        $pu = '';
                        $mode = 'standard';
                        continue; // passer au tour de boucle suivant
                    } else {
                        if ($mode == 'sref') {
                            $sref1 = $r[0];
                            $sref2 = $r[1];
                            $qte = $r[2];
                            $pu = $r[3];
                        } else {
                            if (($r[3] == '') && $r[2] == '' && $r[0] != '') {
                                // passer en mode gestion des sous référence
                                $mode = 'sref';
                                $ref = $r[0];
                                $des = $r[1];
                                continue; // passer au tour de boucle suivant
                            } else {
                                // fonctionnement standard
                                $ref = $r[0];
                                $des = $r[1];
                                $sref1 = '';
                                $sref2 = '';
                                $qte = $r[2];
                                $pu = $r[3];
                                $mode = 'standard';
                            }
                        }
                    }

                    $produit = new Icd();

                    $produit->setRef($ref);
                    $produit->setDesignation($des);
                    $produit->SetSref1($sref1);
                    $produit->SetSref2($sref2);
                    $produit->SetQte($qte);
                    if (!is_numeric($pu) | $pu == '') {
                        $produit->SetPu(0);
                    } else {
                        $produit->SetPu($pu);
                    }
                    $cmp = $this->getImportCalculCmp(1, $ref, $sref1, $sref2, $qte, 'Dépôt');
                    if ($cmp == 0) {
                        $cmpDirect = $this->getImportCalculCmp(1, $ref, $sref1, $sref2, $qte, 'Direct');
                        if ($cmpDirect == 0) {
                            $cmpDernier = $this->getImportCalculCmp(1, $ref, $sref1, $sref2, $qte, 'Dernier');
                            if ($cmpDernier == 0) {
                                if ($pu > 0) {
                                    $produit->setPuCorrige($pu);
                                    $produit->setCommentaires('Pas achat, j\'ai ramené le pu de l\'ancien ICD');
                                } else {
                                    // aucun achat, donc surement uniquement des régularisations internes
                                    $produit->setPuCorrige(0);
                                    $produit->setCommentaires('Aucun achat sur ce produit, uniquement des bls internes !');
                                }
                            } else {
                                // il a fallu pendre le dernier prix d'achat
                                $produit->setPuCorrige($cmpDernier);
                                $produit->setCommentaires('Les achat Dépôt et Direct ne couvrent pas le stock..... j\'ai ramené le dernier prix d\'achat');
                            }
                        } else {
                            // On pioche dans le direct
                            $produit->setPuCorrige($cmpDirect);
                            $produit->setCommentaires('Les achat Dépôt ne couvrent pas le stock, il a fallu piocher dans le Direct');
                        }

                    } else {
                        // tout va bien, on a pu récupérer le pu corrigé
                        $produit->setPuCorrige($cmp);
                        $produit->setCommentaires('Pu corrigé calculé avec les derniers achats dépôts pour couvrir la quantité(cmp)');
                    }
                    $em->persist($produit);

                }
                $em->flush();

            } else {
                echo SimpleXLSX::parseError();
            }
            unlink($fichier);

        }
        $produits = $repoIcd->findAll();

        // Obtenez la date actuelle
        $dateActuelle = new DateTime();
        $date2912 = $dateActuelle->modify('-1 year');
        $date2912->setDate($date2912->format('Y'), 12, 29);
        $date3012 = clone $date2912;
        $date2812 = clone $date2912;
        $date2812 = $date2812->modify('-1 day');
        $date3012 = $date3012->modify('+1 day');
        $date2912 = $date2912->format('Y-m-d');
        $date3012 = $date3012->format('Y-m-d');
        $date2812 = $date2812->format('Y-m-d');

        // Générer le fichier pour la date 29/12
        $param = $this->importDivaltoXlsxService->param('I', 1, 2, $date2912, null, null);
        $regulsS = $this->generateXlsxRegularisation($param, $produits, 'II');
        $sorties = $this->importDivaltoXlsxService->get_export_excel($param, $regulsS['piece']);
        sleep(1);
        $sorties0 = $this->importDivaltoXlsxService->get_export_excel($param, $regulsS['piece0']);
        sleep(1);
        // Générer le fichier pour la date 30/12
        $param = $this->importDivaltoXlsxService->param('I', 1, 2, $date3012, null, null);
        $regulsE = $this->generateXlsxRegularisation($param, $produits, 'JI');
        $entree = $this->importDivaltoXlsxService->get_export_excel($param, $regulsE['piece']);
        sleep(1);
        $entree0 = $this->importDivaltoXlsxService->get_export_excel($param, $regulsE['piece0']);
        sleep(1);
        // Générer le fichier SART permettant la modification du CMP
        $param = [
            'entetes' => $this->importDivaltoXlsxService->getEnteteSart(),
            'dos' => 1,
            'date' => $date2812,
            'title' => 'Article_Sous_Reference',
        ];
        $genSarts = $this->generateXlsxSart($param, $produits);
        $sart = $this->importDivaltoXlsxService->get_export_excel_art($param, $genSarts['art']);
        sleep(1);
        $sart0 = $this->importDivaltoXlsxService->get_export_excel_art($param, $genSarts['art0']);

        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->getUser()->getEmail())
            ->subject('Fichier d\'import correction CMP Divalto')
            ->html('Veuillez intégrer ces fichiers avec toutes les précautions nécéssaires')
            ->attachFromPath($sorties)
            ->attachFromPath($sart)
            ->attachFromPath($entree);
        $this->mailer->send($email);

        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->getUser()->getEmail())
            ->subject('Fichier d\'import article à 0 € Divalto')
            ->html('Veuillez intégrer ces fichiers avec toutes les précautions nécéssaires, il faut au préalable chiffrer les produits')
            ->attachFromPath($sorties0)
            ->attachFromPath($sart0)
            ->attachFromPath($entree0);
        $this->mailer->send($email);

        unlink($sorties);
        unlink($sart);
        unlink($entree);
        unlink($sorties0);
        unlink($sart0);
        unlink($entree0);

        // Créer les fichiers d'import pour mettre à jour les CMP dans Divalto

        return $this->render('dernier_achat_par_produit/import.html.twig', [
            'produits' => $produits,
            'title' => 'Achat produit',
            'form' => $form->createView(),
        ]);
    }
    // régularisation de stock
    public function generateXlsxRegularisation($param, $produits, $op)
    {

        $piece = [];
        $piece0 = [];
        $i = 0;
        $j = 0;
        foreach ($produits as $produit) {
            $diff = 0;
            if (($produit->getPuCorrige() != 0 && $produit->getPu() != 0)) {
                $diff = abs((($produit->getPuCorrige() / $produit->getPu()) - 1) * 100);
            }
            if ($diff > 1) {

                $piece[$i] = array_fill_keys($param['entetes'], ''); // Initialise toutes les colonnes à ''
                $piece[$i]['FICHE'] = 'MOUV';
                $piece[$i]['REFERENCE'] = $produit->getRef(); // MOUV
                $piece[$i]['SREF1'] = $produit->getSref1(); // MOUV
                $piece[$i]['SREF2'] = $produit->getSref2(); // MOUV
                $piece[$i]['DESIGNATION'] = $produit->getDesignation(); // MOUV
                $piece[$i]['MOUV.OP'] = $op; // MOUV
                $piece[$i]['QUANTITE'] = $produit->getQte(); // MOUV
                $piece[$i]['MOUV.PPAR'] = ''; // MOUV
                if ($op == 'JI') {
                    $piece[$i]['MOUV.PUB'] = $produit->getPuCorrige(); // MOUV
                }
                $i++;

            }
            if ($produit->getPuCorrige() == 0) {

                $piece0[$j] = array_fill_keys($param['entetes'], ''); // Initialise toutes les colonnes à ''
                $piece0[$j]['FICHE'] = 'MOUV';
                $piece0[$j]['REFERENCE'] = $produit->getRef(); // MOUV
                $piece0[$j]['SREF1'] = $produit->getSref1(); // MOUV
                $piece0[$j]['SREF2'] = $produit->getSref2(); // MOUV
                $piece0[$j]['DESIGNATION'] = $produit->getDesignation(); // MOUV
                $piece0[$j]['MOUV.OP'] = $op; // MOUV
                $piece0[$j]['QUANTITE'] = $produit->getQte(); // MOUV
                $piece0[$j]['MOUV.PPAR'] = ''; // MOUV
                if ($op == 'JI') {
                    $piece0[$j]['MOUV.PUB'] = $produit->getPuCorrige(); // MOUV
                }
                $j++;

            }

            // Alimentation du MVTL
            /*$piece[$i] = array_fill_keys($param['entetes'], ''); // Initialise toutes les colonnes à ''
        $piece[$i]['FICHE'] = 'MVTL';
        $piece[$i]['EMPLACEMENT'] = $prodByLocation['emplacement']; // MVTL
        $piece[$i]['SERIE'] = ''; // MVTL
        $piece[$i]['QUANTITE_VTL'] = $prodByLocation['emplacementQte']; // MVTL
        $i++;*/

        }

        return ['piece' => $piece, 'piece0' => $piece0];

    }

    // Sart article pour modification CMP
    public function generateXlsxSart($param, $produits)
    {

        $art = [];
        $art0 = [];
        $i = 0;
        $j = 0;
        $date = new datetime($param['date']);
        $date = $date->format('d/m/Y');
        foreach ($produits as $produit) {

            if (($produit->getPuCorrige() != $produit->getPu())) {

                $art[$i] = array_fill_keys($param['entetes'], ''); // Initialise toutes les colonnes à ''
                $art[$i]['DOSSIER'] = $param['dos'];
                $art[$i]['REFERENCE'] = $produit->getRef();
                $art[$i]['SREFERENCE1'] = $produit->getSref1();
                $art[$i]['SREFERENCE2'] = $produit->getSref2();
                $art[$i]['CRUNITAIRE'] = $produit->getPuCorrige();
                $art[$i]['PRIXACHAT'] = $produit->getPuCorrige();
                $art[$i]['CMPUNITAIRE'] = $produit->getPuCorrige();
                $art[$i]['DATECR'] = $date;
                $art[$i]['DATECMP'] = $date;
                $art[$i]['DATEPRIXACHAT'] = $date;
                $i++;

            }
            if ($produit->getPuCorrige() == 0) {

                $art0[$j] = array_fill_keys($param['entetes'], ''); // Initialise toutes les colonnes à ''
                $art0[$j]['DOSSIER'] = $param['dos'];
                $art0[$j]['REFERENCE'] = $produit->getRef();
                $art0[$j]['SREFERENCE1'] = $produit->getSref1();
                $art0[$j]['SREFERENCE2'] = $produit->getSref2();
                $art0[$j]['DATECR'] = $date;
                $art0[$j]['DATECMP'] = $date;
                $art0[$j]['DATEPRIXACHAT'] = $date;
                $j++;

            }

        }

        return ['art' => $art, 'art0' => $art0];

    }

    #[Route("/calcul/Cmp/dernier/achat/{dos}/{produit}/{sref1}/{sref2}", name: "app_dernier_achat_calcul_cmp")]

    public function getcalculCmp(ArtRepository $repo, Request $request, $dos = null, $produit = null, $sref1 = null, $sref2 = null, $qte = null): Response
    {
        if ($sref1 == 'null') {
            $sref1 = null;
        }
        if ($sref2 == 'null') {
            $sref2 = null;
        }

        $qte = $request->request->get('qte');
        $qteInit = $qte;
        $i = 0;
        $cmp = 0;
        if ($produit) {
            $donnees = $repo->getAchatParProduitQteSign($dos, $produit, $sref1, $sref2);
            if ($qte) {
                foreach ($donnees as $prod) {
                    if ($prod['qte'] > $qte && $i == 0) {
                        $cmp = $qte * $prod['pu'];
                        $i = $i + $prod['qte'];
                        goto fin;
                    } else {
                        if ($qte > $prod['qte']) {
                            // multiplier la quantité max par le pu
                            $cmp += ($prod['qte'] * $prod['pu']);
                            $qte = $qte - $prod['qte'];
                            $i = $i + $prod['qte'];
                        } else {
                            // multiplier par la quantité restante
                            $cmp += ($qte * $prod['pu']);
                            $i = $i + $prod['qte'];
                            goto fin;
                        }
                    }
                }
                fin:
                // Si la quantité totale acheté est inférieur à la quantité en stock
                if ($i < $qteInit) {
                    $cmp = 0;
                } else {
                    $cmp = $cmp / $qteInit;
                }

            }

        } else {
            $cmp = 'Pas de données';
        }

        return $this->redirectToRoute('app_dernier_achat_par_produit_with', ['dos' => 1, 'produit' => $produit, 'cmp' => $cmp]);
    }

    public function getImportCalculCmp($dos, $produit, $sref1, $sref2, $qte, $type)
    {
        $qteInit = $qte;
        $i = 0;
        $cmp = 0;
        if ($produit) {
            if ($type == 'Dépôt') {
                $donnees = $this->repoArt->getAchatParProduitQteSign($dos, $produit, $sref1, $sref2);
            } elseif ($type == 'Direct') {
                $donnees = $this->repoArt->getAchatParProduitQteSignDepotDirect($dos, $produit, $sref1, $sref2);
            } elseif ($type == 'Dernier') {
                $donnees = $this->repoArt->getAchatParProduitQteSignDepotDirectDernier($dos, $produit, $sref1, $sref2);
                if ($donnees) {
                    $cmp = $donnees[0]['pu'];
                } else {
                    $cmp = 0;
                }
                goto dernier;
            }
            if ($qte) {
                foreach ($donnees as $prod) {
                    if ($prod['qte'] > $qte && $i == 0) {
                        $cmp = $qte * $prod['pu'];
                        $i = $i + $prod['qte'];
                        goto fin;
                    } else {
                        if ($qte > $prod['qte']) {
                            // multiplier la quantité max par le pu
                            $cmp += ($prod['qte'] * $prod['pu']);
                            $qte = $qte - $prod['qte'];
                            $i = $i + $prod['qte'];
                        } else {
                            // multiplier par la quantité restante
                            $cmp += ($qte * $prod['pu']);
                            $i = $i + $prod['qte'];

                            goto fin;
                        }
                    }
                }
                fin:
                // Si la quantité totale acheté est inférieur à la quantité en stock
                if ($i < $qteInit) {
                    $cmp = 0;
                } else {
                    try {
                        $cmp = $cmp / $qteInit;
                    } catch (Throwable $th) {
                        dd($qteInit);
                    }
                }

            }

        } else {
            $cmp = 'Pas de données';
        }
        dernier:
        return $cmp;
    }

    #[Route("/calcul/cmp/ajax/{dos}/{produit}/{sref1}/{sref2}/{qte}", name: "app_calcul_cmp_ajax")]

    public function EmplacementAjax(ArtRepository $repo, Request $request, $dos = null, $produit = null, $sref1 = null, $sref2 = null, $qte = null): Response
    {
        $dos = 1;
        $qte = 171;
        $qteInit = $qte;
        $produit = 'EVD3025';
        $i = 0;
        $cmp = 0;
        if ($produit) {
            $donnees = $repo->getAchatParProduitQteSign($dos, $produit);
            if ($qte) {
                foreach ($donnees as $prod) {
                    if ($prod['qte'] > $qte && $i == 0) {
                        $cmp = $qte * $prod['pu'];
                        $i = $i + $prod['qte'];
                        goto fin;
                    } else {
                        if ($qte > $prod['qte']) {
                            // multiplier la quantité max par le pu
                            $cmp += ($prod['qte'] * $prod['pu']);
                            $qte = $qte - $prod['qte'];
                        } else {
                            // multiplier par la quantité restante
                            $cmp += ($qte * $prod['pu']);
                            $i = $i + $prod['qte'];

                            goto fin;
                        }
                    }
                }
                fin:
                // Si la quantité totale acheté est inférieur à la quantité en stock
                if ($i < $qteInit) {
                    $cmp = 'Inexploitable';
                } else {
                    $cmp = $cmp / $qteInit;
                }

            }

        } else {
            $cmp = 'Pas de données';
        }
        return new JsonResponse(['cmp' => $cmp]);
    }
}
