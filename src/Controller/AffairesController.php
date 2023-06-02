<?php

namespace App\Controller;

use App\Entity\Main\AffairePiece;
use App\Entity\Main\Affaires;
use App\Entity\Main\OthersDocuments;
use App\Form\AffaireType;
use App\Form\OthersDocumentsType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\AffairePieceRepository;
use App\Repository\Main\AffairesRepository;
use App\Repository\Main\CommentairesRepository;
use App\Repository\Main\OthersDocumentsRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AffairesController extends AbstractController
{

    private $repoMouv;
    private $repoAffaires;
    private $repoAffairePiece;
    private $repoComments;
    private $repoDocs;
    private $mailer;

    public function __construct(AffairePieceRepository $repoAffairePiece, OthersDocumentsRepository $repoDocs, MailerInterface $mailer, CommentairesRepository $repoComments, AffairesRepository $repoAffaires, MouvRepository $repoMouv)
    {
        $this->repoMouv = $repoMouv;
        $this->repoAffaires = $repoAffaires;
        $this->repoComments = $repoComments;
        $this->repoDocs = $repoDocs;
        $this->mailer = $mailer;
        $this->repoAffairePiece = $repoAffairePiece;
        //parent::__construct();
    }

    /**
     * @Route("/Lhermitte/affaire/me/ok", name="app_affaire_me_ok")
     * @Route("/Lhermitte/affaire/me/nok", name="app_affaire_me_nok")
     */
    public function affaire(Request $request): Response
    {
        if ($request->attributes->get('_route') == 'app_affaire_me_ok') {
            $affaires = $this->repoAffaires->findFinish();
        } elseif ($request->attributes->get('_route') == 'app_affaire_me_nok') {
            $affaires = $this->repoAffaires->findNotFinish();
        }

        return $this->render('affaires/affaire.html.twig', [
            // todo à voir pour filtrer l'état en fonction de la page affichée
            'affaires' => $affaires,
            'title' => 'Affaires',
        ]);
    }

    /**
     * @Route("/Lhermitte/pieces/affaire/nok/{{affaire}}", name="app_piece_affaire_nok")
     * @Route("/Lhermitte/pieces/affaire/ok/{{affaire}}", name="app_piece_affaire_ok")
     */
    public function pieceAffaire($affaire, Request $request): Response
    {
        if ($request->attributes->get('_route') == 'app_piece_affaire_ok') {
            $pieces = $this->repoAffairePiece->findBy(['etat' => 'Termine', 'affaire' => $affaire]);
        } elseif ($request->attributes->get('_route') == 'app_piece_affaire_nok') {
            $pieces = $this->repoAffairePiece->findNotFinish($affaire);
        }
        $form = $this->createForm(AffaireType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $affaire = $this->repoAffaires->findOneBy(['code' => $affaire]);
            $data = $form->getData();
            if ($data->getProgress()) {
                $affaire->setProgress($data->getProgress());
            }
            if ($data->getStart()) {
                $affaire->setStart(new DateTime($data->getStart()->format('Y-m-d H:i')));
            }
            if ($data->getEnd()) {
                $affaire->setEnd(new DateTime($data->getEnd()->format('Y-m-d H:i')));
            }
            if ($data->getDuration()) {
                $affaire->setDuration($data->getDuration());
            }
            if ($data->getbackgroundColor()) {
                $affaire->setbackgroundColor($data->getBackgroundColor());
            }
            if ($data->getTextColor()) {
                $affaire->setTextColor($data->getTextColor());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($affaire);
            $em->flush();
            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $affaire->getCode()]);
        }
        $table = 'affaire';
        $id = $this->repoAffaires->findOneBy(['code' => $affaire])->getId();
        $formFiles = $this->createForm(OthersDocumentsType::class);
        $formFiles->handleRequest($request);
        if ($formFiles->isSubmitted() && $formFiles->isValid()) {
            $files = $formFiles->get('file')->getData();
            // On boucle sur les images
            foreach ($files as $file) {
                // On génère un nouveau nom de fichier
                $d = new DateTime();
                $d = $d->format('Y-m-d H-i-s');
                $filename = $file->getClientOriginalName();
                $fichier = $filename . $d . '.' . $file->guessExtension();
                $search = $this->repoDocs->findOneBy(['identifiant' => $id, 'file' => $fichier]);
                if ($search == null) {
                    // On copie le fichier dans le dossier uploads
                    $file->move(
                        $this->getParameter('doc_lhermitte_affaires'),
                        $fichier
                    );
                    // On crée l'image dans la base de données
                    $doc = new OthersDocuments();
                    $doc->setFile($fichier);
                    $doc->setTables($table);
                    $doc->setCreatedAt(new DateTime);
                    $doc->setUser($this->getUser());
                    $doc->setIdentifiant($id);
                    $this->addFlash('message', 'Fichier ' . $filename . ' ajouté avec succés');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($doc);
                    $entityManager->flush();
                } else {
                    $this->addFlash('danger', 'Fichier ' . $filename . ' est déjà présent ! ce fichier n\'est pas sauvegardé !');
                }
            }
            return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $affaire]);
        }
        $docs = $this->repoDocs->findBy(['tables' => 'affaire', 'id' => $id]);
        //dd($docs);
        $affaire = $this->repoAffaires->findOneBy(['code' => $affaire]);
        return $this->render('affaires/pieceAffaire.html.twig', [
            // todo à voir pour filtrer l'état en fonction de la page affichée
            'piecesAffaires' => $pieces,
            'title' => 'piecesAffaires',
            'affaire' => $affaire,
            'form' => $form->createView(),
            'formFiles' => $formFiles->createView(),
            'docs' => $docs,
        ]);
    }
    /**
     * @Route("/Lhermitte/detail/piece/affaire/{{affaire}}/{{type}}/{{piece}}", name="app_detail_piece_affaire")
     */
    public function detailPieceAffaire($affaire, $type, $piece, Request $request): Response
    {

        $affaire = $this->repoAffaires->findOneBy(['code' => $affaire]);
        $p = $this->repoMouv->getDetailPiecesAffaires($type, $piece);
        $id = $this->repoAffairePiece->findOneBy(['affaire' => $affaire->getCode(), 'typePiece' => $type, 'piece' => $piece]);
        return $this->render('affaires/detailPieceAffaire.html.twig', [
            'pieces' => $p,
            'title' => 'Détail de la piece',
            'affaire' => $affaire,
            'id' => $id,
        ]);
    }

    /**
     * @Route("/Lhermitte/update/affaire", name="app_update_affaires")
     */
    public function update(): Response
    {
        $affaires = $this->repoMouv->getAffaires();
        foreach ($affaires as $value) {
            $affaire = $this->repoAffaires->findOneBy(['code' => $value['affaire']]);
            if (!$affaire) {
                $affaire = new Affaires;
                $affaire->setCode($value['affaire'])
                    ->setLibelle($value['libelle'])
                    ->setTiers($value['tiers'])
                    ->setNom($value['nom'])
                    ->setEtat('Nouvelle');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($affaire);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_update_piece_affaires');

    }

    /**
     * @Route("/Lhermitte/update/pieces/affaire", name="app_update_piece_affaires")
     */
    public function updatePieces(): Response
    {
        $affaires = $this->repoAffaires->findAll();
        foreach ($affaires as $affaire) {
            $pieces = $this->repoMouv->getPiecesAffaires($affaire->getCode());
            foreach ($pieces as $p) {
                $piece = $this->repoAffairePiece->findOneBy(['entId' => $p['id']]);

                if (!$piece) {
                    $piece = new AffairePiece;
                    $piece->setAffaire($p['affaire'])
                        ->setEntId($p['id'])
                        ->setAdresse($p['adresse'])
                        ->setTypePiece($p['typeP'])
                        ->setPiece($p['piece'])
                        ->setOp($p['op'])
                        ->setTransport($p['transport'])
                        ->setEtat('Nouvelle');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($piece);
                    $entityManager->flush();
                }
            }
        }
        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_affaire_me_nok');
    }

    /**
     * @Route("/Lhermitte/affaire/change/etat/{id}/{etat}",name="app_affaire_change_etat")
     */

    public function changeEtat($id, $etat): Response
    {

        $piece = $this->repoAffaires->findOneBy(['id' => $id]);
        $piece->setEtat($etat);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($piece);
        $entityManager->flush();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_affaire_me_nok');

    }

    /**
     * @Route("/Lhermitte/affaire/piece/change/etat/{id}/{etat}",name="app_affaire_piece_change_etat")
     */

    public function changeEtatPiece($id, $etat): Response
    {
        $piece = $this->repoAffairePiece->findOneBy(['id' => $id]);
        $piece->setEtat($etat);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($piece);
        $entityManager->flush();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $piece->getAffaire()]);

    }

}
