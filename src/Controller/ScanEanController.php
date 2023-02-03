<?php

namespace App\Controller;

use App\Form\RetraitMarchandiseEanType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ScanEanController extends AbstractController
{
    private $mailer;
    private $repoMail;
    private $mailEnvoi;

    public function __construct(MailListRepository $repoMail, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        //parent::__construct();
    }

    /**
     * @Route("/scan/ean/{chantier}", name="app_scan_ean")
     */
    public function index($chantier = null, ArtRepository $repo, Request $request, RetraitMarchandisesEanRepository $repoRetrait): Response
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
                $em = $this->getDoctrine()->getManager();
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
            }
        }
        //dd($historique);
        return $this->render('scan_ean/index.html.twig', [
            'title' => 'Retrait produits',
            'form' => $form->createView(),
            'produit' => $produit,
            'chantier' => $chantier,
            "historiques" => $historique,
        ]);
    }

    /**
     * @Route("/scan/ean/delete/{id}/{chantier}", name="app_scan_ean-delete")
     */
    public function delete($id, $chantier = null, Request $request, RetraitMarchandisesEanRepository $repo)
    {
        $retrait = $repo->findOneBy(['id' => $id]);
        $em = $this->getDoctrine()->getManager();
        $em->remove($retrait);
        $em->flush();

        $route = $request->attributes->get('_route');
        $this->setTracking($route);

        $this->addFlash('message', 'Article supprimée avec succès');
        return $this->redirectToRoute('app_scan_ean', ['chantier' => $chantier]);
    }

    /**
     * @Route("/scan/ean/all/delete/{chantier}", name="app_scan_ean_delete_all")
     */
    public function deleteAll($chantier, Request $request, RetraitMarchandisesEanRepository $repo)
    {
        //dd($chantier);
        $retrait = $repo->findBy(['chantier' => $chantier, 'sendAt' => null]);
        foreach ($retrait as $value) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($value);
            $em->flush();
        }

        $route = $request->attributes->get('_route');
        $this->setTracking($route);

        $this->addFlash('message', 'Chantier ' . $chantier . ' supprimée avec succès');
        return $this->redirectToRoute('app_scan_ean');
    }

    /**
     * @Route("/scan/ns", name="app_scan_ean_ns")
     */
    public function ns(Request $request, RetraitMarchandisesEanRepository $repo)
    {
        $ns = $repo->getRetraiNonSoumis();

        $route = $request->attributes->get('_route');
        $this->setTracking($route);

        return $this->render('scan_ean/RetraitNonSoumis.html.twig', [
            'title' => 'Retrait produits',
            'ns' => $ns,
        ]);
    }

    /**
     * @Route("/scan/send/ean/{chantier}", name="app_scan_ean-send")
     */
    public function send($chantier = null, Request $request, RetraitMarchandisesEanRepository $repo, ArtRepository $repoArt)
    {
        $dos = 1;
        if ($chantier) {
            $histo = $repo->findBy(['chantier' => $chantier, 'sendAt' => null]);
            for ($ligHisto = 0; $ligHisto < count($histo); $ligHisto++) {
                $prod = $repoArt->getEanStock($dos, $histo[$ligHisto]->getEan());
                $historique[$ligHisto]['id'] = $histo[$ligHisto]->getId();

                $basculeSend = $repo->findOneBy(['id' => $histo[$ligHisto]->getId()]);
                $basculeSend->setSendAt(new DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->persist($basculeSend);
                $em->flush();

                $historique[$ligHisto]['ref'] = $prod['ref'];
                $historique[$ligHisto]['sref1'] = $prod['sref1'];
                $historique[$ligHisto]['sref2'] = $prod['sref2'];
                $historique[$ligHisto]['designation'] = $prod['designation'];
                $historique[$ligHisto]['uv'] = $prod['uv'];
                $historique[$ligHisto]['ean'] = $prod['ean'];
                $historique[$ligHisto]['qte'] = $histo[$ligHisto]->getQte();
            }
            // envoyer un mail si il y a des infos à envoyer
            if (count($historique) > 0) {
                // envoyer un mail
                $html = $this->renderView('mails/listeRetraitProduits.html.twig', ['historiques' => $historique, 'commentaire' => $request->request->get('ta'), 'chantier' => $chantier]);
                // TODO Remettre marina en destinataire des mails.
                $d = new DateTime();
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to('jpochet@groupe-axis.fr')
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

    /**
     * @Route("/scan/ean/ajax/{dos}/{ean}", name="app_scan_ean_ajax")
     */
    public function retourProduitAjax($dos = null, $ean = null, ArtRepository $repo, Request $request): Response
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

}
