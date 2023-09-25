<?php

namespace App\Controller;

use App\Entity\Main\Commentaires;
use App\Entity\Main\ControleArticlesFsc;
use App\Form\CommentairesType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\CommentairesRepository;
use App\Repository\Main\ControleArticlesFscRepository;
use App\Repository\Main\fscListMovementRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ROBY")]

class MovementFscController extends AbstractController
{
    private $mouvRepo;
    private $commentairesRepo;
    private $identifiant;
    private $controleArticleFscRepo;
    private $fscAttachedFileRepo;
    private $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        fscListMovementRepository $fscAttachedFileRepo,
        ControleArticlesFscRepository $controleArticleFscRepo,
        MouvRepository $mouvRepo,
        CommentairesRepository $commentairesRepo
    ) {
        $this->mouvRepo = $mouvRepo;
        $this->commentairesRepo = $commentairesRepo;
        $this->identifiant = '999999999';
        $this->controleArticleFscRepo = $controleArticleFscRepo;
        $this->fscAttachedFileRepo = $fscAttachedFileRepo;
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/Roby/movement/fsc/view/general", name: "app_movement_fsc_view_list")]

    public function index(): Response
    {
        //TODO ajouter une validation manuelle du contrôle article Fsc et lancer la majControleArticleFsc
        $listArticleFsc = $this->mouvRepo->getMovFsc();

        $controles = $this->controleArticleFscRepo->findAll();

        return $this->render('movement_fsc/index.html.twig', [
            'controles' => $controles,
            'MovFscs' => $listArticleFsc,
            'title' => 'Mouvements FSC',
            'comments' => $this->commentairesRepo->findBy(['identifiant' => $this->identifiant]),
        ]);
    }

    #[Route("/Roby/movement/fsc/view/detail/{concat}", name: "app_movement_fsc_detail_art")]

    public function getDetailMouvArticleFsc($concat, Request $request): Response
    {
        $details = $this->mouvRepo->getDetailArtMovFsc($concat);

        $formComment = $this->createForm(CommentairesType::class);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $commentaire = new Commentaires();
            $dd = $formComment->get('content')->getData();
            $commentaire->setCreatedAt(new DateTime())
                ->setUser($this->getUser())
                ->setTables('artFsc' . $concat)
                ->setContent($dd)
                ->setIdentifiant($this->identifiant);
            $entityManager = $this->entityManager;
            $entityManager->persist($commentaire);
            $entityManager->flush();

            //mettre à jour la liste des controles articles Fsc étant donné qu'un commentaire a été déposé pour expliquer
            $controle = $this->controleArticleFscRepo->findOneBy(['products' => $concat]);
            $controle->setUpdatedAt(new DateTime())
                ->setControledBy($this->getUser())
                ->setStatus(1);
            $entityManager = $this->entityManager;
            $entityManager->persist($controle);
            $entityManager->flush();

            return $this->redirectToRoute('app_movement_fsc_detail_art', ['concat' => $concat]);
        }
        $factures = $this->fscAttachedFileRepo->getListFacture();
        $listFactures = '';
        for ($i = 0; $i < count($factures); $i++) {
            //dd($factures[$i]);
            if ($i == 0) {
                $listFactures = $factures[$i]['numFact'];
            } else {
                $listFactures = $listFactures . ',' . $factures[$i]['numFact'];
            }
        }
        $produits = $this->mouvRepo->getListeProduits($listFactures);
        // récupérer les fichiers Factures fournisseurs en lien avec les achats FSC (Documents Fournisseurs FSC, commencé en 2021)
        $files = [];
        //dd($produits);
        $f = 0;
        foreach ($produits as $produit) {
            foreach ($factures as $facture) {
                if ($facture['numFact'] == $produit['Factures']) {
                    if ($produit['Lien'] == $concat) {
                        $files[$f]['fichier'] = $facture['fichier'];
                        $f++;
                    }
                }
            }
        }
        $comments = $this->commentairesRepo->findBy(['Tables' => 'artFsc' . $concat, 'identifiant' => $this->identifiant]);

        return $this->render('movement_fsc/detailsArt.html.twig', [
            'resume' => $this->mouvRepo->getMovFscOneArt($concat),
            'details' => $details,
            'files' => $files,
            'comments' => $comments,
            'formComment' => $formComment->createView(),
            'title' => 'Détail de l\'article',
        ]);
    }

    #[Route("/Roby/movement/fsc/maj/controle/article", name: "app_maj_controle_article_fsc")]

    public function majControleArticleFsc(UsersRepository $users)
    {
        //mettre à jour la liste des controles articles Fsc en vérifiant si la date
        //de la derniére piéce est inférieur à la date du dernier commentaire ou de la derniére validation manuelle
        $listArticleFsc = $this->mouvRepo->getMovFsc();
        foreach ($listArticleFsc as $value) {
            $concat = $value['Lien'];
            $lastOrder = $this->mouvRepo->getMaxPiece($concat);
            $controle = $this->controleArticleFscRepo->findOneBy(['products' => $concat]);
            $user = $users->findOneBy(['pseudo' => 'intranet']);

            if ($controle == null) {
                $controle = new ControleArticlesFsc();
                $controle->setCreatedAt(new DateTime())
                    ->setUpdatedAt(new DateTime())
                    ->setProducts($concat)
                    ->setControledBy($user)
                    ->setLastOrder($lastOrder['NumPiece'])
                    ->setLastOrderAt(new DateTime($lastOrder['MaxDate']))
                    ->setStatus(0);
            } else {
                if ($lastOrder['NumPiece'] == $controle->getLastOrder()) {
                    $controle->setUpdatedAt(new DateTime());
                } else {

                    $controle->setUpdatedAt(new DateTime())
                        ->setControledBy($user)
                        ->setLastOrder($lastOrder['NumPiece'])
                        ->setLastOrderAt(new DateTime($lastOrder['MaxDate']))
                        ->setStatus(0);
                }
            }
            $entityManager = $this->entityManager;
            $entityManager->persist($controle);
            $entityManager->flush();
        }
        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_movement_fsc_view_list');

    }

    #[Route("/Roby/movement/fsc/maj/lock/unlock/article/{concat}", name: "app_lock_unlock_article_fsc")]

    // Vérrouillage manuel
    public function lockUnlockArticleFsc($concat)
    {
        $controle = $this->controleArticleFscRepo->findOneBy(['products' => $concat]);
        if ($controle->getStatus() == 0) {
            $statut = 1;
        } else {
            $statut = 0;
        }
        $controle->setUpdatedAt(new DateTime())
            ->setControledBy($this->getUser())
            ->setStatus($statut);
        $entityManager = $this->entityManager;
        $entityManager->persist($controle);
        $entityManager->flush();

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_movement_fsc_view_list');

    }
}
