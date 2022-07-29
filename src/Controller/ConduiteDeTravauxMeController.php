<?php

namespace App\Controller;

use DateTime;
use App\Form\AddEmailType;
use RecursiveArrayIterator;
use App\Entity\Main\Comments;
use App\Entity\Main\MailList;
use App\Form\CommentairesType;
use RecursiveIteratorIterator;
use App\Entity\Main\Commentaires;
use App\Form\OthersDocumentsType;
use Symfony\Component\Mime\Email;
use App\Entity\Main\OthersDocuments;
use App\Controller\AdminEmailController;
use App\Entity\Main\ConduiteDeTravauxMe;
use App\Repository\Main\UsersRepository;
use App\Form\AddPieceConduiteTravauxType;
use App\Form\ConduiteTravauxAlimenterType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\MailListRepository;
use App\Entity\Main\ConduiteTravauxAddPiece;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Main\CommentairesRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\OthersDocumentsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Main\ConduiteDeTravauxMeRepository;
use App\Repository\Main\ConduiteTravauxAddPieceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConduiteDeTravauxMeController extends AbstractController
{
    private $repoMouv;
    private $repoConduite;
    private $repoUser;
    private $repoComments;
    private $repoDocs;
    private $mailer;
    private $repoAddPieces;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;
    
    public function __construct(AdminEmailController $adminEmailController, MailListRepository $repoMail, ConduiteTravauxAddPieceRepository $repoAddPieces, OthersDocumentsRepository $repoDocs,MailerInterface $mailer, CommentairesRepository $repoComments, ConduiteDeTravauxMeRepository $repoConduite, MouvRepository $repoMouv, UsersRepository $repoUser)
    {
        $this->repoMouv = $repoMouv;
        $this->repoConduite = $repoConduite;
        $this->repoUser = $repoUser;
        $this->repoComments = $repoComments;
        $this->repoDocs = $repoDocs;
        $this->mailer = $mailer;
        $this->repoAddPieces = $repoAddPieces;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        $this->adminEmailController = $adminEmailController;
        //parent::__construct();
    }


    /**
     * @Route("/Lhermitte/conduite/travaux/num/ajout/ajax/{num}/{type}",name="app_conduite_de_travaux_me_add_num_piece")
     */
    public function addNumPiece($num, $type, Request $request): Response
    {
        
        $numPiece = new ConduiteTravauxAddPiece();
        $numPiece->setNumPiece(trim(strip_tags($num)))
                 ->setCreatedAt(new DateTime)
                 ->setCreatedBy($this->getUser())
                 ->setType($type);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($numPiece);
        $entityManager->flush();
        $id = $numPiece->getId();
        return new JsonResponse(['id' => $id]);
        
    }

    /**
     * @Route("/Lhermitte/conduite/travaux/change/etat/{id}/{etat}",name="app_conduite_de_travaux_me_change_etat")
     */
    public function changeEtat($id, $etat, Request $request): Response
    {
         // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $piece = $this->repoConduite->findOneBy(['entId' => $id]);
        $piece->setEtat($etat);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($piece);
        $entityManager->flush();
    
        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_conduite_de_travaux_me_nok');
        
    }
    
    /**
     * @Route("/Lhermitte/conduite/de/travaux/me/ok", name="app_conduite_de_travaux_me_ok")
     * @Route("/Lhermitte/conduite/de/travaux/me/nok", name="app_conduite_de_travaux_me_nok")
     */
    public function index(Request $request): Response
    {
        if ($request->attributes->get('_route') == 'app_conduite_de_travaux_me_ok') {
            $pieces = $this->repoConduite->getOk();
            $commentaires = $this->repoConduite->getCommentsOk();
        }
        elseif ($request->attributes->get('_route') == 'app_conduite_de_travaux_me_nok') {
            $pieces = $this->repoConduite->getNok();
            $commentaires = $this->repoConduite->getCommentsNok();
        }

        $addPieces = $this->repoAddPieces->findAll();
        $formAddPieces = $this->createForm(AddPieceConduiteTravauxType::class, $addPieces);
        $formAddPieces->handleRequest($request);
        if ($formAddPieces->isSubmitted() && $formAddPieces->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($addPieces);
            $entityManager->flush();
            
            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_conduite_de_travaux_me_nok');
        }

        // Calendrier conduite travaux
        $events = $this->repoConduite->getDateDebutFinChantierEnCours();
        $rdvs = [];
        
        foreach($events as $event){
           
            $start = new DateTime($event['start']);
            $end = new DateTime($event['end']);

            $rdvs[] = [
                'id' => $event['id'],
                'start' => $start->format('Y-m-d H:i:s'),
                'end' => $end->format('Y-m-d H:i:s'),
                'title' => $event['nom'] . $event['adresse'] . ' Etat : ' . $event['etat'],
                'backgroundColor' => $event['backgroundColor'],
                'borderColor' => '#FFFFFF',
                'textColor' => $event['textColor'],
               ];
           }
           
       // récupérer les fériers en JSON sur le site etalab
       $ferierJson = file_get_contents("https://etalab.github.io/jours-feries-france-data/json/metropole.json");
       // On ajoute les fériers au calendrier des congés
       $jsonIterator = new RecursiveIteratorIterator(
           new RecursiveArrayIterator(json_decode($ferierJson, TRUE)),
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
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        unset($form);
        $form = $this->createForm(AddEmailType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $find = $this->repoMail->findBy(['email' => $form->getData()['email'], 'page' => $tracking]);
            if (empty($find) | is_null($find)) {
                $mail = new MailList();
                $mail->setCreatedAt(new DateTime())
                     ->setEmail($form->getData()['email'])
                     ->setPage($tracking);
                $em = $this->getDoctrine()->getManager();
                $em->persist($mail);
                $em->flush();
            }else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                return $this->redirectToRoute('app_conduite_de_travaux_me_nok');
            }
        }

        
        return $this->render('conduite_de_travaux_me/index.html.twig', [
            // todo à voir pour filtrer l'état en fonction de la page affichée
            'pieces' => $pieces,
            'data' => $data,
            'title' => "Conduire Travaux",
            'commentaires' => $commentaires,
            'formAddPieces' => $formAddPieces->createView(),
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/Lhermitte/conduite/de/travaux/me/show/{id}", name="app_conduite_de_travaux_me_show")
     */
    public function show($id, Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $table = 'conduiteTravaux';
        $details = $this->repoMouv->getDetailArticleConduiteTravaux($id);
        $piece = $this->repoConduite->findOneBy(['entId' => $id]);
        $form = $this->createForm(CommentairesType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $comment = new Commentaires();
            $comment->setIdentifiant($piece->getId())
                    ->setTables($table)
                    ->setContent($data->getContent())
                    ->setUser($this->getUser())
                    ->setCreatedAt(new DateTime);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            unset($comment);
            $this->addFlash('message', 'Commentaire ajouté avec succés');
        }

        $formMajConduiteTravaux = $this->createForm(ConduiteTravauxAlimenterType::class);
        $formMajConduiteTravaux->handleRequest($request);
        if ($formMajConduiteTravaux->isSubmitted() && $formMajConduiteTravaux->isValid()) {
            $conduite = $this->repoConduite->findOneBy(['entId' => $id]);
            $data = $formMajConduiteTravaux->getData();
            if ($data->getEtat()) {
                $conduite->setEtat($data->getEtat());
            }
            if ($data->getDateDebutChantier()) {
                $conduite->setDateDebutChantier(new DateTime($data->getDateDebutChantier()->format('Y-m-d H:i')));
            }
            if ($data->getDateFinChantier()) {
                $conduite->setDateFinChantier(new DateTime($data->getDateFinChantier()->format('Y-m-d H:i')));
            }
            if ($data->getDureeTravaux()) {
                $conduite->setDureeTravaux($data->getDureetravaux());
            }
            if ($data->getbackgroundColor()) {
                $conduite->setbackgroundColor($data->getBackgroundColor());
            }
            if ($data->getTextColor()) {
                $conduite->setTextColor($data->getTextColor());
            }
            $conduite->setUpdatedBy($this->getUser());
            $conduite->setUpdatedAt(new DateTime);;
            $em = $this->getDoctrine()->getManager();
            $em->persist($conduite);
            $em->flush();
            $this->addFlash('message', 'Mise à jour effectuée avec succés');
        }

        $formFiles = $this->createForm(OthersDocumentsType::class);
        $formFiles->handleRequest($request);
        if ($formFiles->isSubmitted() && $formFiles->isValid()) {
            $files = $formFiles->get('file')->getData();
            // On boucle sur les images
            foreach($files as $file){
                // On génère un nouveau nom de fichier
                $d = new DateTime();
                $d = $d->format('Y-m-d H-i-s');
                $filename = $file->getClientOriginalName();
                $fichier = $filename; // md5(uniqid())
                $search = $this->repoDocs->findOneBy(['identifiant'=> $id, 'file' => $fichier]);
                if ($search == null) {                    
                    // On copie le fichier dans le dossier uploads
                    $file->move(
                        $this->getParameter('doc_lhermitte'),
                        $fichier
                    );
                    // On crée l'image dans la base de données
                    $doc = new OthersDocuments();
                    $doc->setFile($fichier);
                    $doc->setTables($table);
                    $doc->setCreatedAt(new DateTime);
                    $doc->setUser($this->getUser());
                    $doc->setIdentifiant($id);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($doc);
                    $em->flush();
                    $this->addFlash('message', 'Fichier ' . $filename . ' ajouté avec succés');
                    unset($file);
                }else {
                    $this->addFlash('danger', 'Fichier ' . $filename . ' est déjà présent ! ce fichier n\'est pas sauvegardé !');
                }
            }
           
        }
        $comments = $this->repoComments->findBy(['identifiant' => $piece->getId(), 'Tables' => $table]);
        $fichiers = $this->repoDocs->findby(['identifiant' => $id]);
        if (trim($piece->getAffaire()) <> '' && $piece->getAffaire() <> NULL) {
            $achats = $this->repoMouv->getAchatLieAffaireConduitetravaux($piece->getAffaire());
        }else {
            $achats = '';
        }
        return $this->render('conduite_de_travaux_me/show.html.twig', [
            // todo à voir pour filtrer l'état en fonction de la page affichée
            'details' => $details,
            'piece' => $piece,
            'fichiers' => $fichiers,
            'achats' => $achats,
            'title' => "Détail Conduire Travaux",
            'comments' => $comments,
            'form' => $form->createView(),
            'formFiles' => $formFiles->createView(),
            'formMajConduiteTravaux' => $formMajConduiteTravaux->createView()
        ]);
    }

    /**
     * @Route("/Lhermitte/conduite/de/travaux/me/delete/piece/jointe/{id}/{identifiant}", name="app_conduite_de_travaux_me_delete_pj")
     */
    public function deletePiecejointe($id,$identifiant, Request $request): Response
    {
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        // On supprime l'entrée de la base
        $doc = $this->repoDocs->findOneBy(['id' => $id, 'identifiant' => $identifiant]);
        unlink($this->getParameter('doc_lhermitte').'/'.$doc->getFile());
        $em = $this->getDoctrine()->getManager();
        $em->remove($doc);
        $em->flush();
        
        return $this->redirectToRoute('app_conduite_de_travaux_me_show', ['id' => $identifiant]);
    }

    /**
     * @Route("/Lhermitte/conduite/de/travaux/me/delete/comment/{id}/{identifiant}", name="app_conduite_de_travaux_me_delete_comment")
     */
    public function deleteComment($id,$identifiant, Request $request): Response
    {
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        // On supprime l'entrée de la base
        $comment = $this->repoComments->findOneBy(['id' => $id]);
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        return $this->redirectToRoute('app_conduite_de_travaux_me_show', ['id' => $identifiant]);
    }

     /**
     * @Route("/Lhermitte/conduite/de/travaux/me/update", name="app_conduite_de_travaux_me_update")
     */
    public function update()
    {
              
        //TODO retirer toutes les piéces qui sont déjà Terminés pour réduire le délai de traitement à revoir le dernier essaie n'a pas fonctionné
        $cmd = null;
        $bl = null;
        $facture = null;
        $piecesSuppl = $this->repoAddPieces->findAll();
        foreach ($piecesSuppl as $value) {
            if ($value->getType() == 2) {
                //$commande = $this->repoConduite->findOneBy(['numCmd' => $value->getNumPiece()]);
                    if ($cmd == null ) {
                        $cmd =  $value->getNumPiece();   
                    }else {
                        $cmd = $cmd . ',' . $value->getNumPiece();
                    }
            }
            if ($value->getType() == 3) {
                //$livraison = $this->repoConduite->findOneBy(['numeroBl' => $value->getNumPiece()]);
                    if ($bl == null) {
                        $bl =  $value->getNumPiece();   
                    }else {
                        $bl = $bl . ',' . $value->getNumPiece();
                    }
            }
            if ($value->getType() == 4) {
                //$fact = $this->repoConduite->findOneBy(['numeroFacture' => $value->getNumPiece()]);
                    if ($facture == null) {
                        $facture =  $value->getNumPiece();   
                    }else {
                        $facture = $facture . ',' . $value->getNumPiece();
                    }
            }
        }

        $termine = null;
        $listFact = $this->repoConduite->findBy(['etat' => 'Termine']);
        foreach ($listFact as $value) {
            if ($termine == null && $value->getNumeroFacture() !== null) {
                $termine = $value->getNumeroFacture();
            }elseif ($termine !== null && $value->getNumeroFacture() !== null){
                $termine = $termine . ',' . $value->getNumeroFacture();
            }
        }

        $pieces = $this->repoMouv->getUpdateMouvConduiteTravaux($cmd,$bl,$facture, $termine);
        $user = $this->repoUser->findOneBy(['pseudo' => 'intranet']);
        foreach ($pieces as $value) {
            $bdd = $this->repoConduite->findOneBy(['entId' => $value['id']]);
            if ($bdd == null) {
               $conduite = new ConduiteDeTravauxMe();
               $conduite->setEntId($value['id'])
                        ->setCodeClient($value['tiers'])
                        ->setNom($value['nom'])
                        ->setDateCmd(new DateTime($value['dateCmd']))
                        ->setNumCmd($value['numCmd'])
                        ->setDateBl(new DateTime($value['dateBl']))
                        ->setSaisiePar($value['utilisateur'])
                        ->setBackgroundColor('#FCB824')
                        ->setTextColor('#FFFFFF')
                        ->setNumeroBl($value['numBl'])
                        ->setDateFacture(new DateTime($value['dateFacture']))
                        ->setNumeroFacture($value['numFacture']);
                        if ($value['delaiDemande']) {
                            $conduite->setDelaiDemande(new DateTime($value['delaiDemande']));
                        }
                        if ($value['delaiAccepte']) {
                            $conduite->setDelaiAccepte(new DateTime($value['delaiAccepte']));
                        }
                        if ($value['delaiReporte']) {
                            $conduite->setDelaiReporte(new DateTime($value['delaiReporte']));
                        }
                        if ($value['affaire']) {
                            $conduite->setAffaire($value['affaire']);
                        }
                $conduite->setModeDeTransport($value['transport'])
                        ->setAdresseLivraison($value['adresseLivraison'])
                        ->setOp($value['op'])
                        ->setEtat('En attente')
                        ->setDureeTravaux(0)
                        ->setUpdatedAt(new DateTime())
                        ->setUpdatedBy($user);

            }else {
                $conduite = $bdd;
                $conduite->setCodeClient($value['tiers'])
                        ->setNom($value['nom'])
                        ->setDateCmd(new DateTime($value['dateCmd']))
                        ->setNumCmd($value['numCmd'])
                        ->setSaisiePar($value['utilisateur'])
                        ->setDateBl(new DateTime($value['dateBl']))
                        ->setNumeroBl($value['numBl'])
                        ->setDateFacture(new DateTime($value['dateFacture']))
                        ->setNumeroFacture($value['numFacture']);
                        if ($value['delaiDemande']) {
                            $conduite->setDelaiDemande(new DateTime($value['delaiDemande']));
                        }
                        if ($value['delaiAccepte']) {
                            $conduite->setDelaiAccepte(new DateTime($value['delaiAccepte']));
                        }
                        if ($value['delaiReporte']) {
                            $conduite->setDelaiReporte(new DateTime($value['delaiReporte']));
                        }
                        if ($value['affaire']) {
                            $conduite->setAffaire($value['affaire']);
                        }
                $conduite->setModeDeTransport($value['transport'])
                        ->setAdresseLivraison($value['adresseLivraison'])
                        ->setOp($value['op'])
                        ->setUpdatedAt(new DateTime())
                        ->setUpdatedBy($user);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($conduite);
            $em->flush();
        }

        $dateDuJour = new DateTime();
        $heure = $dateDuJour->format('H');
        if ($heure >= 20 && $heure < 3) {
            $this->sendMailChantierDansSeptJours();
            $this->sendMailChantierDepasseMaisPasTermine();
        }

        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_conduite_de_travaux_me_nok');
    }

    /**
     * @Route("/Lhermitte/conduite/de/travaux/me/mail/chantier/dans/sept/jours", name="app_conduite_de_travaux_me_send_mail_chantier_dans_sept_jours")
     */
    // envoyer un mail pour les chantiers dans 7 jours
    public function sendMailChantierDansSeptJours(): Response
    {
        
        // envoyer les mails
        $donnees = $this->repoConduite->getDebutChantierDans7Jours();
        $treatementMails = $this->repoMail->findBy(['page' => 'app_conduite_de_travaux_me_nok']);
        $mails = $this->adminEmailController->formateEmailList($treatementMails); 
       
        // envoyer un mail
       $texte = 'Veuillez trouver ci dessous la liste des chantiers qui débutent dans 7 jours : ';
       $html = $this->renderView('mails/conduiteDeTravaux.html.twig', ['donnees' => $donnees, 'texte' => $texte ]);
       $email = (new Email())
       ->from($this->mailEnvoi)
       ->to(...$mails)
       ->subject('Conduite de travaux - Liste des chantiers programmées dans 7 jours')
       ->html($html);
       $this->mailer->send($email);

       return $this->redirectToRoute('app_conduite_de_travaux_me_nok');
    }

    /**
     * @Route("/Lhermitte/conduite/de/travaux/me/mail/chantier/depasse/pas/Termine", name="app_conduite_de_travaux_me_send_mail_chantier_depasse_pas_termine")
     */
    // envoyer un mail pour les chantiers Date de fin dépassé mais Etat non Terminé
    public function sendMailChantierDepasseMaisPasTermine(): Response
    {
        
        // envoyer les mails
        $donnees = $this->repoConduite->getFinDepasseMaisPasTermine();
       
        // envoyer un mail
       $texte = 'Veuillez trouver ci dessous la liste des chantiers dépassé mais pas Terminé : ';
       $html = $this->renderView('mails/conduiteDeTravaux.html.twig', ['donnees' => $donnees, 'texte' => $texte ]);
       $treatementMails = $this->repoMail->findBy(['page' => 'app_conduite_de_travaux_me_nok']);
       $mails = $this->adminEmailController->formateEmailList($treatementMails); 
       $email = (new Email())
       ->from($this->mailEnvoi)
       ->to(...$mails)
       ->subject('Conduite de travaux - Liste des chantiers Dépassés mais pas Terminés')
       ->html($html);
       $this->mailer->send($email);

       return $this->redirectToRoute('app_conduite_de_travaux_me_nok');
    }
}
