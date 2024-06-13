<?php

namespace App\Controller;

use App\Controller\AffairesAdminController;
use App\Entity\Main\AffairePiece;
use App\Entity\Main\Affaires;
use App\Entity\Main\Chats;
use App\Entity\Main\InterventionFicheMonteur;
use App\Entity\Main\InterventionFichesMonteursHeures;
use App\Entity\Main\InterventionMonteurs;
use App\Entity\Main\OthersDocuments;
use App\Form\AddPicturesOrDocsType;
use App\Form\ChatsType;
use App\Form\CommentairesType;
use App\Form\InterventionFicheCommentType;
use App\Form\InterventionFichesMonteursHeuresType;
use App\Form\InterventionFicheType;
use App\Form\InterventionsMonteursType;
use App\Form\OthersDocumentsMultipleType;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\AffairePieceRepository;
use App\Repository\Main\AffairesRepository;
use App\Repository\Main\ChatsRepository;
use App\Repository\Main\CommentairesRepository;
use App\Repository\Main\InterventionFicheMonteurRepository;
use App\Repository\Main\InterventionFichesMonteursHeuresRepository;
use App\Repository\Main\InterventionMonteursRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\OthersDocumentsRepository;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use App\Repository\Main\StatutsGenerauxRepository;
use App\Repository\Main\UsersRepository;
use App\Service\BlogFormaterService;
use App\Service\DateCheckerIsWorkingDayService;
use App\Service\EmailTreatementService;
use App\Service\ImageService;
use App\Service\ProductFormService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\Main\Commentaires;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ME")]

class AffairesController extends AbstractController
{

    private $repoMouv;
    private $repoArt;
    private $repoAffaires;
    private $repoAffairePiece;
    private $repoUsers;
    private $repoCli;
    private $repoEnt;
    private $repoMail;
    private $mailEnvoi;
    private $repoDocs;
    private $repoComments;
    private $productFormService;
    private $repoChats;
    private $repoIntervertionsMonteurs;
    private $repoInterventionFicheMonteur;
    private $repoInterventionFichesMonteursHeures;
    private $affaireAdminController;
    private $repoFiche;
    private $mailer;
    private $mailTreatement;
    private $repoRetrait;
    private $emailTreatementService;
    private $blobFormater;
    private $entityManager;
    private $repoStatusGeneraux;
    private $isWorkingDay;
    private $urlGenerator;

