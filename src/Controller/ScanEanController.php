<?php

namespace App\Controller;

use App\Entity\Main\AlimentationEmplacement;
use App\Form\AlimentationEmplacementEanType;
use App\Form\GeneralSearchType;
use App\Form\PrintEmplType;
use App\Form\RetraitMarchandiseEanType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\AlimentationEmplacementRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use Com\Tecnick\Barcode\Barcode;
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

class ScanEanController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, MailListRepository $repoMail, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }
    // Retrait chantier
    #[Route("/scan/ean/{chantier}", name: "app_scan_ean")]

    public function index(ArtRepository $repo, Request $request, RetraitMarchandisesEanRepository $repoRetrait, $chantier = null): Response
    {
        $dos = 1;
        $produit = "";
        $historique = [];
        // tracking user page for stats
        /*$tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);*/

        $form = $this->createForm(RetraitMarchandiseEanType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $produit = $repo->getEanStock($dos, $form->getData()->getEan());
            //dd($produit);
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
                //dd($prod);
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
        return $this->render('scan_ean/index.html.twig', [
            'title' => 'Retrait produits',
            'form' => $form->createView(),
            'produit' => $produit,
            'chantier' => $chantier,
            "historiques" => $historique,
        ]);
    }

    #[Route("/scan/ean/delete/{id}/{chantier}", name: "app_scan_ean-delete")]

    public function delete(RetraitMarchandisesEanRepository $repo, $id, $chantier = null)
    {
        $retrait = $repo->findOneBy(['id' => $id]);
        $em = $this->entityManager;
        $em->remove($retrait);
        $em->flush();

        // $route = $request->attributes->get('_route');
        // $this->setTracking($route);

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

    public function retourProduitAjax(ArtRepository $repo, $dos = null, $ean = null): Response
    {
        $dos = 1;
        $produit = "";
        // tracking user page for stats
        /*$tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);*/
        if ($ean) {
            $produit = $repo->getEanStock($dos, $ean);
        }
        return new JsonResponse(['ref' => $produit['ref'],
            'sref1' => $produit['sref1'],
            'sref2' => $produit['sref2'],
            'designation' => $produit['designation'],
            'ean' => $produit['ean'],
            'uv' => $produit['uv'],
            'stock' => $produit['stock'],
            'ferme' => $produit['ferme'],
        ]);
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
        // tracking user page for stats
        /*$tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);*/

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
                    ->setCreatedBy($this->getUser());
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
            }
        }
        return $this->render('scan_ean/alim_empl_scan.html.twig', [
            'title' => 'Alim Empl',
            'form' => $form->createView(),
            'produit' => $produit,
            'emplacement' => $emplacement,
            "historiques" => $historique,
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

        //  $route = $request->attributes->get('_route');
        //  $this->setTracking($route);

        return $this->render('scan_ean/alim_empl_scan_ns.html.twig', [
            'title' => 'Empl NS',
            'ns' => $ns,
        ]);
    }

    #[Route("/emplacement/scan/send/ean", name: "app_emplacement_scan_ean-send")]

    public function EmplacementsSend(Request $request, AlimentationEmplacementRepository $repo, ArtRepository $repoArt)
    {
        $dos = 1;
        $historique = [];
        $histo = $repo->findBy(['sendAt' => null]);
        for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
            $prod = $repoArt->getEanStock($dos, $histo[$ligHisto]->getEan());
            $basculeSend = $repo->findOneBy(['id' => $histo[$ligHisto]->getId()]);
            $basculeSend->setSendAt(new DateTime());
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
        }
        // envoyer un mail si il y a des infos à envoyer
        if ($historique) {
            // envoyer un mail
            $html = $this->renderView('mails/AlimentationEmplacementScan.html.twig', ['historiques' => $historique, 'commentaire' => $request->request->get('ta')]);
            $d = new DateTime();
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to('jpochet@groupe-axis.fr')
                ->subject('Liste des produits pour alimentation emplacement par ' . $this->getUser()->getPseudo() . " le " . $d->format('d-m-Y H:i:s'))
                ->html($html);
            $this->mailer->send($email);
        } else {
            $this->addFlash('danger', 'Pas d\'emplacement à cloturer');
            return $this->redirectToRoute('app_scan_ean_alim_empl');
        }

        /*$route = $request->attributes->get('_route');
        $this->setTracking($route);*/

        $this->addFlash('message', 'Soumission effectuée avec succès');
        return $this->redirectToRoute('app_scan_ean_alim_empl');
    }

    #[Route("/emplacement/produit/print/{emplacement}", name: "app_scan_emplacement_print")]

    public function print(Request $request, ArtRepository $repo, $emplacement = null)
    {

        $dos = 1;
        $produits = "";

        $form = $this->createForm(GeneralSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_numeric($form->getData()['search']) && strlen($form->getData()['search']) == 13) {
                $produits = $repo->getSearchArt($dos, $form->getData()['search'], 'EAN');
                //dd($produits);
            } else {
                $produits = $repo->getSearchArt($dos, $form->getData()['search'], 'REF');
            }
        }

        // instantiate the barcode class
        $barcode = new Barcode();

        // generate a barcode
        $bobj = $barcode->getBarcodeObj(
            'C128A', // barcode type and additional comma-separated parameters
            '1000000440324', // data string to encode
            150, // bar width (use absolute or negative value as multiplication factor)
            30, // bar height (use absolute or negative value as multiplication factor)
            'black', // foreground color
            array(0, 0, 0, 0) // padding (use absolute or negative values as multiplication factors)
        )->setBackgroundColor('white'); // background color

        // output the barcode as HTML div (see other output formats in the documentation and examples)
        //echo $bobj->getHtmlDiv();

        //  $route = $request->attributes->get('_route');
        // $this->setTracking($route);

        return $this->render('scan_ean/print.html.twig', [
            'title' => 'Imprimer',
            'form' => $form->createView(),
            'produits' => $produits,
            'ean' => $bobj->getHtmlDiv(),

        ]);
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
}
