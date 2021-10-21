<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Entity\Main\ControlesAnomalies;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\FouRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\ControlesAnomaliesRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ControleAnomaliesController extends AbstractController
{
    private $mailer;
    private $anomalies;
    private $compta;
    private $articleSrefFermes;
    private $client;
    private $article;
    private $fournisseur;

    public function __construct(FouRepository $fournisseur, ArtRepository $article, CliRepository $client, ControleArtStockMouvEfRepository $articleSrefFermes,MailerInterface $mailer, ControlesAnomaliesRepository $anomalies,ControleComptabiliteRepository $compta)
    {
        $this->mailer = $mailer;
        $this->anomalies = $anomalies;
        $this->compta = $compta;
        $this->articleSrefFermes = $articleSrefFermes;
        $this->client = $client;
        $this->article = $article;
        $this->fournisseur = $fournisseur;
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
        return $this->render('controle_anomalies/anomalies.html.twig',[
            'title' => 'Liste des anomalies',
            'anomalies' => $this->anomalies->findAll(),
        ]);
    }
    
    /**
     * @Route("/controle/anomalies/run/script", name="app_controle_anomalies_run")
     */
    Public function Run_Cron(){
        $dateDuJour = new DateTime();
        $dateDuJour  = $dateDuJour->format('d-m-Y');

        if ($this->isWeekend($dateDuJour) == false) {
            $this->ControleClient();
            $this->ControleFournisseur();
            $this->ControleArticle();
            $this->ControlStockDirect();
            $this->run_auto_wash();
        }
        $this->addFlash('message', 'Les scripts ont bien été lancés !');
        return $this->redirectToRoute('app_controle_anomalies');
    }
    
    function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }
        
    public function ControleFournisseur()
    {
        // Contrôle régime fournisseur sur les piéces        
        $donnees = $this->compta->getSendMailErreurRegimeFournisseur();
        $libelle = 'RegimeFournisseur';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle régime fournisseur en fonction du Pays
        $donnees = $this->fournisseur->getControleRegimeFournisseur();
        $libelle = 'RegimeFournisseurPays';
        $template = 'mails/sendMailAnomalieRegimePaysFournisseur.html.twig';
        $subject = 'Probléme Régime TVA sur un fournisseur, incohérence avec le pays';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle données générales du fournisseur
        $donnees = $this->fournisseur->SurveillanceFournisseurLhermitteReglStatVrpTransVisaTvaPay();
        $libelle = 'DonneeFournisseur';
        $template = 'mails/sendMailAnomalieDonneesFournisseurs.html.twig';
        $subject = 'Erreur ou manquement sur une fiche Fournisseur';
        $this->Execute($donnees, $libelle, $template, $subject); 
    }

    public function ControleArticle(){
        // Anomalies Unité et famille 
        $donnees = $this->article->getControleArt();
        $libelle = 'ProblémeArticle';
        $template = 'mails/sendMailAnomalieArticles.html.twig';
        $subject = 'Probléme Article à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle la présence d'article ou de sous référence fermée
        $dossier = 1;
        $donnees = $this->articleSrefFermes->getControleSaisieArticlesSrefFermes($dossier);
        $libelle = 'ArticleSrefFerme';
        $template = 'mails/sendMailSaisieArticlesSrefFermes.html.twig';
        $subject = 'Saisie sur un article ou une sous référence article fermé';
        $this->Execute($donnees, $libelle, $template, $subject);
    }
    
    public function ControleClient()
    {
        // Contrôle Régime piéce avec régime Client
        $donnees = $this->compta->getSendMailErreurRegimeClient();
        $libelle = 'RegimeClient';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);
        
        // Contrôle cohérence pays régime client
        $donnees = $this->client->SendMailProblemePaysRegimeClients();
        $libelle = 'RegimeClientPays';
        $template = 'mails/sendMailAnomalieRegimePaysClient.html.twig';
        $subject = 'Probléme Régime TVA sur un client, incohérence avec le pays';
        $this->Execute($donnees, $libelle, $template, $subject);
        
        // Contrôle mise à jour client Phyto
        $donnees = $this->client->SendMailMajCertiphytoClient();
        $libelle = 'CertiphytoClient';
        $template = 'mails/sendMailAnomalieMajPhytoClients.html.twig';
        $subject = 'Mise à jour d\'un certiphyto client';
        $this->Execute($donnees, $libelle, $template, $subject);

        // Contrôle données générales du client
        $donnees = $this->client->SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay();
        $libelle = 'DonneeClient';
        $template = 'mails/sendMailAnomalieDonneesClients.html.twig';
        $subject = 'Erreur ou manquement sur une fiche client';
        $this->Execute($donnees, $libelle, $template, $subject); 
    }

    public function Execute($donnees, $libelle, $template, $subject ){
        
        $dateDuJour = new DateTime();
        //dd($donnees);
        for ($lig=0; $lig <count($donnees) ; $lig++) { 
            $id = $donnees[$lig]['Identification'];
            $ano = $this->anomalies->findOneBy(['idAnomalie' => $id, 'type' => $libelle]);
            
            // si elle n'existe pas, on la créér
            if ( empty($ano)) {
                
                // créer une nouvelle anomalie
                $createAnomalie = new ControlesAnomalies();

                $createAnomalie->setIdAnomalie($id)
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
                ->from('intranet@groupe-axis.fr')
                ->to($donnees[$lig]['Email'])
                ->cc('jpochet@lhermitte.fr')
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
                    ->from('intranet@groupe-axis.fr')
                    ->to($donnees[$lig]['Email'])
                    ->cc('jpochet@lhermitte.fr')
                    ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                    ->html($html);
                    $this->mailer->send($email);

                    $ano->setUpdatedAt($dateDuJour);
                    $ano->setModifiedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }else{
                    $ano->setUpdatedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }               
            }
        }
        
    }

    // suppression des anomalies trop vieilles
    /**
     * @Route("/controle/anomalies/TEST", name="app_controle_anomalies_test")
     */ 
    public function run_auto_wash(){
        $dateDuJour = new DateTime();
        $controleAno = $this->anomalies->findAll();
        foreach ($controleAno as $key ) {            
            $dateModif = $key->getModifiedAt();
            $datediff = $dateModif->diff($dateDuJour)->format("%a");
            // si l'écart de date entre date début et modif est supérieur à 2 jours on supprime, c'est que le probléme est résolu
            if ($datediff > 1 && $key->getIdAnomalie() != '999999999999' && $key->getType() != 'StockDirect' ) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($key);
                $em->flush(); 
            }
        }
    }
    
    public function ControlStockDirect(){
        
        $ano = $this->anomalies->findOneBy(['idAnomalie' => '999999999999', 'type' => 'StockDirect']);
        $dateDuJour = new DateTime();
        $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                if ($datediff > 29) {
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

    private function getData($mail, $donnees): array
    {
        $list = [];
        for ($d=0; $d < count($donnees); $d++) {
                
            $donnee = $donnees[$d];
            if ($donnee['Email'] == $mail) {
                
                $list[] = [
                    $donnee['REFERENCE'],
                    $donnee['SREFERENCE1'],
                    $donnee['SREFERENCE2'],
                    $donnee['ARTICLE_DESIGNATION'],
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
        $sheet->fromArray($this->getData($mail, $donnees),null, 'A6', true);
        $dernLign = count($this->getData($mail, $donnees)) + 5;

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
                    ->from('intranet@groupe-axis.fr')
                    ->to(...$MailsList)
                    ->cc('jpochet@lhermitte.fr')
                    ->subject('Liste des Stocks Direct de Divalto')
                    ->html($html)
                    ->attachFromPath($temp_file, $fileName, 'application/msexcel');
                $this->mailer->send($email);
        }
  
    }


}