    public function __construct(
        ManagerRegistry $registry,
        EmailTreatementService $emailTreatementService,
        ArtRepository $repoArt,
        RetraitMarchandisesEanRepository $repoRetrait,
        InterventionFicheMonteurRepository $repoFiche,
        AffairesAdminController $affaireAdminController,
        InterventionFichesMonteursHeuresRepository $repoInterventionFichesMonteursHeures,
        InterventionFicheMonteurRepository $repoInterventionFicheMonteur,
        InterventionMonteursRepository $repoIntervertionsMonteurs,
        StatutsGenerauxRepository $repoStatusGeneraux,
        ProductFormService $productFormService,
        CliRepository $repoCli,
        UsersRepository $repoUsers,
        ChatsRepository $repoChats,
        AffairePieceRepository $repoAffairePiece,
        MailListRepository $repoMail,
        OthersDocumentsRepository $repoDocs,
        BlogFormaterService $blobFormater,
        EntRepository $repoEnt,
        MailerInterface $mailer,
        CommentairesRepository $repoComments,
        AffairesRepository $repoAffaires,
        MouvRepository $repoMouv,
        DateCheckerIsWorkingDayService $isWorkingDay,
        UrlGeneratorInterface $urlGenerator,
    ) {
        $this->repoMouv = $repoMouv;
        $this->repoIntervertionsMonteurs = $repoIntervertionsMonteurs;
        $this->repoCli = $repoCli;
        $this->repoAffaires = $repoAffaires;
        $this->repoComments = $repoComments;
        $this->repoDocs = $repoDocs;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->isWorkingDay = $isWorkingDay;
        $this->urlGenerator = $urlGenerator;
        $this->repoEnt = $repoEnt;
        $this->productFormService = $productFormService;
        $this->mailer = $mailer;
        $this->blobFormater = $blobFormater;
        $this->repoAffairePiece = $repoAffairePiece;
        $this->repoStatusGeneraux = $repoStatusGeneraux;
        $this->repoChats = $repoChats;
        $this->repoUsers = $repoUsers;
        $this->repoInterventionFichesMonteursHeures = $repoInterventionFichesMonteursHeures;
        $this->repoInterventionFicheMonteur = $repoInterventionFicheMonteur;
        $this->affaireAdminController = $affaireAdminController;
        $this->repoFiche = $repoFiche;
        $this->repoRetrait = $repoRetrait;
        $this->repoArt = $repoArt;
        $this->emailTreatementService = $emailTreatementService;
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/Lhermitte/affaire/me/ok", name: "app_affaire_me_ok")]
    #[Route("/Lhermitte/affaire/me/nok", name: "app_affaire_me_nok")]

    public function affaire(UrlGeneratorInterface $urlGenerator, Request $request): Response
    {
        $interventionsActuels = $this->repoIntervertionsMonteurs->findInterventionDuMoment();
        if ($request->attributes->get('_route') == 'app_affaire_me_ok') {
            $affaires = $this->repoAffaires->findFinish();
        } elseif ($request->attributes->get('_route') == 'app_affaire_me_nok') {
            $affaires = $this->repoAffaires->findNotFinish();
        }
        $fichesManquantes = $this->affaireAdminController->recupFichesManquantes();
        $fichesNonVerrouillees = $this->repoFiche->findBy(['lockedAt' => null]);

        return $this->render('affaires/affaire.html.twig', [
            'affaires' => $affaires,
            'title' => 'Affaires',
            'data' => $this->interventionsCalendar(),
            'interventionsActuels' => $interventionsActuels,
            'fichesManquantes' => $fichesManquantes,
            'fichesNonVerrouillees' => $fichesNonVerrouillees,
            'cmdsWithProblem' => $this->repoEnt->getRowsWithoutAffair(),
            'blsWithProblem' => $this->repoEnt->getRowsWithoutAffaireBl(),
        ]);
    }

    public function interventionsCalendar()
    {
        // Calendrier des congés
        $events = $this->repoIntervertionsMonteurs->findAll();
        $rdvs = [];
        foreach ($events as $event) {

            $monteurs = '';
            if ($event->getEquipes()) {
                foreach ($event->getEquipes() as $monteur) {
                    $monteurs .= $monteur->getPseudo() . ', ';
                }
            }
            // Supprimer la virgule en trop à la fin
            $monteurs = rtrim($monteurs, ', ');

            $id = $event->getId();
            $libelle = $event->getCode()->getLibelle();
            $color = $event->getTypeIntervention()->getBackgroundColor();
            $textColor = $event->getTypeIntervention()->getTextColor();
            $end = clone $event->getEnd();
            $allDay = false;
            if ($event->isAllDay() == 1) {
                //$end->modify('+1 day');
                $allDay = true;
            }
            if ($event->getStart() && $event->getEnd()) {
                $start = $event->getStart()->format('Y-m-d H:i');
                $end = $event->getEnd()->format('Y-m-d H:i');
            }
            if ($this->isGranted('ROLE_ADMIN_MONTEUR')) {
                $editable = true;
                $startEditable = true;
                $url = $this->urlGenerator->generate('app_piece_affaire_nok', ['affaire' => $event->getCode()->getCode()]);
            } else {
                $editable = false;
                $startEditable = false;
                $url = $this->urlGenerator->generate('app_affaire_show_intervention', ['id' => $id]);
            }
            $maxCreatedAt = null;
            $minEndDate = null;
            if (count($event->getInterventionFicheMonteurs()) > 0) {
                // Initialiser la date de création maximale à null
                // Parcourir les fiches d'intervention pour trouver la date de création maximale
                foreach ($event->getInterventionFicheMonteurs() as $ficheIntervention) {
                    // Récupérer la date de création de la fiche d'intervention
                    $createdAt = $ficheIntervention->getCreatedAt();

                    // Comparer avec la date de création maximale actuelle
                    if ($maxCreatedAt === null || $createdAt > $maxCreatedAt) {
                        $maxCreatedAt = $createdAt;
                    }
                }
                $minEndDate = $maxCreatedAt;
                if ($maxCreatedAt >= new DateTime()) {
                    $editable = false;
                }
                $startEditable = false;
            }

            if ($event->getStart() && $event->getEnd()) {
                $rdvs[] = [
                    'id' => $event->getId(),
                    'start' => $start,
                    'end' => $end,
                    'url' => $url,
                    'title' => $libelle . ' du ' . $event->getStart()->format('d-m-y H:i') . ' au ' . $event->getEnd()->format('d-m-y H:i') . ' - ' . $monteurs,
                    'icon' => $event->getTypeIntervention()->getFaIconsClass(),
                    'backgroundColor' => $color,
                    'borderColor' => '#FFFFFF',
                    'allDay' => $allDay,
                    'textColor' => $textColor,
                    'editable' => $editable,
                    'display' => 'block',
                    'startEditable' => $startEditable,
                    'tooltips' => 'Client : ' . $event->getCode()->getNom() . ' Intervenants : ' . $monteurs,
                    'minEndDate' => $minEndDate, // Ajout de la date de fin minimale
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
                'backgroundColor' => '#6c757d',
                'borderColor' => '#FFFFFF',
                'textColor' => '#FFFFFF',
                'editable' => false,
            ];
        }

        $data = json_encode($rdvs);

        return $data;
    }

    #[Route("/Lhermitte/pieces/affaire/nok/{{affaire}}", name: "app_piece_affaire_nok")]
    #[Route("/Lhermitte/pieces/affaire/ok/{{affaire}}", name: "app_piece_affaire_ok")]

    public function pieceAffaire($affaire, Request $request, CommentairesRepository $repoCommentaire): Response
    {
        if ($request->attributes->get('_route') == 'app_piece_affaire_ok') {
            $pieces = $this->repoAffairePiece->findBy(['etat' => 'Termine', 'affaire' => $affaire]);
        } elseif ($request->attributes->get('_route') == 'app_piece_affaire_nok') {
            $pieces = $this->repoAffairePiece->findBy(['affaire' => $affaire, 'closedAt' => null]);
        }

        // ramener le planning des monteurs

        $pieces = $this->mettreProduitsSurPieces($pieces);
        $table = 'affaire';
        $id = $this->repoAffaires->findOneBy(['code' => $affaire])->getId();

        $affaire = $this->repoAffaires->findOneBy(['code' => $affaire]);

        $commentaire = $repoCommentaire->findOneBy(['Tables' => 'app_piece_affaire', 'identifiant' => $affaire->getId()]);

        if (!$commentaire) {
            $commentaire = new Commentaires();
        }

        $formComment = $this->createForm(CommentairesType::class, $commentaire);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment->isValid()) {

            $dd = $formComment->get('content')->getData();

            $commentaire->setCreatedAt(new DateTime())
                ->setUser($this->getUser())
                ->setTables('app_piece_affaire')
                ->setIdentifiant($affaire->getId());
            $entityManager = $this->entityManager;
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute($request->attributes->get('_route'), ['affaire' => $affaire->getCode()]);
        } else {
            $entityManager = $this->entityManager;
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        $intervention = new InterventionMonteurs();
        $InterventionsMonteursForm = $this->createForm(InterventionsMonteursType::class, $intervention);
        $InterventionsMonteursForm->handleRequest($request);

        if ($InterventionsMonteursForm->isSubmitted() && $InterventionsMonteursForm->isValid()) {

            $intervention->setCreatedAt(new DateTime())
                ->setUserCr($this->getUser())
                ->setCode($affaire);
            if (!$InterventionsMonteursForm->getData()->getTypeIntervention()) {
                $intervention->setTypeIntervention($this->repoStatusGeneraux->findOneBy(['id' => 7]));
            }
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
                $intervention->setAllDay(true);
            }
            $entityManager = $this->entityManager;
            $entityManager->persist($intervention);
            $entityManager->flush();

            // Ajouter les fichiers joints
            $files = $InterventionsMonteursForm->get('files')->getData();
            // On boucle sur les images
            if ($files) {
                foreach ($files as $file) {
                    // On génère un nouveau nom de fichier
                    $d = new DateTime();
                    $d = $d->format('Y-m-d H-i-s');
                    $filename = $file->getClientOriginalName();
                    $fichier = $filename . $d . '.' . $file->guessExtension();

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
                    $entityManager = $this->entityManager;
                    $entityManager->persist($doc);
                    $entityManager->flush();
                }
            }

            if ($InterventionsMonteursForm->get('comment')->getData()) {
                $chat = new Chats;
                $chat->setCreatedAt(new \DateTime())
                    ->setUser($this->getUser())
                    ->setContent($InterventionsMonteursForm->get('comment')->getData())
                    ->setFonction('chatAffaire')
                    ->setIdentifiant($intervention->getCode()->getId())
                    ->setTables($intervention->getId());
                $entityManager = $this->entityManager;
                $entityManager->persist($chat);
                $entityManager->flush();

            }

            $this->changeEtat($affaire->getId(), 'Planifiee');

            $this->addFlash('message', 'Création d\'intervention effectuée avec succés');
            return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $affaire->getCode()]);

        }
        $Interventions = $this->repoIntervertionsMonteurs->findBy(['code' => $affaire]);
        $chats = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $affaire->getId()], ['createdAt' => 'DESC']);
        $docs = $this->repoDocs->findBy(['tables' => 'affaire', 'identifiant' => $id]);
        $retraits = $this->repoRetrait->findBy(['chantier' => $affaire->getCode()]);
        foreach ($retraits as $retrait) {
            $r = $this->repoArt->getEanStock(1, $retrait->getEan());

            // Définir les données récupérées comme propriétés supplémentaires sur l'objet $retrait
            // Ce code fontionne, c'est VisualStudio qui ne comprends pas.
            $retrait->ref = $r['ref'];
            $retrait->sref1 = $r['sref1'];
            $retrait->sref2 = $r['sref2'];
            $retrait->designation = $r['designation'];
            $retrait->uv = $r['uv'];
            $retrait->stock = $r['stock'];
        }

        $types = $this->repoStatusGeneraux->findBy(['entity' => 'interventionMonteurs', 'pilotage' => 'type']);

        return $this->render('affaires/pieceAffaire.html.twig', [
            // todo à voir pour filtrer l'état en fonction de la page affichée
            'piecesAffaires' => $pieces,
            'title' => 'piecesAffaires',
            'affaire' => $affaire,
            'docs' => $docs,
            'types' => $types,
            'data' => $this->interventionsCalendar(),
            'chats' => $chats,
            'retraits' => $retraits,
            'formComment' => $formComment->createView(),
            'InterventionsMonteursForm' => $InterventionsMonteursForm->createView(),
            'Interventions' => $Interventions,
        ]);
    }

    #[Route("/Lhermitte/update/affaire", name: "app_update_affaires")]

    public function updateAffaire(): Response
    {
        $affaires = $this->repoMouv->getAffaires();
        foreach ($affaires as $value) {
            $affaire = $this->repoAffaires->findOneBy(['code' => $value['affaire']]);
            $new = false;
            if (!$affaire) {
                $affaire = new Affaires;
                $new = true;
                $affaire->setCode($value['affaire'])
                    ->setDuration($value['duration'])
                    ->setLibelle($value['libelle'])
                    ->setTiers($value['tiers'])
                    ->setNom($value['nom'])
                    ->setStart(new Datetime($value['dateCreation']))
                    ->setEtat('Nouvelle');
            }

            $affaire->setDuration($value['duration'])
                ->setLibelle($value['libelle'])
                ->setNom($value['nom']);
            $entityManager = $this->entityManager;
            $entityManager->persist($affaire);
            $entityManager->flush();
            if ($new == true) {
                $subjet = "Une nouvelle affaire a été créé => " . $affaire->getLibelle();
                $donnees = $affaire;
                $pageUrl = "affaires/mails/mailNewAffaire.html.twig";
                $mails = $this->emailTreatementService->treatementMails('app_affaires_admin', null, null);
                $urlFiles = [];
                $this->emailTreatementService->sendMail($subjet, $mails, $donnees, $pageUrl, $urlFiles);
            }
        }

        return $this->redirectToRoute('app_update_piece_affaires');

    }

    #[Route("/Lhermitte/update/pieces/affaire", name: "app_update_piece_affaires")]

    public function updatePiecesAffaires(): Response
    {
        $affaires = $this->repoAffaires->findAll();
        foreach ($affaires as $affaire) {
            $pieces = $this->repoMouv->getPiecesAffaires($affaire->getCode());
            foreach ($pieces as $p) {
                $piece = $this->repoAffairePiece->findOneBy(['cdno' => $p['cdno']]);
                $ent = $this->repoMouv->getEntetePiecesAffaires($p['cdno'], 2);
                if ($p['blno'] != 0 || $p['cdno'] == 0) {
                    $piece = $this->repoAffairePiece->findOneBy(['blno' => $p['blno']]);
                    if ($p['blno'] != 0) {
                        $ent = $this->repoMouv->getEntetePiecesAffaires($p['blno'], 3);
                    }
                }
                if (!$piece) {
                    $piece = new AffairePiece;

                    $piece->setAffaire($p['affaire'])
                        ->setCdno($p['cdno'])
                        ->setBlno($p['blno'])
                        ->setAdresse($ent['adresse'])
                        ->setOp($ent['op'])
                        ->setTransport($ent['transport'])
                        ->setEtat('Nouvelle');
                    $entityManager = $this->entityManager;
                    $entityManager->persist($piece);
                    $entityManager->flush();

                    if ($affaire->getEnd()) {
                        $affaire->setEnd(null)
                            ->setEtat('A finir');
                        $entityManager = $this->entityManager;
                        $entityManager->persist($affaire);
                        $entityManager->flush();
                    }

                } else {
                    $piece->setAffaire($p['affaire'])
                        ->setAdresse($ent['adresse'])
                        ->setCdno($p['cdno'])
                        ->setBlno($p['blno'])
                        ->setOp($ent['op'])
                        ->setTransport($ent['transport']);
                    $entityManager = $this->entityManager;
                    $entityManager->persist($piece);
                    $entityManager->flush();
                }
            }
        }
        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_affaire_me_nok');
    }

    public function changeEtat($id, $etat)
    {

        $affaire = $this->repoAffaires->findOneBy(['id' => $id]);

        if ($etat == 'Termine') {
            $affaire->setEnd(new DateTime());
            $pieces = $this->repoAffairePiece->findBy(['affaire' => $affaire->getCode()]);
            foreach ($pieces as $value) {
                $this->changeEtatPieceFunction($value->getId(), $etat);
            }
        } else {
            if ($affaire->getEtat() == 'Termine' && $etat != 'Termine') {
                $pieces = $this->repoAffairePiece->findBy(['affaire' => $affaire->getCode()]);

                foreach ($pieces as $value) {
                    $this->changeEtatPieceFunction($value->getId(), $etat);
                }
            }
            $affaire->setEnd(null);
        }
        $affaire->setEtat($etat);
        $entityManager = $this->entityManager;
        $entityManager->persist($affaire);
        $entityManager->flush();

    }

    #[Route("/Lhermitte/affaire/change/etat/{id}/{etat}", name: "app_affaire_change_etat")]

    public function changeEtatWithMessage($id, $etat): Response
    {

        $this->changeEtat($id, $etat);

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_affaire_me_nok');

    }

    #[Route("/Lhermitte/affaire/piece/change/etat/{id}/{etat}", name: "app_affaire_piece_change_etat")]

    public function changeEtatPiece($id, $etat): Response
    {
        $piece = $this->repoAffairePiece->findOneBy(['id' => $id]);
        $piece->setEtat($etat);
        if ($etat == 'Termine') {
            $piece->setClosedAt(new DateTime());
        } else {
            $piece->setClosedAt(null);
        }
        $entityManager = $this->entityManager;
        $entityManager->persist($piece);
        $entityManager->flush();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $piece->getAffaire()]);

    }

    public function changeEtatPieceFunction($id, $etat)
    {
        $piece = $this->repoAffairePiece->findOneBy(['id' => $id]);
        if ($etat == 'Termine') {
            $piece->setClosedAt(new DateTime());
        } else {
            $piece->setClosedAt(null);
        }
        $piece->setEtat($etat);
        $entityManager = $this->entityManager;
        $entityManager->persist($piece);
        $entityManager->flush();

    }

    #[Route("/Lhermitte/affaire/remove/intervention/{id}", name: "app_affaire_remove_intervention")]

    public function removeIntervention($id): Response
    {
        $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
        // supprimer les liaisons avec les piéces
        $pieces = $intervention->getPieces();
        foreach ($pieces as $value) {
            $intervention->removePiece($value);
        }
        // suppression des fichiers de cette intervention
        $fichiers = $this->repoDocs->findBy(['tables' => 'affaire', 'Parametre' => $id, 'identifiant' => $intervention->getCode()->getId()]);
        if ($fichiers) {
            foreach ($fichiers as $fichier) {
                $chemin = $this->getParameter('doc_lhermitte_affaires') . '/' . $fichier->getFile();
                $entityManager = $this->entityManager;
                $entityManager->remove($fichier);
                $entityManager->flush();
                unset($chemin);
            }
        }

        // suppression des commentaires de cette intervention
        $commentaires = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $intervention->getCode()->getId(), 'tables' => $id]);
        if ($commentaires) {
            foreach ($commentaires as $commentaire) {
                $entityManager = $this->entityManager;
                $entityManager->remove($commentaire);
                $entityManager->flush();
            }
        }
        $affaire = $intervention->getCode()->getCode();
        $entityManager = $this->entityManager;
        $entityManager->remove($intervention);
        $entityManager->flush();

        $this->addFlash('message', 'Intervention supprimée avec succés');
        return $this->redirectToRoute('app_piece_affaire_nok', ['affaire' => $affaire]);

    }

    public function removeInterventionAction($id)
    {

        try {
            $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
            // supprimer les liaisons avec les piéces
            $pieces = $intervention->getPieces();
            if ($pieces) {
                foreach ($pieces as $value) {
                    $intervention->removePiece($value);
                }
            }
            // suppression des fichiers de cette intervention
            $fichiers = $this->repoDocs->findBy(['tables' => 'affaire', 'Parametre' => $id, 'identifiant' => $intervention->getCode()->getId()]);
            if ($fichiers) {
                foreach ($fichiers as $fichier) {
                    $chemin = $this->getParameter('doc_lhermitte_affaires') . '/' . $fichier->getFile();
                    $this->entityManager->remove($fichier);
                    unset($chemin);
                }
            }

            // suppression des commentaires de cette intervention
            $commentaires = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $intervention->getCode()->getId(), 'tables' => $id]);
            if ($commentaires) {
                foreach ($commentaires as $commentaire) {
                    $this->entityManager->remove($commentaire);
                }
            }
            $this->entityManager->remove($intervention);
            $this->entityManager->flush();
        } catch (\Throwable $th) {
            dd($th);
        }

    }

    #[Route("/Lhermitte/affaire/edit/intervention/{id}/{affaire}", name: "app_affaire_edit_intervention")]

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
            $entityManager = $this->entityManager;
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

    #[Route("/Lhermitte/affaire/drag/and/drop/intervention/{id}/{start}/{end}", name: "app_affaire_edit_drag_and_drop_intervention")]

    public function dragAndDropIntervention($id, $start, $end): Response
    {
        try {
            $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
            $start = new DateTime($start);
            $end = new DateTime($end);
            $startClone = clone $start;
            $endClone = clone $end;

            $startHour = $startClone->format('H:i:s');
            $endHour = $endClone->format('H:i:s');

            $isAllDay = ($startHour === '00:00:00' && $endHour === '00:00:00');

            $intervention->setStart($start)
                ->setEnd($end)
                ->setAllDay($isAllDay);
            $this->entityManager->persist($intervention);
            $this->entityManager->flush();
            $intervenants = '';
            foreach ($intervention->getEquipes() as $intervenant) {
                $intervenants = $intervenant->getPseudo() . ', ';
            }
            $intervenants = rtrim($intervenants, ', ');
            $message = $intervention->getCode()->getLibelle() . ' du ' . $intervention->getStart()->format('d-m-y H:i') . ' au ' . $intervention->getEnd()->format('d-m-y H:i') . ' - ' . $intervenants;
            return new JsonResponse(['message' => $message]);
        } catch (\Exception $e) {
            // Gérer les erreurs
            return new JsonResponse(['message' => 'Erreur lors de la mise à jour de l\'intervention : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createInterventionRow($nom, $libelle, $oldStart, $oldEnd, $start, $end, $commentaire)
    {
        return [
            'nom' => $nom,
            'libelle' => $libelle,
            'oldStart' => $oldStart->format('d/m/Y H:i'),
            'oldEnd' => $oldEnd->format('d/m/Y H:i'),
            'start' => $start->format('d/m/Y H:i'),
            'end' => $end->format('d/m/Y H:i'),
            'commentaire' => $commentaire,
        ];
    }

    #[Route("/Lhermitte/affaire/show/intervention/{id}", name: "app_affaire_show_intervention")]

    public function showIntervention(ImageService $imageService, $id, Request $request): Response
    {

        $produits = '';
        $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
        $ChatsForm = $this->createForm(ChatsType::class);
        $ChatsForm->handleRequest($request);

        if ($ChatsForm->isSubmitted() && $ChatsForm->isValid()) {
            $chat = $ChatsForm->getData();
            $chat->setCreatedAt(new \DateTime())
                ->setUser($this->getUser())
                ->setContent($chat->getContent())
                ->setFonction('chatAffaire')
                ->setIdentifiant($intervention->getCode()->getId())
                ->setTables($intervention->getId());
            $entityManager = $this->entityManager;
            $entityManager->persist($chat);
            $entityManager->flush();

            $subjet = "Un commentaire à été déposé => " . $intervention->getCode()->getLibelle();
            $donnees = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $intervention->getCode()->getId(), 'tables' => $intervention->getId()], ['createdAt' => 'DESC']);
            $emails = null;
            $equipes = $intervention->getEquipes();
            if ($equipes) {
                $equipesArray = $equipes->toArray(); // Convertir la collection en un tableau
                $emails = array_map(function ($equipe) {
                    return $equipe->getEmail();
                }, $equipesArray);
            }
            $pageUrl = "affaires/mails/mailNewCommentaire.html.twig";
            $mails = $this->emailTreatementService->treatementMails('app_affaires_admin', null, $emails);
            $urlFiles = [];
            // envoie du mail
            $this->emailTreatementService->sendMail($subjet, $mails, $donnees, $pageUrl, $urlFiles);

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
                    $entityManager = $this->entityManager;
                    $entityManager->persist($doc);
                    $entityManager->flush();
                } else {
                    $this->addFlash('danger', 'Fichier ' . $filename . ' est déjà présent ! ce fichier n\'est pas sauvegardé !');
                }
            }
            $this->addFlash('message', 'Fichier(s) ajouté(s) avec succès');
            return $this->redirectToRoute('app_affaire_show_intervention', ['id' => $id]);
        }

        $docs = $this->repoDocs->findBy(['tables' => 'affaire', 'identifiant' => $intervention->getCode()->getId()]);
        $chats = $this->repoChats->findBy(['fonction' => 'chatAffaire', 'identifiant' => $intervention->getCode()->getId()], ['createdAt' => 'DESC']);
        $usersMes = $this->repoUsers->findby(['service' => 9]);
        $pieces = $intervention->getPieces();
        $ids = '';
        $i = 0;
        foreach ($pieces as $piece) {
            if ($i == 0) {
                $ids = $piece->getId();
            } else {
                $ids = $ids . ',' . $piece->getId();
            }
            $i++;
        }
        if ($i > 0) {
            if ($piece->getBlno() > 0) {
                $pino = $piece->getBlno();
                $picod = 3;
            } else {
                $pino = $piece->getCdno();
                $picod = 2;
            }
            $produits = $this->repoMouv->getDetailPiecesAffaires($pino, $picod);
            foreach ($produits as &$produit) {
                $produit['note'] = $this->blobFormater->getFormate($produit['note']);
            }
        }
        $retraits = $this->repoRetrait->findBy(['chantier' => $intervention->getCode()->getCode()]);
        foreach ($retraits as $retrait) {
            $r = $this->repoArt->getEanStock(1, $retrait->getEan());

            // Définir les données récupérées comme propriétés supplémentaires sur l'objet $retrait
            // Ce code fontionne, c'est VisualStudio qui ne comprends pas.
            $retrait->ref = $r['ref'];
            $retrait->sref1 = $r['sref1'];
            $retrait->sref2 = $r['sref2'];
            $retrait->designation = $r['designation'];
            $retrait->uv = $r['uv'];
            $retrait->stock = $r['stock'];
        }

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
                        $processedImage = $imageService->compressAndResize($file, 1000000);
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

        return $this->render('affaires/showIntervention.html.twig', [
            'title' => "Voir intervention",
            'intervention' => $intervention,
            'produits' => $produits,
            'usersMes' => $usersMes,
            'docs' => $docs,
            'formFiles' => $formFiles->createView(),
            'formAddPicturesOrDocs' => $formAddPicturesOrDocs->createView(),
            'productFormScript' => $this->productFormService->getProductFormScript(),
            'chats' => $chats,
            'retraits' => $retraits,
            'ChatsForm' => $ChatsForm->createView(),
        ]);
    }

    #[Route("/Lhermitte/affaire/creer/fiche/intervention/{id}/{intervenant}/{createdAt}", name: "app_affaire_creer_fiche_intervention")]

    public function creerFicheIntervention($id, $intervenant, $createdAt, Request $request)
    {
        $fiche = new InterventionFicheMonteur;
        $fiche->setCreatedAt(new DateTime($createdAt))
            ->setCreatedBy($this->getUser())
            ->setHere(true)
            ->setIntervenant($this->repoUsers->findOneBy(['id' => $intervenant]))
            ->setIntervention($this->repoIntervertionsMonteurs->findOneBy(['id' => $id]));
        $entityManager = $this->entityManager;
        $entityManager->persist($fiche);
        $entityManager->flush();

        $this->addFlash('message', 'Fiche créé avec succès');
        return $this->redirectToRoute('app_affaire_edit_fiche_intervention', ['id' => $id, 'ficheId' => $fiche->getId()]);
    }

    #[Route("/Lhermitte/affaire/saisie/fiche/intervention/{id}", name: "app_affaire_saisie_fiche_intervention")]
    #[Route("/Lhermitte/affaire/edit/fiche/intervention/{id}/{ficheId}", name: "app_affaire_edit_fiche_intervention")]

    public function saisieFicheIntervention($id, Request $request, $ficheId = null): Response
    {

        // Si la fiche existe déjà, on la modifie et on la charge
        if ($request->attributes->get('_route') == 'app_affaire_saisie_fiche_intervention') {
            $searchFiche = $this->repoInterventionFicheMonteur->findOneBy([
                'createdAt' => new DateTime($request->request->get('intervention_fiche')['createdAt']),
                'intervenant' => $request->request->get('intervention_fiche')['intervenant'],
                'intervention' => $id,
            ]);
            if ($searchFiche) {
                $this->addFlash('warning', 'Une fiche existe déjà pour cette intervention, cet intervenant et cette date, elle a été chargée ...');
                return $this->redirectToRoute('app_affaire_edit_fiche_intervention', ['id' => $id, 'ficheId' => $searchFiche->getId()]);
            } else {
                $fiche = new InterventionFicheMonteur;
            }
        } elseif ($request->attributes->get('_route') == 'app_affaire_edit_fiche_intervention') {
            $fiche = $this->repoInterventionFicheMonteur->findOneBy(['id' => $ficheId]);
        }
        $formFicheIntervention = $this->createForm(InterventionFicheType::class, $fiche);
        $formFicheIntervention->handleRequest($request);
        if ($formFicheIntervention->isSubmitted() && $formFicheIntervention->isValid()) {
            if ($request->attributes->get('_route') == 'app_affaire_saisie_fiche_intervention') {
                $fiche->setIntervention($this->repoIntervertionsMonteurs->findOneBy(['id' => $id]));
                $fiche->setCreatedBy($this->getUser());
            } elseif ($request->attributes->get('_route') == 'app_affaire_edit_fiche_intervention') {
                //$fiche->setCreatedAt(new DateTime($fiche['createdAt']));
                $fiche->setCreatedBy($this->getUser());
            }
            $entityManager = $this->entityManager;
            $entityManager->persist($fiche);
            $entityManager->flush();
            if ($request->attributes->get('_route') == 'app_affaire_saisie_fiche_intervention') {
                $this->addFlash('message', 'Fiche créé avec succès');
            } elseif ($request->attributes->get('_route') == 'app_affaire_edit_fiche_intervention') {
                $this->addFlash('message', 'Fiche modifiée avec succès');
            }
            return $this->redirectToRoute('app_affaire_edit_fiche_intervention', ['id' => $id, 'ficheId' => $fiche->getId()]);
        }
        $heure = new InterventionFichesMonteursHeures;
        $formHeureIntervention = $this->createForm(InterventionFichesMonteursHeuresType::class, $heure);
        $formHeureIntervention->handleRequest($request);
        if ($formHeureIntervention->isSubmitted() && $formHeureIntervention->isValid()) {
            $heure->setCreatedAt(new DateTime())
                ->setCreatedBy($this->getUser())
                ->setInterventionFicheMonteur($fiche);
            $entityManager = $this->entityManager;
            $entityManager->persist($heure);
            $entityManager->flush();
            $this->addFlash('message', 'Heures déposée avec succès');
            return $this->redirectToRoute('app_affaire_edit_fiche_intervention', ['id' => $id, 'ficheId' => $fiche->getId()]);
        }

        $formCommentaire = $this->createForm(InterventionFicheCommentType::class, $fiche);
        $formCommentaire->handleRequest($request);
        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            $entityManager = $this->entityManager;
            $entityManager->persist($fiche);
            $entityManager->flush();

            $this->addFlash('message', 'Commentaire déposé avec succès');
            return $this->redirectToRoute('app_affaire_edit_fiche_intervention', ['id' => $id, 'ficheId' => $fiche->getId()]);
        }
        //$fiches = $this->repoInterventionFicheMonteur->findAll();
        $fiche = $this->repoInterventionFicheMonteur->findOneBy(['id' => $ficheId]);
        $intervention = $this->repoIntervertionsMonteurs->findOneBy(['id' => $id]);
        $heures = $this->repoInterventionFichesMonteursHeures->findBy(['interventionFicheMonteur' => $ficheId]);
        return $this->render('affaires/saisieFicheIntervention.html.twig', [
            'title' => "Fiche d'intervention",
            'formFicheIntervention' => $formFicheIntervention->createView(),
            //'fiches' => $fiches,
            'formHeureIntervention' => $formHeureIntervention->createView(),
            'formCommentaire' => $formCommentaire->createView(),
            'heures' => $heures,
            'fiche' => $fiche,
            'intervention' => $intervention,
        ]);
    }

    #[Route("/Lhermitte/affaire/remove/fiche/intervention/{fiche}", name: "app_affaire_remove_fiche_intervention")]

    public function removeFicheIntervention($fiche, Request $request): Response
    {
        // suppression des heures en rapport avec la fiche monteur
        $heures = $this->repoInterventionFichesMonteursHeures->findBy(['interventionFicheMonteur' => $fiche]);
        foreach ($heures as $heure) {
            $entityManager = $this->entityManager;
            $entityManager->remove($heure);
            $entityManager->flush();
        }
        // suppression de la fiche monteur
        $fiche = $this->repoInterventionFicheMonteur->findOneBy(['id' => $fiche]);
        $entityManager = $this->entityManager;
        $entityManager->remove($fiche);
        $entityManager->flush();

        $this->addFlash('message', 'Fiche supprimée avec succès');

        return $this->redirectToRoute('app_affaire_me_nok');
    }

    #[Route("/Lhermitte/affaire/remove/heure/intervention/{id}/{ficheId}/{heureId}", name: "app_affaire_remove_heure_intervention")]

    public function removeHeureIntervention($id, $ficheId, $heureId, Request $request): Response
    {
        $heure = $this->repoInterventionFichesMonteursHeures->findOneBy(['id' => $heureId]);
        $entityManager = $this->entityManager;
        $entityManager->remove($heure);
        $entityManager->flush();

        $this->addFlash('message', 'Horaire supprimé avec succès');
        return $this->redirectToRoute('app_affaire_edit_fiche_intervention', ['id' => $id, 'ficheId' => $ficheId]);
    }

    #[Route("/Lhermitte/affaire/verrouiller/fiche/intervention/{id}/{ficheId}", name: "app_affaire_verrouiller_fiche_intervention")]

    public function verrouillerFicheIntervention($id, $ficheId): Response
    {
        $fiche = $this->repoInterventionFicheMonteur->findOneBy(['id' => $ficheId]);
        $heures = $this->repoInterventionFichesMonteursHeures->findBy(['interventionFicheMonteur' => $ficheId]);
        if ($heures || $fiche->getHere() == false) {
            $fiche->setLockedAt(new DateTime())
                ->setLockedBy($this->getUser());
            $entityManager = $this->entityManager;
            $entityManager->persist($fiche);
            $entityManager->flush();

            $this->addFlash('message', 'Fiche verrouillée avec succès');
        } else {
            $this->addFlash('danger', "Il n'y a pas d'heures de déclarées sur cette fiche, merci de les renseigner ! Pour cela utilisez le bouton Déclarer Heures Déplac. Travaux");
        }
        return $this->redirectToRoute('app_affaire_me_nok');
    }

    // alimenter les piéces avec les produits qu'elle contient
    public function mettreProduitsSurPieces($pieces)
    {
        $lesPieces = [];
        foreach ($pieces as $piece) {
            $pino = $piece->getCdno();
            $picod = 2;
            if ($piece->getBlno() > 0 || $piece->getCdno() == 0) {
                $pino = $piece->getBlno();
                $picod = 3;
            }

            $produits = $this->repoMouv->getDetailPiecesAffaires($pino, $picod);
            foreach ($produits as &$produit) {
                $produit['note'] = $this->blobFormater->getFormate($produit['note']);
            }

            $lesPieces[] = $piece = [
                'id' => $piece->getId(),
                'cdno' => $piece->getCdno(),
                'blno' => $piece->getBlno(),
                'adresse' => $piece->getAdresse(),
                'op' => $piece->getOp(),
                'transport' => $piece->getTransport(),
                'etat' => $piece->getEtat(),
                'affaire' => $piece->getAffaire(),
                'produits' => $produits,
                'interventions' => $piece->getInterventionMonteursPieces(),
            ];
        }
        return $lesPieces;
    }
}
