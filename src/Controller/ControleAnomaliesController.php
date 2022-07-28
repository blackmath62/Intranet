<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Entity\Main\ControlesAnomalies;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\FouRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Main\CmdRobyDelaiAccepteReporte;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\ControlesAnomaliesRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use App\Repository\Main\CmdRobyDelaiAccepteReporteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class ControleAnomaliesController extends AbstractController
{
    private $mailer;
    private $anomalies;
    private $compta;
    private $articleSrefFermes;
    private $client;
    private $article;
    private $fournisseur;
    private $entete;
    private $cmdRoby;
    private $cmdRobyController;
    private $fscAttachedFileController;
    private $contratCommissionnaireController;
    private $conduiteDeTravauxMeController;
    private $movementBillFscController;
    private $movRepo;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;

    public function __construct(MailListRepository $repoMail, MovementBillFscController $movementBillFscController, MouvRepository $movRepo, ConduiteDeTravauxMeController $conduiteDeTravauxMeController, ContratCommissionnaireController $contratCommissionnaireController ,FscAttachedFileController $fscAttachedFileController, CmdRobyDelaiAccepteReporteController $cmdRobyController, CmdRobyDelaiAccepteReporteRepository $cmdRoby, EntRepository $entete, FouRepository $fournisseur, ArtRepository $article, CliRepository $client, ControleArtStockMouvEfRepository $articleSrefFermes,MailerInterface $mailer, ControlesAnomaliesRepository $anomalies,ControleComptabiliteRepository $compta)
    {
        $this->mailer = $mailer;
        $this->anomalies = $anomalies;
        $this->compta = $compta;
        $this->articleSrefFermes = $articleSrefFermes;
        $this->client = $client;
        $this->article = $article;
        $this->fournisseur = $fournisseur;
        $this->entete = $entete;
        $this->cmdRoby = $cmdRoby;
        $this->cmdRobyController = $cmdRobyController;
        $this->fscAttachedFileController = $fscAttachedFileController;
        $this->contratCommissionnaireController = $contratCommissionnaireController;
        $this->conduiteDeTravauxMeController = $conduiteDeTravauxMeController;
        $this->movRepo = $movRepo;
        $this->movementBillFscController;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        //parent::__construct();
    }

    
    public function lancer_macro(){
        $this->ControlStockDirect();
        return $this->render('controle_anomalies/index.html.twig');
    }
    
    /**
     * @Route("/controle/anomalies", name="app_controle_anomalies")
     */
    public function Show_Anomalies()
    {   
        $anomaliesCount = $this->anomalies->getCountAnomalies();
        $this->MajCmdRobyAccepteReporte();

        return $this->render('controle_anomalies/anomalies.html.twig',[
            'title' => 'Liste des anomalies',
            'anomalies' => $this->anomalies->findAll(),
            'anomaliesCount' => $anomaliesCount,
        ]);
    }
    
    /**
     * @Route("/controle/anomalies/run/script", name="app_controle_anomalies_run")
     */
    Public function Run_Cron(){
        $dateDuJour = new DateTime();
        $jour = $dateDuJour->format('w');
        $d = $dateDuJour->format('d');
        $heure = $dateDuJour->format('H');
        $dateDuJour  = $dateDuJour->format('d-m-Y');
        // envoie d'un mail le 5 de chaque mois
        if ($d == 20) {
            if ($heure >= 8 && $heure < 20) {
                $this->contratCommissionnaireController->sendMail();
            }
        }
        // envoyer les mails conduite de travaux
        $this->conduiteDeTravauxMeController->update();

        if ($this->isWeekend($dateDuJour) == false) {
            $this->ControleClient();
            $this->ControleFournisseur();
            $this->ControleArticle();
            $this->ControlStockDirect(); // j'ai mis Utilisateur
            $this->movementBillFscController->update(); // Envoyer les mails à la référente sur les ventes FSC.
            if ($heure >= 8 && $heure < 20) {
                $this->fscAttachedFileController->majFscOrderListFromDivalto();
            if ($jour == 5 || $jour == 1) {
                    $this->MajCmdRobyAccepteReporte();
                    $this->cmdRobyController->sendMail();
            }
            }
            $this->run_auto_wash();
        }
        $this->addFlash('message', 'Les scripts ont bien été lancés !');
        return $this->redirectToRoute('app_controle_anomalies');
    }
    
    function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }

    public function MajCmdRobyAccepteReporte(){
        $donnees = $this->entete->majCmdsRobyDelaiAccepteReporte();
        for ($lig=0; $lig <count($donnees) ; $lig++) { 
            $id = $donnees[$lig]['Identification'];
            $search = $this->cmdRoby->findOneBy(['identification' => $id]);
            // si elle n'existe pas, on la créér
            if ( empty($search)) {
                $listCmd = new CmdRobyDelaiAccepteReporte();
                $listCmd->setIdentification($donnees[$lig]['Identification']);
                $listCmd->setStatut('en cours ...');
                $listCmd->setCreatedAt(new DateTime);
                $listCmd->setTiers($donnees[$lig]['Tiers']);
                $listCmd->setNom($donnees[$lig]['Nom']);
                $listCmd->setCmd($donnees[$lig]['Cmd']);
                $listCmd->setTel($donnees[$lig]['Tel']);
                $listCmd->setDateCmd(new DateTime($donnees[$lig]['DateCmd']));
                $listCmd->setNotreRef($donnees[$lig]['NotreRef']);
                if ($donnees[$lig]['DelaiAccepte']) {
                    $listCmd->setDelaiAccepte(new DateTime($donnees[$lig]['DelaiAccepte']));
                }else {
                    $listCmd->setDelaiAccepte(null);
                }
                if ($donnees[$lig]['DelaiReporte']) {
                    $listCmd->setDelaiReporte(new DateTime($donnees[$lig]['DelaiReporte']));
                }else {
                    $listCmd->setDelaiReporte(null);
                }
            }elseif(!is_null($search)){
                $listCmd = $search;
                $listCmd->setNotreRef($donnees[$lig]['NotreRef']);
                $listCmd->setTel($donnees[$lig]['Tel']);
                if ($donnees[$lig]['DelaiAccepte']) {
                    $listCmd->setDelaiAccepte(new DateTime($donnees[$lig]['DelaiAccepte']));
                }else {
                    $listCmd->setDelaiAccepte(null);
                }
                if ($donnees[$lig]['DelaiReporte']) {
                    $listCmd->setDelaiReporte(new DateTime($donnees[$lig]['DelaiReporte']));
                }else {
                    $listCmd->setDelaiReporte(null);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($listCmd);
            $em->flush();
        }
       
       

    }
        
    public function ControleFournisseur()
    {
        // Contrôle régime fournisseur sur les piéces        
        $donnees = $this->compta->getSendMailErreurRegimeFournisseur(); // j'ai mis Utilisateur
        $libelle = 'RegimeFournisseur';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle régime fournisseur en fonction du Pays
        $donnees = $this->fournisseur->getControleRegimeFournisseur(); // j'ai mis Utilisateur
        $libelle = 'RegimeFournisseurPays';
        $template = 'mails/sendMailAnomalieRegimePaysFournisseur.html.twig';
        $subject = 'Probléme Régime TVA sur un fournisseur, incohérence avec le pays';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle données générales du fournisseur
        $donnees = $this->fournisseur->SurveillanceFournisseurLhermitteReglStatVrpTransVisaTvaPay(); // j'ai mis Utilisateur
        $libelle = 'DonneeFournisseur';
        $template = 'mails/sendMailAnomalieDonneesFournisseurs.html.twig';
        $subject = 'Erreur ou manquement sur une fiche Fournisseur';
        $this->Execute($donnees, $libelle, $template, $subject); 
    }

    public function ControleArticle(){
        // Anomalies Unité et famille 
        $donnees = $this->article->getControleArt(); // j'ai mis Utilisateur
        $libelle = 'ProblémeArticle';
        $template = 'mails/sendMailAnomalieArticles.html.twig';
        $subject = 'Probléme Article à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Anomalies mauvais article utilisé sur piéce FSC
        $donnees = $this->movRepo->getCheckCodeAndDesArticles();
        $libelle = 'ProblémeArticleSurPiecesFsc';
        $template = 'mails/sendMailAnomalieArticlesPieceFsc.html.twig';
        $subject = 'Probléme Article sur Piece FSC à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Anomalies Régime article sur piéce
        $donnees = $this->articleSrefFermes->getControleRegimeArtOnOrder(); // j'ai mis Utilisateur
        $libelle = 'ProblémeRegimeArticle';
        $template = 'mails/sendMailAnomalieRegimeArticle.html.twig';
        $subject = 'Probléme Régime TVA Article sur piéce à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle la présence d'article ou de sous référence fermée
        $donnees = $this->articleSrefFermes->getControleSaisieArticlesSrefFermes(); // j'ai mis Utilisateur
        $libelle = 'ArticleSrefFerme';
        $template = 'mails/sendMailSaisieArticlesSrefFermes.html.twig';
        $subject = 'Saisie sur un article ou une sous référence article fermé';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle la présence de BL Direct
        $donnees = $this->movRepo->getListBlDirect(); // j'ai mis Utilisateur
        $libelle = 'BlDirectPasNormal';
        $template = 'mails/sendMailBlDirect.html.twig';
        $subject = 'BL Direct qui ne devrait pas exister';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle que toutes les sous références ne sont pas fermées sur un article
        // TODO Pas trés logique plutôt envoyer un fichier Excel comme pour les fermetures qui regroupe produits et sref
        $donnees = $this->article->ControleToutesSrefFermeesArticle(); // j'ai mis Utilisateur
        $libelle = 'ToutesSrefFermeesArticleOuvert';
        $template = 'mails/sendMailControleToutesSrefFermeesArticle.html.twig';
        $subject = 'Toutes les sous références sont fermées un article ouvert';
        $this->Execute($donnees, $libelle, $template, $subject);

        $this->ControlArticleAFermer(); // j'ai mis Utilisateur
        $this->ControlSrefArticleAFermer(); // j'ai mis Utilisateur
    }
    
    public function ControleClient()
    {
        // Contrôle Régime piéce avec régime Client
        $donnees = $this->compta->getSendMailErreurRegimeClient(); // j'ai mis Utilisateur
        $libelle = 'RegimeClient';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);
        
        // Contrôle cohérence pays régime client
        $donnees = $this->client->SendMailProblemePaysRegimeClients(); // j'ai mis Utilisateur
        $libelle = 'RegimeClientPays';
        $template = 'mails/sendMailAnomalieRegimePaysClient.html.twig';
        $subject = 'Probléme Régime TVA sur un client, incohérence avec le pays';
        $this->Execute($donnees, $libelle, $template, $subject);
        
        // Contrôle mise à jour client Phyto
        $donnees = $this->client->SendMailMajCertiphytoClient(); // j'ai mis Utilisateur
        $libelle = 'CertiphytoClient';
        $template = 'mails/sendMailAnomalieMajPhytoClients.html.twig';
        $subject = 'Mise à jour d\'un certiphyto client';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle données générales du client
        $donnees = $this->client->SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay(); // j'ai mis Utilisateur
        $libelle = 'DonneeClient';
        $template = 'mails/sendMailAnomalieDonneesClients.html.twig';
        $subject = 'Erreur ou manquement sur une fiche client';
        $this->Execute($donnees, $libelle, $template, $subject); 
    }

    public function Execute($donnees, $libelle, $template, $subject ){
        
        $dateDuJour = new DateTime();
        //dd($donnees);
        // TODO il existe un cas de figure ou ma macro ne mets rien à jour, même pas l'update, 
        //c'est quand $donnees est vide ! donc je peux encore attendre d'avoir une mise à jour de la updatedAt, car elle n'arrivera jamais
        for ($lig=0; $lig <count($donnees) ; $lig++) { 
            $id = $donnees[$lig]['Identification'];
            $ano = $this->anomalies->findOneBy(['idAnomalie' => $id, 'type' => $libelle]);
            
            // si elle n'existe pas, on la créér
            if ( empty($ano)) {
                
                // créer une nouvelle anomalie
                $createAnomalie = new ControlesAnomalies();

                $createAnomalie->setIdAnomalie($id)
                               ->setUser($donnees[$lig]['Utilisateur'])
                               ->setUpdatedAt($dateDuJour)
                               ->setCreatedAt($dateDuJour)
                               ->setModifiedAt($dateDuJour)
                               ->setType($libelle);
                $em = $this->getDoctrine()->getManager();
                $em->persist($createAnomalie);
                $em->flush();

                // envoyer un mail
                $html = $this->renderView($template, ['anomalie' => $donnees[$lig]]);
                $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($donnees[$lig]['Email'])
                ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                ->html($html);
                $this->mailer->send($email);

                // si elle existe on envoit un mail et on mets à jours la date
            }elseif(!is_null($ano)){
                // mettre la date de modification à jour si ça fait plus de 0 jours que le mail à été envoyé
                $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                if ($datediff > 0) {
                    // envoyer un mail
                    $html = $this->renderView($template, ['anomalie' => $donnees[$lig]]);
                    $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($donnees[$lig]['Email'])
                    ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                    ->html($html);
                    $this->mailer->send($email);

                    $ano->setUser($donnees[$lig]['Utilisateur']);
                    $ano->setUpdatedAt($dateDuJour);
                    $ano->setModifiedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }else{
                    $ano->setUser($donnees[$lig]['Utilisateur']);
                    $ano->setUpdatedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }               
            }
        }
        
    }

    // suppression des anomalies trop vieilles
    public function run_auto_wash(){
        $dateDuJour = new DateTime();
        $controleAno = $this->anomalies->findAll();
        foreach ($controleAno as $key ) {            
            $dateModif = $key->getModifiedAt();
            $datediff = $dateModif->diff($dateDuJour)->format("%a");
            // si l'écart de date entre date début et modif est supérieur à 2 jours on supprime, c'est que le probléme est résolu
            if ($datediff > 1 && $key->getIdAnomalie() != '999999999999'
                              && $key->getIdAnomalie() != '999999999997' 
                              && $key->getIdAnomalie() != '999999999996' 
                              && $key->getType() != 'StockDirect'
                              && $key->getType() != 'SrefArticleAFermer'
                              && $key->getType() != 'ArticleAFermer' ) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($key);
                $em->flush(); 
            }
        }
    }
    
    
    ///////////////////////////////////// SOUS REFERENCES ARTICLES
    //TODO à voir, ça ne fonctionne pas toujours correctement, faire des tests.
    public function ControlSrefArticleAFermer(){
        
        $ano = $this->anomalies->findOneBy(['idAnomalie' => '999999999996', 'type' => 'SrefArticleAFermer']);
        $dateDuJour = new DateTime();
        $metiers = [];
        $MailsList = [];
        $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                $produits = $this->article->getControleArticleAFermer();
                $Srefs = $this->article->getControleSousRefArticleAFermer();
                if ($datediff > 0 && !empty($Srefs) ) {
                    $this->exportSrefArticleAFermer();
                    $ano->setUpdatedAt($dateDuJour)
                        ->setUser('JEROME')
                        ->setModifiedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }else {
                    $ano->setUpdatedAt($dateDuJour)
                        ->setUser('JEROME');
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }
                // Envoyer un mail aux utilisateurs pour les avertirs des fermetures    
            
            if ((!empty($produits) | !empty($Srefs) && $datediff > 0)) {
                
                // On verifie les métiers des articles à fermer pour n'envoyer qu'aux personnes pertinentes
                
                for ($i=0; $i <count($Srefs) ; $i++) { 
                    $metiers[] = $Srefs[$i]['Metier']; 
                }
                $metier = array_values(array_unique($metiers, SORT_REGULAR));
                $EV = in_array('EV',$metier);
                $HP = in_array('HP',$metier);
                $ME = in_array('ME',$metier);
                if ($EV == true | $HP == true) {
                    $MailsList = [
                        new Address('dlouchart@lhermitte.fr'),
                        new Address('clerat@lhermitte.fr'),
                        new Address('ctrannin@lhermitte.fr'),
                        new Address('bgovaere@lhermitte.fr'),
                        new Address('twrazidlo@lhermitte.fr'),
                        new Address('ymalmonte@lhermitte.fr'),
                        new Address('xdupire@lhermitte.fr'),
                        new Address('lleleu@lhermitte.fr'),
                        new Address('rvasset@lhermitte.fr'),
                    ];
                }
                if ($ME == true) {
                    if ($EV == true | $HP == true) {
                        array_push($MailsList, new Address('adeschodt@lhermitte.fr'),
                                               new Address('crichard@lhermitte.fr')
                        );
                    }else {
                        $MailsList = [
                            new Address('adeschodt@lhermitte.fr'),
                            new Address('crichard@lhermitte.fr'),
                        ];
                    }
                }
                
                if ($MailsList) {
                    $html = $this->renderView('mails/sendMailForUsersArticleAFermer.html.twig', ['produits' => $produits, 'Srefs' => $Srefs ]);
                    $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to(...$MailsList)
                    ->cc($this->mailTreatement)
                    ->subject('INFORMATION Articles qui vont être fermés Divalto')
                    ->html($html);
                    $this->mailer->send($email);        
                }
            }       
    }

    private function getDataSrefArticleAFermer($donnees): array
    {
        
        $list = [];
        for ($d=0; $d < count($donnees); $d++) {
                
                $donnee = $donnees[$d];
                
                $list[] = [
                    $donnee['Dos'],
                    $donnee['Ref'],
                    $donnee['Sref1'],
                    $donnee['Sref2'],
                    'Usrd'          
                ];
            }
        return $list;
    }
    
    public function exportSrefArticleAFermer()
    {
        $donnees = $this->article->getControleSousRefArticleAFermer();
        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Article_Sous_Reference');
        // Entête de colonne
        $sheet->getCell('A6')->setValue('DOSSIER');
        $sheet->getCell('B6')->setValue('REFERENCE');
        $sheet->getCell('C6')->setValue('SREFERENCE1');
        $sheet->getCell('D6')->setValue('SREFERENCE2');
        $sheet->getCell('E6')->setValue('CONF');
        $sheet->getCell('F6')->setValue('Anomalies');
        $sheet->getCell('G6')->setValue('Alertes');

        $sheet->getCell('A4')->setValue('Données');

        $spreadsheet->getActiveSheet()->getStyle('A6:G6')
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // Le style de l'entête
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
        // Couleur de DOS, REF, SREF1, SREF2
        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF6600',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("A6:D6")->applyFromArray($styleEntete);
        // Couleur de CONF
        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCC00',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("E6")->applyFromArray($styleEntete);
        // Couleur d'Anomalies
        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF0000',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("F6")->applyFromArray($styleEntete);
        // Couleur d'Alertes
        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("G6")->applyFromArray($styleEntete);
        
        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '969696',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("A4")->applyFromArray($styleEntete);
        
        
        // Increase row cursor after header write
        $sheet->fromArray($this->getDataSrefArticleAFermer($donnees),null, 'A7', true);
        $nomFichier = 'Article_Sous_Reference - Article Sous-référence ( SART )' ;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        $writer->save($temp_file);
        // Return the excel file as an attachment
        $produits = [];
        for ($i=0; $i <count($donnees) ; $i++) { 
            //dd($donnees);
            if ($donnees[$i]['Alerte'] <> NULL | $donnees[$i]['Cmd'] <> NULL | $donnees[$i]['Bl'] <> NULL ) {
                $produits[$i]['Ref'] = $donnees[$i]['Ref'];
                $produits[$i]['Sref1'] = $donnees[$i]['Sref1'];
                $produits[$i]['Sref2'] = $donnees[$i]['Sref2'];
                $produits[$i]['Alerte'] = $donnees[$i]['Alerte'];
                $produits[$i]['Cmd'] = $donnees[$i]['Cmd'];
                $produits[$i]['Bl'] = $donnees[$i]['Bl'];
            }
        }

                $html = $this->renderView('mails/sendMailArticleAFermer.html.twig', ['produits' => $produits ]);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($this->mailTreatement)
                    ->subject('Sous références Article Divalto à fermer')
                    ->html($html)
                    ->attachFromPath($temp_file, $fileName, 'application/msexcel');
                $this->mailer->send($email);  
    }





    //////////////////////////////////// ARTICLES 
    public function ControlArticleAFermer(){
        
        $ano = $this->anomalies->findOneBy(['idAnomalie' => '999999999997', 'type' => 'ArticleAFermer']);
       
        $dateDuJour = new DateTime();
        $dateModif = $ano->getModifiedAt();
        $datediff = $dateModif->diff($dateDuJour)->format("%a");
        $produits = $this->article->getControleArticleAFermer();
        if ($datediff > 0 && !empty($produits) ) {
                $this->exportArticleAFermer($produits);
                $ano->setUpdatedAt($dateDuJour)
                    ->setUser('JEROME')
                    ->setModifiedAt($dateDuJour);
                $em = $this->getDoctrine()->getManager();
                $em->persist($ano);
                $em->flush();
            }else{
                    $ano->setUpdatedAt($dateDuJour);
                    $ano->setUser('JEROME');
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }        
    }

    private function getDataArticleAFermer($donnees): array
    {
        $dateDuJour = new DateTime();
        $date_1 = date_modify($dateDuJour, '-1 Day');

        $list = [];
        for ($d=0; $d < count($donnees); $d++) {
                
                $donnee = $donnees[$d];
                
                $list[] = [
                    $donnee['Dos'],
                    $donnee['Ref'],
                    $date_1->format('d-m-Y')          
                ];

            }
        return $list;
    }
    
    public function exportArticleAFermer($donnees)
    {        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Article');
        // Entête de colonne
        $sheet->getCell('A6')->setValue('DOSSIER');
        $sheet->getCell('B6')->setValue('REFERENCE');
        $sheet->getCell('C6')->setValue('DATEFINVALID');
        $sheet->getCell('D6')->setValue('Anomalies');
        $sheet->getCell('E6')->setValue('Alertes');

        $sheet->getCell('A4')->setValue('Données');

        $spreadsheet->getActiveSheet()->getStyle('A6:E6')
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // Le style de l'entête
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF6600',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("A6:B6")->applyFromArray($styleEntete);

        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCC00',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("C6")->applyFromArray($styleEntete);

        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF0000',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("D6")->applyFromArray($styleEntete);

        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("E6")->applyFromArray($styleEntete);

        $styleEntete = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '969696',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("A4")->applyFromArray($styleEntete);
        
        
        // Increase row cursor after header write
        $sheet->fromArray($this->getDataArticleAFermer($donnees),null, 'A7', true);
        $nomFichier = 'Article - Article ( ART )' ;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        $writer->save($temp_file);
        // Return the excel file as an attachment
        $produits = [];
        for ($i=0; $i <count($donnees) ; $i++) { 
            //dd($donnees);
            if ($donnees[$i]['Alerte'] <> NULL | $donnees[$i]['Cmd'] <> NULL | $donnees[$i]['Bl'] <> NULL ) {
                $produits[$i]['Ref'] = $donnees[$i]['Ref'];
                $produits[$i]['Sref1'] = $donnees[$i]['Sref1'];
                $produits[$i]['Sref2'] = $donnees[$i]['Sref2'];
                $produits[$i]['Alerte'] = $donnees[$i]['Alerte'];
                $produits[$i]['Cmd'] = $donnees[$i]['Cmd'];
                $produits[$i]['Bl'] = $donnees[$i]['Bl'];
            }
        }

                $html = $this->renderView('mails/sendMailArticleAFermer.html.twig', ['produits' => $produits ]);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($this->mailTreatement)
                    ->subject('Article Divalto à fermer')
                    ->html($html)
                    ->attachFromPath($temp_file, $fileName, 'application/msexcel');
                $this->mailer->send($email);  
    }


    public function ControlStockDirect(){
        
        $ano = $this->anomalies->findOneBy(['idAnomalie' => '999999999999', 'type' => 'StockDirect']);
        $dateDuJour = new DateTime();
        $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                if ($datediff > 7) {
                $this->exportStockDirect();
                $ano->setUpdatedAt($dateDuJour);
                $ano->setModifiedAt($dateDuJour);
                $em = $this->getDoctrine()->getManager();
                $em->persist($ano);
                $em->flush();
                }else {
                    $ano->setUpdatedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }
    }

    // Export Excel par metier

    private function getDataStockDirect($mail, $donnees): array
    {
        $list = [];
        for ($d=0; $d < count($donnees); $d++) {
                
            $donnee = $donnees[$d];
            //dd($donnee);
            if ($donnee['Email'] == $mail) {
                
                $list[] = [
                    $donnee['Ref'],
                    $donnee['Sref1'],
                    $donnee['Sref2'],
                    $donnee['Designation'],
                    $donnee['StockDirect'],                
                ];
            }
        }
        return $list;
    }
    
    public function exportStockDirect()
    {
        $donnees = $this->article->getControleStockDirect();
        
        for ($ligCom=0; $ligCom <count($donnees) ; $ligCom++) {
            $mail[$ligCom]['Email'] = $donnees[$ligCom]['Email'];
            $mail[$ligCom]['Email2'] = $donnees[$ligCom]['Email2'];
            $mail[$ligCom]['Email3'] = $donnees[$ligCom]['Email3'];
        }
        $Mails = array_values(array_unique($mail, SORT_REGULAR)); // faire une liste de mail sans doublon
        for ($lig=0; $lig <count($Mails) ; $lig++) { 
            $mail = $Mails[$lig]['Email'];
            $MailsList = [$Mails[$lig]['Email'], new Address($Mails[$lig]['Email2']) ];
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Stock Direct');
        // Entête de colonne
        $sheet->getCell('A5')->setValue('Référence');
        $sheet->getCell('B5')->setValue('Sref1');
        $sheet->getCell('C5')->setValue('Sref2');
        $sheet->getCell('D5')->setValue('Désignation');
        $sheet->getCell('E5')->setValue('Stock Direct');
        
        // Increase row cursor after header write
        $sheet->fromArray($this->getDataStockDirect($mail, $donnees),null, 'A6', true);
        $dernLign = count($this->getDataStockDirect($mail, $donnees)) + 5;

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y') ;
        $nomFichier = 'Stock Direct le ' . $dateTime ;
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
        $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);
        
        $sheet->getStyle("A1:E{$dernLign}")
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        // Espacement automatique sur toutes les colonnes sauf la A
        $sheet->setAutoFilter("A5:E{$dernLign}");
        $sheet->getColumnDimension('A')->setWidth(30, 'pt');
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        // Le style de l'entête
        $styleEntete = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'F17A2B8',
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle("A5:E5")->applyFromArray($styleEntete);
 
        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y') ;
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        $writer->save($temp_file);
        // Return the excel file as an attachment

                $html = $this->renderView('mails/sendMailAnomalieStockDirect.html.twig');
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to(...$MailsList)
                    ->subject('Liste des Stocks Direct de Divalto')
                    ->html($html)
                    ->attachFromPath($temp_file, $fileName, 'application/msexcel');
                $this->mailer->send($email);
        }
  
    }

}
