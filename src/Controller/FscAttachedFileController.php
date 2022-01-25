<?php

namespace App\Controller;

use DateTime;
use App\Form\MultiUploadType;
use App\Form\DocumentsFscType;
use App\Entity\Main\documentsFsc;
use Symfony\Component\Mime\Email;
use App\Entity\Main\fscListMovement;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Divalto\MouvRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Container8AgRG4X\getMouvRepositoryService;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Main\documentsFscRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\fscListMovementRepository;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FscAttachedFileController extends AbstractController
{
    private $mouvRepo;
    private $repoFsc;
    private $manager;
    private $repoDocs;
    private $mailer;

    public function __construct(MouvRepository $mouvRepo, fscListMovementRepository $repoFsc, EntityManagerInterface $manager, documentsFscRepository $repoDocs,MailerInterface $mailer)
    {
        $this->mouvRepo = $mouvRepo;
        $this->repoFsc = $repoFsc;
        $this->manager = $manager;
        $this->repoDocs = $repoDocs;
        $this->mailer = $mailer;
        //parent::__construct();
    }
    
    /**
     * @Route("/Roby/fsc/liste/nok", name="app_fsc_attached_file")
     * @Route("/Roby/fsc/liste/ok", name="app_fsc_attached_file_ok")
     */
    public function index(Request $request): Response
    {

        if ($request->attributes->get('_route') == 'app_fsc_attached_file_ok') {
            $pieces = $this->repoFsc->findBy(['status' => true]);
        }elseif ($request->attributes->get('_route') == 'app_fsc_attached_file') {
            $pieces = $this->repoFsc->findBy(['status' => false]);
        }

        return $this->render('fsc_attached_file/index.html.twig', [
            'controller_name' => 'FscAttachedFileController',
            'title' => 'Documents FSC',
            'pieces' => $pieces
        ]);
    }

    /**
     * @Route("/Roby/fsc/show/{num}/{type}/{tiers}", name="app_fsc_show")
     */
    public function fscShow($num,$type, $tiers, fscListMovementRepository $repo	 , Request $request): Response
    {
        $detailsPieces = $this->mouvRepo->getMouvOnOrder($num, $type, $tiers);
        if ($type == 2) {
            $p = 'numCmd';
        }elseif ($type == 3) {
            $p = 'numBl';
        }elseif ($type == 4) {
            $p = 'numFact';
        }
        $piece = $repo->findOneBy([$p => $num, 'codePiece' => $type, 'tiers' => $tiers]);

        $form = $this->createForm(DocumentsFscType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('file')->getData();
            // On boucle sur les images
            foreach($files as $file){
                // On génère un nouveau nom de fichier
                $d = new DateTime();
                $d = $d->format('Y-m-d H-i-s');
                $filename = $file->getClientOriginalName();
                $fichier = $filename . ' - ' . $tiers . ' ' . $d .'.'. $file->guessExtension(); // md5(uniqid())
                
                // On copie le fichier dans le dossier uploads
                $file->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $doc = new documentsFsc();
                $doc->setFile($fichier);
                $piece->addFile($doc);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($piece);
            $entityManager->flush();

            // modifier le status d'une piéce en fonction du nombre de piéces jointes qu'elle contient
            $this->changeStatusPiece($piece->getId());

            return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $type, 'tiers' => $tiers]);
        }

        return $this->render('fsc_attached_file/detailsPiece.html.twig', [
            'title' => 'Détail pièce FSC',
            'details' => $detailsPieces,
            'documents' => $piece,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/Roby/supprime/doc/{id}", name="app_document_fsc_delete")
     */
    public function deleteImage($id, documentsFsc $doc){

        $doc = $this->repoDocs->findOneBy(['id' => $id]);
        $piece = $this->repoFsc->findOneBy(['id' => $doc->getFscListMovement()]);
        if ($piece->getCodePiece() == 2 ) {
            $num = $piece->getNumCmd();
        }elseif ($piece->getCodePiece() == 3) {
            $num = $piece->getNumBl();
        }elseif ($piece->getCodePiece() == 4) {
            $num = $piece->getNumFact();
        }
        
        $nom = $doc->getFile();
        // On supprime le fichier
        unlink($this->getParameter('images_directory').'/'.$nom);
        // On supprime l'entrée de la base
        $em = $this->getDoctrine()->getManager();
        $em->remove($doc);
        $em->flush();

        // modifier le status d'une piéce en fonction du nombre de piéces jointes qu'elle contient
        $this->changeStatusPiece($doc->getFscListMovement());
        
        $this->addFlash('message', 'Piéces jointes supprimées avec succés');
        return $this->redirectToRoute('app_fsc_show', ['num' => $num, 'type' => $piece->getCodePiece(), 'tiers' => $piece->getTiers()]);
    }

    public function changeStatusPiece($id){

        // compter le nombre de piéces  jointes liées à cette piéce
        $count = count($this->repoDocs->findBy(['fscListMovement' => $id ]));
        $piece = $this->repoFsc->findOneBy(['id' => $id]);
        if ($count < 4) {
            $piece->setStatus(false);
        }elseif ($count >= 4) {
            $piece->setStatus(true);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($piece);
        $entityManager->flush();
    }


    /**
     * @Route("/Roby/fsc/order/list/maj", name="app_fsc_order_list_maj")
     */
    // Mettre à jour la liste en comparant Divalto à la liste
    public function majFscOrderListFromDivalto(): Response
    {
        
        // mise à jour des piéces
        // TODO voir pour ne pas extraire les commandes qui ont déjà été traitées
        $piecesOk = $this->repoFsc->findBy(['status' => True]);
        $listpieceOk = '';
        for ($ligListOk=0; $ligListOk <count($piecesOk) ; $ligListOk++) { 
            if ($ligListOk == 0) {
                $listpieceOk = $piecesOk[$ligListOk]->getNumCmd();
            }else {
                $listpieceOk = $listpieceOk . ' , ' . $piecesOk[$ligListOk]->getNumCmd();
            }
        }
        if (!empty($listpieceOk)) {
            $maj = $this->mouvRepo->getFscOrderList($listpieceOk);
        }else {
            $maj = $this->mouvRepo->getFscOrderListRun();
        }
        $this->majFscOrderListFromList();

        for ($ligMaj=0; $ligMaj <count($maj) ; $ligMaj++) {
           $search = $this->repoFsc->findOneBy(['codePiece' => $maj[$ligMaj]['codePiece'], 'numCmd' => $maj[$ligMaj]['numCmd'], 'tiers' => $maj[$ligMaj]['tiers']]);
           if ($search == null) {
               $search = new fscListMovement();
               $search->setCreatedAt(new DateTime())
                      ->setUpdatedAt(new DateTime())
                      ->setStatus(false)
                      ->setNotreRef($maj[$ligMaj]['notreRef'])
                      ->setDateCmd(new DateTime($maj[$ligMaj]['dateCmd']));
                if ( $maj[$ligMaj]['numBl'] > 0) {
                   $search->setDateBl(new DateTime($maj[$ligMaj]['dateBl']));
               }else{
                $search->setDateBl(null);
               }
               if ($maj[$ligMaj]['numFact'] > 0) {
                   $search->setDateFact(new DateTime($maj[$ligMaj]['dateFact']));
               }else {
                $search->setDateFact(null);
               }
                      $search->setNumCmd($maj[$ligMaj]['numCmd'])
                      ->setNumBl($maj[$ligMaj]['numBl'])
                      ->setNumFact($maj[$ligMaj]['numFact'])
                      ->setTiers($maj[$ligMaj]['tiers'])
                      ->setUtilisateur($maj[$ligMaj]['utilisateur'])
                      ->setCodePiece($maj[$ligMaj]['codePiece']);
           }else{
                $search->setUtilisateur($maj[$ligMaj]['utilisateur'])
                      ->setUpdatedAt(new DateTime())
                      ->setNotreRef($maj[$ligMaj]['notreRef'])
                      ->setDateCmd(new DateTime($maj[$ligMaj]['dateCmd']));
                      if ( $maj[$ligMaj]['numBl'] > 0 ) {
                            $search->setDateBl(new DateTime($maj[$ligMaj]['dateBl']));
                        }else{
                        $search->setDateBl(null);
                        }
                        if ($maj[$ligMaj]['numFact'] > 0) {
                            $search->setDateFact(new DateTime($maj[$ligMaj]['dateFact']));
                        }else {
                        $search->setDateFact(null);
                        }
                      $search->setNumCmd($maj[$ligMaj]['numCmd'])
                      ->setNumBl($maj[$ligMaj]['numBl'])
                      ->setNumFact($maj[$ligMaj]['numFact']);
           }
           $this->manager->persist($search);
           $this->manager->flush();
        }
        $this->sendMail();
        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_fsc_attached_file');
    }

    /**
     * @Route("/Roby/fsc/order/list/maj/from/list", name="app_fsc_order_list_maj_from_list")
     */
    // Mettre à jour la liste en comparant la liste et Divalto
    public function majFscOrderListFromList()
    {
        $search = $this->repoFsc->findBy(['status' => false]);
        foreach ($search as $value) {
            $piece = $this->mouvRepo->getMouvByOrder($value->getNumCmd(), $value->getTiers(), $value->getCodePiece());
            if ($piece == false) {
                $this->deleteOrder($value->getId());
            }else {
                $value->setUpdatedAt(new DateTime());
                    if ( $value->getNumBl() > 0 ) {
                        $value->setDateBl(new DateTime($piece['dateBl']));
                    }else{
                    $value->setDateBl(null);
                    }
                    if ($value->getNumFact() > 0) {
                        $value->setDateFact(new DateTime($piece['dateFact']));
                    }else {
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
    public function deleteOrder($id){
        $docs = $this->repoDocs->findBy(['fscListMovement' => $id]);
        foreach ($docs as $value) {
            // supprimer les fichiers dans le dossier
            unlink($this->getParameter('images_directory').'/'.$value->getFile());
            // supprimer la ligne dans la BDD document
            $em = $this->getDoctrine()->getManager();
            $em->remove($value);
            $em->flush();
        }
        $search = $this->repoFsc->findOneBy(['id' => $id]);

        // On supprime la piéce de la base
        $em = $this->getDoctrine()->getManager();
        $em->remove($search);
        $em->flush();

    }

    // mail automatique pour demander les documents Fsc
    public function sendMail(){
       $piecesAnormales = [];
       $pieces = $this->repoFsc->findAll();
       for ($i=0; $i <count($pieces) ; $i++) { 
           $count = count($this->repoDocs->findBy(['fscListMovement' => $pieces[$i]->getId() ]));
           if ($count < 4) {
            $piecesAnormales[$i]['notreRef'] = $pieces[$i]->getNotreRef();
            $piecesAnormales[$i]['numCmd'] = $pieces[$i]->getNumCmd();
            $piecesAnormales[$i]['dateCmd'] = $pieces[$i]->getDateCmd();
            $piecesAnormales[$i]['numBl'] = $pieces[$i]->getNumBl();
            $piecesAnormales[$i]['dateBl'] = $pieces[$i]->getDateBl();
            $piecesAnormales[$i]['numFact'] =$pieces[$i]->getNumFact();
            $piecesAnormales[$i]['dateFact'] = $pieces[$i]->getDateFact();
            $piecesAnormales[$i]['tiers'] = $pieces[$i]->getTiers();
            $piecesAnormales[$i]['count'] = $count;
           }
       }
       // envoyer un mail
       $html = $this->renderView('mails/listePieceFscSansPj.html.twig', ['piecesAnormales' => $piecesAnormales ]);
       $email = (new Email())
       ->from('intranet@groupe-axis.fr')
       ->to('jpochet@groupe-axis.fr')
       ->cc('jpochet@groupe-axis.fr')
       ->subject('Liste des piéces sur lesquels il manque les piéces jointes Fsc')
       ->html($html);
       $this->mailer->send($email);
       
    }


}
