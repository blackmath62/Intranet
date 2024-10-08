<?php

namespace App\Controller;

use App\Entity\Main\Commentaires;
use App\Entity\Main\documentsFsc;
use App\Entity\Main\fscListMovement;
use App\Form\CommentairesType;
use App\Form\DocumentsFscType;
use App\Form\PerimetreBoisFscType;
use App\Form\TextType;
use App\Form\TypeDocumentFscChoiceType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\CommentairesRepository;
use App\Repository\Main\documentsFscRepository;
use App\Repository\Main\fscListMovementRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\TypeDocumentFscRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class FscAttachedFileController extends AbstractController
{
    private $mouvRepo;
    private $repoFsc;
    private $manager;
    private $repoDocs;
    private $mailer;
    private $commentairesRepo;
    private $typeDocFscRepo;
    private $usersRepo;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        MailListRepository $repoMail,
        UsersRepository $usersRepo,
        TypeDocumentFscRepository $typeDocFscRepo,
        CommentairesRepository $commentairesRepo,
        MouvRepository $mouvRepo,
        fscListMovementRepository $repoFsc,
        EntityManagerInterface $manager,
        documentsFscRepository $repoDocs,
        MailerInterface $mailer
    ) {
        $this->mouvRepo = $mouvRepo;
        $this->repoFsc = $repoFsc;
        $this->manager = $manager;
        $this->repoDocs = $repoDocs;
        $this->mailer = $mailer;
        $this->commentairesRepo = $commentairesRepo;
        $this->typeDocFscRepo = $typeDocFscRepo;
        $this->usersRepo = $usersRepo;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/Roby/fsc/liste/nok", name: "app_fsc_attached_file")]
    #[Route("/Roby/fsc/liste/ok", name: "app_fsc_attached_file_ok")]

    public function index(Request $request): Response
    {
        // vérifier si le statut des piéces a changé
        $listPieces = $this->repoFsc->findAll();
        foreach ($listPieces as $pi) {
            $this->changeStatusPiece($pi->getId());
        }

        $piecesbloquées = '';
        if ($request->attributes->get('_route') == 'app_fsc_attached_file_ok') {
            $pieces = $this->repoFsc->findBy(['status' => true, 'Probleme' => 0]);
        } elseif ($request->attributes->get('_route') == 'app_fsc_attached_file') {
            $pieces = $this->repoFsc->findBy(['status' => false, 'Probleme' => 0]);
            $piecesbloquées = $this->repoFsc->findBy(['Probleme' => 1]);
        }
        $typePieces = $this->repoFsc->getCountTypeDocByOrderFscForAll();
        $table = 'fsclistmovement';
        $comments = $this->commentairesRepo->findBy(['Tables' => $table]);

        return $this->render('fsc_attached_file/index.html.twig', [
            'typePieces' => $typePieces,
            'title' => 'Documents FSC',
            'pieces' => $pieces,
            'piecesBloquees' => $piecesbloquées,
            'comments' => $comments,
        ]);
    }

    #[Route("/Roby/fsc/show/{num}/{type}/{tiers}", name: "app_fsc_show")]

    // Voir les détails d'une piéce
    public function fscShow($num, $type, $tiers, fscListMovementRepository $repo, Request $request): Response
    {
        $table = 'fsclistmovement';
        $detailsPieces = $this->mouvRepo->getMouvOnOrder($num, $type, $tiers);
        $notreRef = $this->mouvRepo->getNotreRef($num, $type, $tiers);
        if ($type == 2) {
            $p = 'numCmd';
        } elseif ($type == 3) {
            $p = 'numBl';
        } elseif ($type == 4) {
            $p = 'numFact';
        }
        $piece = $repo->findOneBy([$p => $num, 'codePiece' => $type, 'tiers' => $tiers]);
        $piecesClients = [];
        $i = 0;
        foreach ($piece->getMovBillFscs()->getValues() as $value) {
            // facture, sa date, tiers et notre référence
            $piecesClients[$i]['id'] = $value->getId();
            $piecesClients[$i]['numFact'] = $value->getFacture();
            $piecesClients[$i]['dateFact'] = $value->getDateFact();
            $piecesClients[$i]['tiers'] = $value->getTiers();
            $piecesClients[$i]['nom'] = $value->getNom();
            $piecesClients[$i]['notreRef'] = $value->getNotreRef();
            $i++;
        }
        $comments = $this->commentairesRepo->findBy(['Tables' => $table, 'identifiant' => $piece->getId()]);
        $typeDocFsc = $this->typeDocFscRepo->findAll();
        $findDocs = [];
        $i = 0;
        foreach ($typeDocFsc as $value) {
            $findDocs[$i]['id'] = $value->getId();
            $findDocs[$i]['count'] = count($this->repoDocs->findBy(['TypeDoc' => $value->getId(), 'fscListMovement' => $piece->getId()]));
            $i += 1;
        }

        $formPerimetre = $this->createForm(PerimetreBoisFscType::class);
        $formPerimetre->handleRequest($request);
        if ($formPerimetre->isSubmitted() && $formPerimetre->isValid()) {
            $per = $this->repoFsc->findOneBy(['id' => $piece->getId()]);
            $per->setUserChangePerimetreBoisFsc($this->getUser())
                ->setUpdatePerimetreBoisFsc(new DateTime())
                ->setPerimetreBois($formPerimetre->get('perimetreBois')->getData());
            $entityManager = $this->entityManager;
            $entityManager->persist($per);
            $entityManager->flush();
        }

        $formText = $this->createForm(TextType::class);
        $formText->handleRequest($request);
        if ($formText->isSubmitted() && $formText->isValid()) {
            // vérrouiller la piéce et basculer son probleme en true
            $piece = $this->repoFsc->findOneBy(['id' => $piece->getId()]);
            if ($piece->getProbleme() == 0) {
                $piece->setProbleme(1);
            } else {
                $piece->setProbleme(0);
            }
            $this->manager->persist($piece);
            $this->manager->flush();

            $commentaire = new Commentaires();
            $dd = $formText->get('content')->getData();
            $commentaire->setCreatedAt(new DateTime())
                ->setUser($this->getUser())
                ->setTables($table)
                ->setContent($dd)
                ->setIdentifiant($piece->getId());
            $entityManager = $this->entityManager;
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $type, 'tiers' => $tiers]);
        }

        $form = $this->createForm(DocumentsFscType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('file')->getData();
            // On boucle sur les images
            foreach ($files as $file) {
                // On génère un nouveau nom de fichier
                $d = new DateTime();
                $d = $d->format('Y-m-d H-i-s');
                $filename = $file->getClientOriginalName();
                $fichier = $filename . ' - ' . $tiers . ' ' . $d . '.' . $file->guessExtension(); // md5(uniqid())

                // On copie le fichier dans le dossier uploads
                $file->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                // On crée l'image dans la base de données
                $doc = new documentsFsc();
                $doc->setFile($fichier);
                $doc->setTypeDoc($form->get('typeDoc')->getData());
                $piece->addFile($doc);
            }

            $entityManager = $this->entityManager;
            $entityManager->persist($piece);
            $entityManager->flush();

            // modifier le status d'une piéce en fonction du nombre de piéces jointes qu'elle contient
            $this->changeStatusPiece($piece->getId());

            return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $type, 'tiers' => $tiers]);
        }

        $formComment = $this->createForm(CommentairesType::class);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $dd = $formComment->get('content')->getData();
            $comment = new Commentaires();
            $comment->setCreatedAt(new DateTime())
                ->setUser($this->getUser())
                ->setTables($table)
                ->setContent($dd)
                ->setIdentifiant($piece->getId());
            $entityManager = $this->entityManager;
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $type, 'tiers' => $tiers]);
        }
        $typeDocs = $this->typeDocFscRepo->findAll();

        return $this->render('fsc_attached_file/detailsPiece.html.twig', [
            'title' => 'Détail pièce FSC',
            'typeDocs' => $typeDocs,
            'findDocs' => $findDocs,
            'notreRef' => $notreRef,
            'piece' => $piece,
            'details' => $detailsPieces,
            'documents' => $piece,
            'formText' => $formText->createView(),
            'formPerimetre' => $formPerimetre->createView(),
            'form' => $form->createView(),
            'formComment' => $formComment->createView(),
            'comments' => $comments,
            'piecesclients' => $piecesClients,
            'factureAchat' => $this->repoDocs->findOneBy(['fscListMovement' => $piece->getId(), 'TypeDoc' => 9]),
        ]);
    }

    #[Route("/Roby/change/type/doc/fsc/{id}", name: "app_change_type_doc_fsc")]

    // Modifier le type de document Fsc
    public function changeTypeDocFsc($id, Request $request)
    {

        $doc = $this->repoDocs->findOneBy(['id' => $id]);
        $piece = $this->repoFsc->findOneBy(['id' => $doc->getFscListMovement()]);

        $form = $this->createForm(TypeDocumentFscChoiceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doc = $this->repoDocs->findOneBy(['id' => $id]);
            $d = $form->get('title')->getData();
            $doc->setTypeDoc($d);
            $em = $this->entityManager;
            $em->persist($doc);
            $em->flush();

            if ($piece->getCodePiece() == 2) {
                $p = 'numCmd';
                $num = $piece->getNumCmd();
            } elseif ($piece->getCodePiece() == 3) {
                $p = 'numBl';
                $num = $piece->getNumBl();
            } elseif ($piece->getCodePiece() == 4) {
                $p = 'numFact';
                $num = $piece->getNumFact();
            }

            return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $piece->getCodePiece(), 'tiers' => $piece->getTiers()]);
        }

        return $this->render('fsc_attached_file/changeTypeDocumentFSC.html.twig', [
            'title' => 'Type Doc FSC',
            'piece' => $piece,
            'doc' => $doc,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/Roby/supprime/doc/{id}", name: "app_document_fsc_delete")]

    // supprimer un document
    public function deleteImage($id, documentsFsc $doc)
    {

        $doc = $this->repoDocs->findOneBy(['id' => $id]);
        $piece = $this->repoFsc->findOneBy(['id' => $doc->getFscListMovement()]);
        if ($piece->getCodePiece() == 2) {
            $num = $piece->getNumCmd();
        } elseif ($piece->getCodePiece() == 3) {
            $num = $piece->getNumBl();
        } elseif ($piece->getCodePiece() == 4) {
            $num = $piece->getNumFact();
        }

        $nom = $doc->getFile();
        // On supprime le fichier
        unlink($this->getParameter('images_directory') . '/' . $nom);
        // On supprime l'entrée de la base
        $em = $this->entityManager;
        $em->remove($doc);
        $em->flush();

        $this->addFlash('message', 'Piéces jointes supprimées avec succés');
        return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $piece->getCodePiece(), 'tiers' => $piece->getTiers()]);
    }

    public function changeStatusPiece($id)
    {

        // compter le nombre de piéces jointes liées à cette piéce
        $count = count($this->repoFsc->getCountTypeDocByOrderFsc($id));

        $piece = $this->repoFsc->findOneBy(['id' => $id]);
        // Date à laquelle nous avons commencé à gérer les piéces FSC sur l'intranet
        $d = new DateTime('2021/01/01');
        $fiveYearsAgo = new DateTime();
        $fiveYearsAgo = date('Y-m-d', strtotime('-5 years'));
        if ($piece->getDateFact() != null) {
            $datePiece = $piece->getDateFact()->format('Y-m-d');
        } else {
            $datePiece = null;
        }
        if ($datePiece != null && $datePiece <= $fiveYearsAgo) {
            $piece->setStatus(true);
        } else {
            if ($piece->getDateFact() < $d) {
                if ($count < 2) {
                    $piece->setStatus(false);
                } elseif ($count >= 2) {
                    if ($piece->getPerimetreBois() == null || $piece->getPerimetreBois() == 'Non Renseigné') {
                        $piece->setStatus(false);
                    } else {
                        $piece->setStatus(true);
                    }
                }
            } else {
                if ($count < 5) {
                    $piece->setStatus(false);
                } elseif ($count >= 5) {
                    if ($piece->getPerimetreBois() == null || $piece->getPerimetreBois() == 'Non Renseigné') {
                        $piece->setStatus(false);
                    } else {
                        $piece->setStatus(true);
                    }
                }
            }
        }
        $entityManager = $this->entityManager;
        $entityManager->persist($piece);
        $entityManager->flush();
    }

    // Mettre à jour la liste en comparant Divalto à la liste
    public function majFscOrderListFromDivalto()
    {

        // mise à jour des piéces
        $piecesOk = $this->repoFsc->findBy(['status' => true]);
        $listpieceOk = '';
        for ($ligListOk = 0; $ligListOk < count($piecesOk); $ligListOk++) {
            if ($ligListOk == 0) {
                $listpieceOk = $piecesOk[$ligListOk]->getNumCmd();
            } else {
                $listpieceOk .= ' , ' . $piecesOk[$ligListOk]->getNumCmd();
            }
        }
        if (!empty($listpieceOk)) {
            $maj = $this->mouvRepo->getFscOrderList($listpieceOk);
        } else {
            $maj = $this->mouvRepo->getFscOrderListRun();
        }

        // Ajout d'une vérification pour les résultats de la requête
        if (empty($maj)) {
            $this->addFlash('warning', 'Aucune mise à jour trouvée.');
            return;
        }

        $this->majFscOrderListFromList();

        for ($ligMaj = 0; $ligMaj < count($maj); $ligMaj++) {
            $search = $this->repoFsc->findOneBy(['codePiece' => $maj[$ligMaj]['codePiece'], 'numCmd' => $maj[$ligMaj]['numCmd'], 'tiers' => $maj[$ligMaj]['tiers']]);
            if ($search == null) {
                $search = new fscListMovement();
                $user = $this->usersRepo->findOneBy(['pseudo' => 'intranet']);
                $search->setCreatedAt(new DateTime())
                    ->setUpdatedAt(new DateTime())
                    ->setStatus(false)
                    ->setProbleme(false)
                    ->setPerimetreBois('Non Renseigné')
                    ->setUserChangePerimetreBoisFsc($user)
                    ->setUpdatePerimetreBoisFsc(new DateTime())
                    ->setNotreRef($maj[$ligMaj]['notreRef'])
                    ->setDateCmd(new DateTime($maj[$ligMaj]['dateCmd']));
                if ($maj[$ligMaj]['numBl'] > 0) {
                    $search->setDateBl(new DateTime($maj[$ligMaj]['dateBl']));
                } else {
                    $search->setDateBl(null);
                }
                if ($maj[$ligMaj]['numFact'] > 0) {
                    $search->setDateFact(new DateTime($maj[$ligMaj]['dateFact']));
                } else {
                    $search->setDateFact(null);
                }
                $search->setNumCmd($maj[$ligMaj]['numCmd'])
                    ->setNumBl($maj[$ligMaj]['numBl'])
                    ->setNumFact($maj[$ligMaj]['numFact'])
                    ->setTiers($maj[$ligMaj]['tiers'])
                    ->setUtilisateur($maj[$ligMaj]['utilisateur'])
                    ->setCodePiece($maj[$ligMaj]['codePiece']);
            } else {
                $search->setUtilisateur($maj[$ligMaj]['utilisateur'])
                    ->setUpdatedAt(new DateTime())
                    ->setNotreRef($maj[$ligMaj]['notreRef'])
                    ->setDateCmd(new DateTime($maj[$ligMaj]['dateCmd']));
                if ($maj[$ligMaj]['numBl'] > 0) {
                    $search->setDateBl(new DateTime($maj[$ligMaj]['dateBl']));
                } else {
                    $search->setDateBl(null);
                }
                if ($maj[$ligMaj]['numFact'] > 0) {
                    $search->setDateFact(new DateTime($maj[$ligMaj]['dateFact']));
                } else {
                    $search->setDateFact(null);
                }
                $search->setNumCmd($maj[$ligMaj]['numCmd'])
                    ->setNumBl($maj[$ligMaj]['numBl'])
                    ->setNumFact($maj[$ligMaj]['numFact']);
            }
            $this->manager->persist($search);
        }
        $this->manager->flush();
        $this->sendMail();

    }

    #[Route("/Roby/fsc/order/list/maj", name: "app_fsc_order_list_maj")]
    // Mettre à jour la liste en comparant Divalto à la liste avec redirection de page
    public function majFscOrderListFromDivaltoRedirect(): Response
    {
        $this->majFscOrderListFromDivalto();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_fsc_attached_file');
    }

    #[Route("/Roby/fsc/order/list/maj/from/list", name: "app_fsc_order_list_maj_from_list")]

    // Mettre à jour la liste en comparant la liste et Divalto
    public function majFscOrderListFromList()
    {
        $search = $this->repoFsc->findBy(['status' => false]);
        foreach ($search as $value) {
            $piece = $this->mouvRepo->getMouvByOrder($value->getNumCmd(), $value->getTiers(), $value->getCodePiece());
            if ($piece == false) {
                $this->deleteOrder($value->getId());
            } else {
                $value->setUpdatedAt(new DateTime());
                if ($value->getNumBl() > 0) {
                    $value->setDateBl(new DateTime($piece['dateBl']));
                } else {
                    $value->setDateBl(null);
                }
                if ($value->getNumFact() > 0) {
                    $value->setDateFact(new DateTime($piece['dateFact']));
                } else {
                    $value->setDateFact(null);
                }
                $value->setNumBl($piece['numBl'])
                    ->setNumFact($piece['numFact']);
                $this->manager->persist($value);
                $this->manager->flush();
            }
        }

    }

    // suppression d'une piéce dans le BDD
    public function deleteOrder($id)
    {
        $docs = $this->repoDocs->findBy(['fscListMovement' => $id]);
        foreach ($docs as $value) {
            // supprimer les fichiers dans le dossier
            unlink($this->getParameter('images_directory') . '/' . $value->getFile());
            // supprimer la ligne dans la BDD document
            $em = $this->entityManager;
            $em->remove($value);
            $em->flush();
        }
        $search = $this->repoFsc->findOneBy(['id' => $id]);

        // On supprime la piéce de la base
        $em = $this->entityManager;
        $em->remove($search);
        $em->flush();

    }

    // mail automatique pour demander les documents Fsc
    public function sendMail()
    {
        $piecesAnormales = [];
        $pieces = $this->repoFsc->getPieceFscAAlimenter();
        for ($i = 0; $i < count($pieces); $i++) {
            if ($pieces[$i]['codePiece'] == 2) {
                $typePiece = 'dateCmd';
            } elseif ($pieces[$i]['codePiece'] == 3) {
                $typePiece = 'dateBl';
            } elseif ($pieces[$i]['codePiece'] == 4) {
                $typePiece = 'dateFact';
            }
            $d = new DateTime('2021/01/01');
            $count = count($this->repoFsc->getCountTypeDocByOrderFsc($pieces[$i]['id']));
            if ($pieces[$i][$typePiece] >= $d && ($count < 5 or $pieces[$i]['perimetreBois'] == 'Non Renseigné')) {
                $piecesAnormales[$i]['notreRef'] = $pieces[$i]['notreRef'];
                $piecesAnormales[$i]['numCmd'] = $pieces[$i]['numCmd'];
                $piecesAnormales[$i]['dateCmd'] = $pieces[$i]['dateCmd'];
                $piecesAnormales[$i]['numBl'] = $pieces[$i]['numBl'];
                $piecesAnormales[$i]['dateBl'] = $pieces[$i]['dateBl'];
                $piecesAnormales[$i]['numFact'] = $pieces[$i]['numFact'];
                $piecesAnormales[$i]['dateFact'] = $pieces[$i]['dateFact'];
                $piecesAnormales[$i]['tiers'] = $pieces[$i]['tiers'];
                $piecesAnormales[$i]['perimetre'] = $pieces[$i]['perimetreBois'];
                $piecesAnormales[$i]['count'] = $count;
            } elseif ($pieces[$i][$typePiece] < $d && ($count < 2 or $pieces[$i]['perimetreBois'] == 'Non Renseigné')) {
                $piecesAnormales[$i]['notreRef'] = $pieces[$i]['notreRef'];
                $piecesAnormales[$i]['numCmd'] = $pieces[$i]['numCmd'];
                $piecesAnormales[$i]['dateCmd'] = $pieces[$i]['dateCmd'];
                $piecesAnormales[$i]['numBl'] = $pieces[$i]['numBl'];
                $piecesAnormales[$i]['dateBl'] = $pieces[$i]['dateBl'];
                $piecesAnormales[$i]['numFact'] = $pieces[$i]['numFact'];
                $piecesAnormales[$i]['dateFact'] = $pieces[$i]['dateFact'];
                $piecesAnormales[$i]['tiers'] = $pieces[$i]['tiers'];
                $piecesAnormales[$i]['perimetre'] = $pieces[$i]['perimetreBois'];
                $piecesAnormales[$i]['count'] = $count;
            }
        }
        // envoyer un mail si il y a des infos à envoyer
        if (count($piecesAnormales) > 0) {
            // envoyer un mail
            $html = $this->renderView('mails/listePieceFscSansPj.html.twig', ['piecesAnormales' => $piecesAnormales]);
            // TODO Remettre marina en destinataire des mails.
            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to('marina@roby-fr.com')
                ->cc($this->mailTreatement)
                ->subject('Liste des piéces sur lesquels il manque les piéces jointes Fsc')
                ->html($html);
            $this->mailer->send($email);
        }
    }

}
