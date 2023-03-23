<?php

namespace App\Controller;

use App\Entity\Main\Icd;
use App\Form\MetierProdType;
use App\Form\OthersDocumentsType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\IcdRepository;
use Shuchkin\SimpleXLSX;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Throwable;

class DernierAchatParProduitController extends AbstractController
{

    private $repoArt;

    public function __construct(ArtRepository $repoArt)
    {
        $this->repoArt = $repoArt;
        //parent::__construct();
    }

    /**
     * @Route("/dernier/achat/par/produit/{dos}", name="app_dernier_achat_par_produit")
     * @Route("/with/dernier/achat/par/produit/{dos}/{produit}/{cmp}", name="app_dernier_achat_par_produit_with")
     */
    public function index($dos, $produit = null, $cmp = null, ArtRepository $repo, Request $request): Response
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

    /**
     * @Route("/import/produit", name="app_import_produit")
     */
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

            $em = $this->getDoctrine()->getManager();
            // suppression des anciennes données
            $produits = $repoIcd->findAll();
            foreach ($produits as $key => $value) {
                $em->remove($value);
            }
            $em->flush();

            if ($xlsx = SimpleXLSX::parse($fichier)) {

                foreach ($xlsx->rows() as $key => $r) {
                    $produit = new Icd();
                    $produit->setRef($r[0]);
                    $produit->setDesignation($r[1]);
                    $produit->SetSref1($r[2]);
                    $produit->SetSref2($r[3]);
                    $produit->SetQte($r[4]);
                    if (!is_numeric($r[5]) | $r[5] == '') {
                        $produit->SetPu(0);
                    } else {
                        $produit->SetPu($r[5]);
                    }
                    $cmp = $this->getImportCalculCmp(1, $r[0], $r[2], $r[3], $r[4], 'Dépôt');
                    if ($cmp == 0) {
                        $cmpDirect = $this->getImportCalculCmp(1, $r[0], $r[2], $r[3], $r[4], 'Direct');
                        if ($cmpDirect == 0) {
                            $cmpDernier = $this->getImportCalculCmp(1, $r[0], $r[2], $r[3], $r[4], 'Dernier');
                            if ($cmpDernier == 0) {
                                if ($r[5] > 0) {
                                    $produit->setPuCorrige($r[5]);
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
        return $this->render('dernier_achat_par_produit/import.html.twig', [
            'produits' => $produits,
            'title' => 'Achat produit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/calcul/Cmp/dernier/achat/{dos}/{produit}/{sref1}/{sref2}", name="app_dernier_achat_calcul_cmp")
     */
    public function getcalculCmp($dos = null, $produit = null, $sref1 = null, $sref2 = null, $qte = null, ArtRepository $repo, Request $request): Response
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
                            //if ($prod['qte'] < 0 && $i == 0) {
                            //} else {
                            // multiplier la quantité max par le pu
                            $cmp += ($prod['qte'] * $prod['pu']);
                            $qte = $qte - $prod['qte'];
                            $i = $i + $prod['qte'];
                            //}
                        } else {
                            // multiplier par la quantité restante
                            $cmp += ($qte * $prod['pu']);
                            $i = $i + $prod['qte'];
                            //dd($qte);
                            /*if ($prod['ref'] == 'STABENCH2000-1500MM') {
                            dd($i);
                            }*/

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

    /**
     * @Route("/calcul/cmp/ajax/{dos}/{produit}/{sref1}/{sref2}/{qte}", name="app_calcul_cmp_ajax")
     */
    public function EmplacementAjax($dos = null, $produit = null, $sref1 = null, $sref2 = null, $qte = null, ArtRepository $repo, Request $request): Response
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
