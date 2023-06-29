<?php

namespace App\Controller;

use App\Entity\Main\AffairePiece;
use App\Entity\Main\Affaires;
use App\Entity\Main\InterventionMonteurs;
use App\Entity\Main\OthersDocuments;
use App\Form\AffaireType;
use App\Form\ChatsType;
use App\Form\CreationChantierAffaireType;
use App\Form\InterventionsMonteursType;
use App\Form\OthersDocumentsMultiple2Type;
use App\Form\OthersDocumentsMultipleType;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\AffairePieceRepository;
use App\Repository\Main\AffairesRepository;
use App\Repository\Main\ChatsRepository;
use App\Repository\Main\CommentairesRepository;
use App\Repository\Main\InterventionMonteursRepository;
use App\Repository\Main\OthersDocumentsRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AffairesController extends AbstractController
{

    private $repoMouv;
    private $repoCli;
    private $repoAffaires;
    private $repoAffairePiece;
    private $repoComments;
    private $repoUsers;
    private $repoDocs;
    private $mailer;
    private $repoChats;
    private $repoIntervertionsMonteurs;

    public function __construct(InterventionMonteursRepository $repoIntervertionsMonteurs, CliRepository $repoCli, UsersRepository $repoUsers, ChatsRepository $repoChats, AffairePieceRepository $repoAffairePiece, OthersDocumentsRepository $repoDocs, MailerInterface $mailer, CommentairesRepository $repoComments, AffairesRepository $repoAffaires, MouvRepository $repoMouv)
    {
        $this->repoMouv = $repoMouv;
        $this->repoIntervertionsMonteurs = $repoIntervertionsMonteurs;
        $this->repoCli = $repoCli;
        $this->repoAffaires = $repoAffaires;
        $this->repoComments = $repoComments;
        $this->repoDocs = $repoDocs;
        $this->mailer = $mailer;
        $this->repoAffairePiece = $repoAffairePiece;
        $this->repoChats = $repoChats;
        $this->repoUsers = $repoUsers;
        //parent::__construct();
    }

    /**
     * @Route("/Lhermitte/affaire/me/ok", name="app_affaire_me_ok")
     * @Route("/Lhermitte/affaire/me/nok", name="app_affaire_me_nok")
     */
    public function affaire(Request $request): Response
    {

        // Calendrier des congés
        $events = $this->repoIntervertionsMonteurs->findAll();
        $interventionsActuels = $events;
        $rdvs = [];

        foreach ($events as $event) {
            $id = $event->getId();
            $code = $event->getCode()->getCode();
            $libelle = $event->getCode()->getLibelle();
            if ($event->getBackgroundColor()) {
                $color = $event->getBackgroundColor();
            }
            if ($event->getTextColor()) {
                $textColor = $event->getTextColor();
            }
            if ($event->getStart() && $event->getEnd()) {
                $start = $event->getStart()->format('Y-m-d H:i:s');
                $end = $event->getEnd()->format('Y-m-d H:i:s');
                if ($event->getStart()->format('Y-m-d') == $event->getEnd()->format('Y-m-d') && $event->getStart()->format('H:i') == '00:00' && $event->getEnd()->format('H:i') == '23:00') {
                    $start = $event->getStart()->format('Y-m-d');
                    $end = $event->getEnd()->format('Y-m-d');
                }
            }
            if ($event->getStart() && $event->getEnd()) {
                $rdvs[] = [
                    'id' => $event->getId(),
                    'start' => $start,
                    'end' => $end,
                    'url' => 'http://192.168.50.244/Lhermitte/affaire/show/intervention/' . $id,
                    'title' => 'Affaire : ' . $libelle . ' du ' . $event->getStart()->format('d-m-Y H:i') . ' au ' . $event->getEnd()->format('d-m-Y H:i'),
                    'backgroundColor' => $color,
                    'borderColor' => '#FFFFFF',
                    'textColor' => $textColor,
                ];
            }
        }

        // récupérer les fériers en JSON sur le site etalab
        $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
        // On ajoute les fériers au calendrier des congés
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($ferierJson, true)),
            RecursiveIteratorIterator::SELF_FIRST);
        foreach ($jsonIterator as $key => $val) {
            $rdvs[] = [
                'id' => '',
                'start' => $key,
                'end' => $key,
                'title' => $val,
                'backgroundColor' => '#404040',
                'borderColor' => '#FFFFFF',
                'textColor' => '#FFFFFF',
            ];
        }

        $data = json_encode($rdvs);

        $form = $this->createForm(CreationChantierAffaireType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $chantier = $this->repoAffaires->findOneBy(['code' => $data->getCode()]);
            if ($chantier) {
                $this->addFlash('danger', 'Ce code chantier est déjà utilisé, veuillez en choisir un autre');
                return $this->redirectToRoute('app_affaire_me_nok');
            } else {
                $cli = $this->repoCli->getThisCodeClient($data->getTiers());
                $chantier = new Affaires();
                $chantier->setCode($data->getCode() . 'Manuelle')
                    ->setLibelle($data->getLibelle())
                    ->setTiers($data->getTiers())
                    ->setNom($cli['nom']);
                $chantier->setStart(new DateTime())
                    ->setEtat('Nouvelle')
                    ->setDuration($data->getDuration())
                ;
                $em = $this->getDoctrine()->getManager();
                $em->persist($chantier);
                $em->flush();
                $pieceChantier = new AffairePiece();
                $pieceChantier->setEntId('999999')
                    ->setTypePiece('2')
                    ->setPiece('99999999')
                    ->setOp('C')
                    ->setEtat('Nouvelle')
                    ->setAffaire($data->getCode() . 'Manuelle')
                    ->setAdresse($cli['rue'] . ' ' . $cli['cp'] . ' ' . $cli['ville']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($pieceChantier);
                $em->flush();

                $this->addFlash('message', 'Votre chantier a été créé avec succés !');
                return $this->redirectToRoute('app_affaire_me_nok');
            }
        }
        if ($request->attributes->get('_route') == 'app_affaire_me_ok') {
            $affaires = $this->repoAffaires->findFinish();
        } elseif ($request->attributes->get('_route') == 'app_affaire_me_nok') {
            $affaires = $this->repoAffaires->findNotFinish();
        }
        return $this->render('affaires/affaire.html.twig', [
            'affaires' => $affaires,
            'title' => 'Affaires',
            'data' => $data,
            'interventionsActuels' => $interventionsActuels,
            'form' => $form->createView(),
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
            $pieces = $this->repoAffairePiece->findBy(['affaire' => $affaire, 'closedAt' => null]);
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
        $formFiles = $this->createForm(OthersDocumentsMultipleType::class);
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
        $docs = $this->repoDocs->findBy(['tables' => 'affaire', 'identifiant' => $id]);

        $affaire = $this->repoAffaires->findOneBy(['code' => $affaire]);

        $ChatsForm = $this->createForm(ChatsType::class);
        $ChatsForm->handleRequest($request);

        if ($ChatsForm->isSubmitted() && $ChatsForm->isValid()) {
            $chat = $ChatsForm->getData();
            $chat->setCreatedAt(new \DateTime())
                ->setUser($this->getUser())
                ->setContent($chat->getContent())
                ->setFonction('chatAffaire')
                ->setIdentifiant($affaire->getId());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chat);
            $entityManager->flush();
            $this->addFlash('message', 'Chat modifié avec succès');
            return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $affaire->getCode()]);

        }

        $intervention = new InterventionMonteurs();
        $InterventionsMonteursForm = $this->createForm(InterventionsMonteursType::class, $intervention);
        $InterventionsMonteursForm->handleRequest($request);

        if ($InterventionsMonteursForm->isSubmitted() && $InterventionsMonteursForm->isValid()) {
            $intervention->setCreatedAt(new DateTime())
                ->setUserCr($this->getUser())
                ->setCode($affaire);
            if ($InterventionsMonteursForm->getData()->getAdresse()) {
                $intervention->setAdresse($InterventionsMonteursForm->getData()->getAdresse());
            } else {
                if ($InterventionsMonteursForm->getData()->getPieces()) { // && $value->getPiece() != '99999999'
                    $adresses = [];
                    foreach ($InterventionsMonteursForm->getData()->getPieces() as $value) {
                        $adresses[] = $value->getAdresse();
                    }
                    $adresses = array_unique($adresses);
                    $adresse = '';
                    foreach ($adresses as $value) {
                        $adresse = $value . ' | ' . $adresse;
                    }
                }
                if (!$adresse) {
                    $adresse = 'Pas de donnée adresse';
                }
                $intervention->setAdresse($adresse);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($intervention);
            $entityManager->flush();

            $this->changeEtat($affaire->getId(), 'Planifiee');

        }

        $Interventions = $this->repoIntervertionsMonteurs->findBy(['code' => $affaire]);
        $chats = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $affaire->getId()], ['createdAt' => 'DESC']);
        $usersMes = $this->repoUsers->findby(['service' => 9]);

        return $this->render('affaires/pieceAffaire.html.twig', [
            // todo à voir pour filtrer l'état en fonction de la page affichée
            'piecesAffaires' => $pieces,
            'title' => 'piecesAffaires',
            'affaire' => $affaire,
            'form' => $form->createView(),
            'formFiles' => $formFiles->createView(),
            'docs' => $docs,
            'chats' => $chats,
            'ChatsForm' => $ChatsForm->createView(),
            'usersMes' => $usersMes,
            'InterventionsMonteursForm' => $InterventionsMonteursForm->createView(),
            'Interventions' => $Interventions,
        ]);
    }
    /**
     * @Route("/Lhermitte/detail/piece/affaire/{{affaire}}/{{id}}", name="app_detail_piece_affaire")
     */
    public function detailPieceAffaire($affaire, $id, Request $request): Response
    {

        $affaire = $this->repoAffaires->findOneBy(['code' => $affaire]);
        $produits = $this->repoMouv->getDetailPiecesAffaires($id);
        $piece = $this->repoAffairePiece->findOneBy(['affaire' => $affaire->getCode(), 'entId' => $id]);
        return $this->render('affaires/detailPieceAffaire.html.twig', [
            'produits' => $produits,
            'title' => 'Détail de la piece',
            'affaire' => $affaire,
            'piece' => $piece,
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
                    ->setStart(new Datetime($value['dateCreation']))
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
                } else {
                    $piece->setAffaire($p['affaire'])
                        ->setEntId($p['id'])
                        ->setAdresse($p['adresse'])
                        ->setTypePiece($p['typeP'])
                        ->setPiece($p['piece'])
                        ->setOp($p['op'])
                        ->setTransport($p['transport']);
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

        $affaire = $this->repoAffaires->findOneBy(['id' => $id]);
        $affaire->setEtat($etat);
        if ($etat == 'Termine') {
            $affaire->setEnd(new DateTime());
            $pieces = $this->repoAffairePiece->findBy(['affaire' => $affaire->getCode()]);
            foreach ($pieces as $value) {
                $this->changeEtatPiece($value->getId(), $etat);
            }
        } else {
            $affaire->setEnd(null);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($affaire);
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
        if ($etat == 'Termine') {
            $piece->setClosedAt(new DateTime());
        } else {
            $piece->setClosedAt(null);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($piece);
        $entityManager->flush();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $piece->getAffaire()]);

    }

    /**
     * @Route("/Lhermitte/affaire/remove/intervention/{id}",name="app_affaire_remove_intervention")
     */

    public function removeIntervention($id): Response
    {
        $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
        $affaire = $intervention->getCode()->getCode();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($intervention);
        $entityManager->flush();

        $this->addFlash('message', 'Intervention supprimée avec succés');
        return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $affaire]);

    }

    /**
     * @Route("/Lhermitte/affaire/edit/intervention/{id}/{affaire}",name="app_affaire_edit_intervention")
     */

    public function editIntervention($id, $affaire, Request $request): Response
    {

        $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
        $InterventionsMonteursForm = $this->createForm(InterventionsMonteursType::class, $intervention);
        $InterventionsMonteursForm->handleRequest($request);

        if ($InterventionsMonteursForm->isSubmitted() && $InterventionsMonteursForm->isValid()) {
            $intervention->setCreatedAt(new DateTime())
                ->setUserCr($this->getUser());
            if ($InterventionsMonteursForm->getData()->getAdresse()) {
                $intervention->setAdresse($InterventionsMonteursForm->getData()->getAdresse());
            } else {
                if ($InterventionsMonteursForm->getData()->getPieces()) { // && $value->getPiece() != '99999999'
                    $adresses = [];
                    foreach ($InterventionsMonteursForm->getData()->getPieces() as $value) {
                        $adresses[] = $value->getAdresse();
                    }
                    $adresses = array_unique($adresses);
                    $adresse = '';
                    foreach ($adresses as $value) {
                        $adresse = $value . ' | ' . $adresse;
                    }
                }
                if (!$adresse) {
                    $adresse = 'Pas de donnée adresse';
                }
                $intervention->setAdresse($adresse);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($intervention);
            $entityManager->flush();
            $this->addFlash('message', 'Intervention modifiée avec succés');
        }

        return $this->render('affaires/editIntervention.html.twig', [
            'InterventionsMonteursForm' => $InterventionsMonteursForm->createView(),
            'title' => "Modification d'intervention",
            'intervention' => $intervention,
        ]);

    }

    /**
     * @Route("/Lhermitte/affaire/show/intervention/{id}",name="app_affaire_show_intervention")
     */

    public function showIntervention($id, Request $request): Response
    {

        $produits = '';
        $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
        $ChatsForm = $this->createForm(ChatsType::class);
        $ChatsForm->handleRequest($request);
        $intervenants = ['Anthony_Martinage', 'Bruno_Debonne', 'Bruno_Lefebvre', 'Alexandre_Deschodt'];

        if ($ChatsForm->isSubmitted() && $ChatsForm->isValid()) {
            $chat = $ChatsForm->getData();
            $chat->setCreatedAt(new \DateTime())
                ->setUser($this->getUser())
                ->setContent($chat->getContent())
                ->setFonction('chatAffaire')
                ->setIdentifiant($intervention->getCode()->getId())
                ->setTables($intervention->getId());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chat);
            $entityManager->flush();
            $this->addFlash('message', 'Commentaire ajouté avec succès');
            return $this->redirectToRoute('app_affaire_show_intervention', ['id' => $id]);

        }
        $formFiles = $this->createForm(OthersDocumentsMultipleType::class);
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
                    $doc->setTables('affaire');
                    $doc->setParametre($intervention->getId());
                    $doc->setCreatedAt(new DateTime);
                    $doc->setUser($this->getUser());
                    $doc->setIdentifiant($intervention->getCode()->getId());
                    $this->addFlash('message', 'Fichier ' . $filename . ' ajouté avec succés');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($doc);
                    $entityManager->flush();
                } else {
                    $this->addFlash('danger', 'Fichier ' . $filename . ' est déjà présent ! ce fichier n\'est pas sauvegardé !');
                }
            }
            $this->addFlash('message', 'Fichier(s) ajouté(s) avec succès');
            return $this->redirectToRoute('app_affaire_show_intervention', ['id' => $id]);
        }

        // TODO A VOIR LA DECISIONS DE LUNDI !
        // REVOIR CETTE PARTI, IL NE VEUT PAS TRAITER CORRECTEMENT CETTE FORME CAR J'UTILISE DEJA CETTE CLASS SUR LA MEME PAGE
        $formDepotFeuilleIntervention = $this->createForm(OthersDocumentsMultiple2Type::class);
        $formDepotFeuilleIntervention->handleRequest($request);
        if ($formDepotFeuilleIntervention->isSubmitted() && $formDepotFeuilleIntervention->isValid()) {
            $files = $formDepotFeuilleIntervention->get('file')->getData();
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
                    $doc->setTables('app_affaire_show_intervention');
                    $doc->setCreatedAt(new DateTime);
                    $doc->setUser($this->getUser());
                    $doc->setIdentifiant($intervention->getCode()->getId());
                    $doc->setParametre($id);
                    $this->addFlash('message', 'Fichier ' . $filename . ' ajouté avec succés');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($doc);
                    $entityManager->flush();
                } else {
                    $this->addFlash('danger', 'Fichier ' . $filename . ' est déjà présent ! ce fichier n\'est pas sauvegardé !');
                }
            }
            return $this->redirectToRoute('app_affaire_show_intervention', ['id' => $id]);
        }
        $docs = $this->repoDocs->findBy(['tables' => 'affaire', 'identifiant' => $intervention->getCode()->getId()]);
        $FichesIntervs = $this->repoDocs->findBy(['tables' => 'app_affaire_show_intervention', 'identifiant' => $intervention->getCode()->getId()]);
        $chats = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $intervention->getCode()->getId()], ['createdAt' => 'DESC']);
        $usersMes = $this->repoUsers->findby(['service' => 9]);
        $pieces = $intervention->getPieces();
        $ids = '';
        $i = 0;
        foreach ($pieces as $piece) {
            if ($i == 0) {
                $ids = $piece->getEntId();
            } else {
                $ids = $ids . ',' . $piece->getEntId();
            }
            $i++;
        }
        if ($i > 0) {
            $produits = $this->repoMouv->getDetailPiecesAffaires($ids);
        }
        return $this->render('affaires/showIntervention.html.twig', [
            'title' => "Voir intervention",
            'intervention' => $intervention,
            'produits' => $produits,
            'usersMes' => $usersMes,
            'docs' => $docs,
            'formFiles' => $formFiles->createView(),
            'formDepotFeuilleIntervention' => $formDepotFeuilleIntervention->createView(),
            'chats' => $chats,
            'FichesIntervs' => $FichesIntervs,
            'ChatsForm' => $ChatsForm->createView(),
            'intervenants' => $intervenants,
        ]);
    }

}
