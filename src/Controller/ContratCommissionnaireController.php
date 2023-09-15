<?php

namespace App\Controller;

use App\Entity\Main\MailList;
use App\Entity\Main\ProduitsCommissionnaires;
use App\Form\AddEmailType;
use App\Form\YearMonthType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\ProduitsCommissionnairesRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */

class ContratCommissionnaireController extends AbstractController
{

    private $cc;
    private $repoMouv;
    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, ProduitsCommissionnairesRepository $cc, MouvRepository $repoMouv, MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->cc = $cc;
        $this->repoMail = $repoMail;
        $this->repoMouv = $repoMouv;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    /**
     * @Route("Lhermitte/contrat/commissionnaire", name="app_contrat_commissionnaire")
     */
    public function index(ProduitsCommissionnairesRepository $repo, Request $request, MailListRepository $repoMails): Response
    {

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        //$this->setTracking($tracking);

        $mouvs = "";
        $art = "";
        unset($form);
        $form = $this->createForm(AddEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $find = $repoMails->findBy(['email' => $form->getData()['email'], 'page' => $tracking]);
            if (empty($find) | is_null($find)) {
                $mail = new MailList();
                $mail->setCreatedAt(new DateTime())
                    ->setEmail($form->getData()['email'])
                    ->setPage($tracking);
                $em = $this->entityManager;
                $em->persist($mail);
                $em->flush();
            } else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                return $this->redirectToRoute('app_contrat_commissionnaire');
            }
        }
        $formCr = $this->createForm(YearMonthType::class);
        $formCr->handleRequest($request);
        if ($formCr->isSubmitted() && $formCr->isValid()) {
            $articles = $this->cc->findBy(['contratCommissionaire' => true]);
            foreach ($articles as $value) {
                if ($art == '') {
                    $art = '\'' . $value->getReference() . '\'';
                } else {
                    $art = $art . ',' . '\'' . $value->getReference() . '\'';
                }
            }
            $mois = $formCr->getData()['month'];
            $annee = $formCr->getData()['year'];
            $mouvs = $this->repoMouv->getVenteContratCommissionnaire($art, $mois, $annee);
            //dd($mouvs);
        }

        return $this->render('contrat_commissionnaire/index.html.twig', [
            'controller_name' => 'ContratCommissionnaireController',
            'title' => "contrats commissionnaires",
            'listeArticles' => $repo->findAll(),
            'listeMails' => $repoMails->findBy(['page' => $tracking]),
            'mouvs' => $mouvs,
            'form' => $form->createView(),
            'formCr' => $formCr->createView(),
        ]);
    }

    /**
     * @Route("Lhermitte/contrat/commissionnaire/delete/mail{id}", name="app_contrat_commissionnaire_delete_mail")
     */
    public function deleteMail($id, MailListRepository $repo): Response
    {

        $search = $repo->findOneBy(['id' => $id]);

        $em = $this->entityManager;
        $em->remove($search);
        $em->flush();

        $this->addFlash('message', 'le mail a bien été supprimé !');
        return $this->redirectToRoute('app_contrat_commissionnaire');
    }

    /**
     * @Route("Lhermitte/contrat/commissionnaire/update/list", name="app_contrat_commissionnaire_update_list")
     */
    public function updateList(ArtRepository $repo, ProduitsCommissionnairesRepository $cc): Response
    {
        $listeArticlesDivalto = $repo->getPhyto();
        $listeArticlesIntranet = $cc->findAll();
        foreach ($listeArticlesDivalto as $value) {
            $articleIntranet = $cc->findOneBy(['reference' => $value['reference']]);
            if (empty($articleIntranet) | is_null($articleIntranet)) {
                $article = new ProduitsCommissionnaires();
                $article->setReference($value['reference'])
                    ->setDesignation($value['designation'])
                    ->setContratCommissionaire(false)
                    ->setUpdatedAt(new DateTime())
                    ->setCreatedAt(new DateTime());
                $em = $this->entityManager;
                $em->persist($article);
                $em->flush();
            }
        }

        $this->addFlash('message', 'Mise à jour éffectuée avec succés !');
        return $this->redirectToRoute('app_contrat_commissionnaire');
    }

    /**
     * @Route("Lhermitte/contrat/commissionnaire/change/cc/{id}", name="app_contrat_commissionnaire_update_article")
     */
    public function updateArticle($id, ProduitsCommissionnairesRepository $cc): Response
    {
        $article = $cc->findOneBy(['id' => $id]);
        if ($article->getContratCommissionaire() == false) {
            $article->setContratCommissionaire(true);
        } elseif ($article->getContratCommissionaire() == true) {
            $article->setContratCommissionaire(false);
        }
        $em = $this->entityManager;
        $em->persist($article);
        $em->flush();

        $this->addFlash('message', 'Mise à jour éffectuée avec succés !');
        return $this->redirectToRoute('app_contrat_commissionnaire');
    }

    /**
     * @Route("Lhermitte/contrat/commissionnaire/send/mail", name="app_contrat_commissionnaire_send_mail")
     */
    public function sendMail(): Response
    {

        $articles = $this->cc->findBy(['contratCommissionaire' => true]);
        $art = '';
        $d = date("Y-m-d");
        $mois = date("m", strtotime($d . "- 1 months"));
        $annee = date("Y", strtotime($d . "- 1 months"));
        foreach ($articles as $value) {
            if ($art == '') {
                $art = '\'' . $value->getReference() . '\'';
            } else {
                $art = $art . ',' . '\'' . $value->getReference() . '\'';
            }
        }
        if (!is_null($art) | !empty($art)) {
            $mouvs = $this->repoMouv->getVenteContratCommissionnaire($art, $mois, $annee);
            $mailsList = $this->repoMail->findBy(['page' => 'app_contrat_commissionnaire']);
            $mails = [];
            foreach ($mailsList as $value) {
                array_push($mails, new Address($value->getEmail()));
            }
            if (!empty($mails) | !is_null($mails) | !$mails == '') {
                // envoyer un mail
                $html = $this->renderView('mails/mailContratCommissionnaire.html.twig', ['mouvs' => $mouvs, 'annee' => $annee, 'mois' => $mois]);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to(...$mails)
                    ->subject('Liste des ventes de produits sous contrat commissionnaire')
                    ->html($html);
                $this->mailer->send($email);
            }
        }

        return $this->redirectToRoute('app_contrat_commissionnaire');
    }

}
