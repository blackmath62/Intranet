<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Form\AddEmailType;
use App\Form\YearMonthType;
use App\Entity\Main\MailList;
use Symfony\Component\Mime\Email;
use App\Form\AddEmailWithNumberType;
use App\Controller\AdminEmailController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\ComptaAnalytiqueRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @IsGranted("ROLE_COMPTA")
*/

class ComptaAnalytiqueController extends AbstractController
{

    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;
    private$repoAnal;

    public function __construct(ComptaAnalytiqueRepository $repoAnal, AdminEmailController $adminEmailController,MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        $this->adminEmailController = $adminEmailController;
        $this->repoAnal = $repoAnal;

        //parent::__construct();
    }


    /**
     * @Route("compta/compta_analytique", name="app_compta_analytique")
     */
    public function index(Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        $ventes = '';
        $annee = '';
        $mois = '';

        $form = $this->createForm(YearMonthType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        
                        // appel de ma fonction
                        $ventes = $this->getRapport($annee, $mois);
                    }

                    // form pour gérer la liste des mails d'envois
                    unset($formMails);
                    $formMails = $this->createForm(AddEmailWithNumberType::class);
                    $formMails->handleRequest($request);
                    if($formMails->isSubmitted() && $formMails->isValid()){
                        $find = $this->repoMail->findBy(['email' => $formMails->getData()['email'], 'page' => $tracking]);
                        if (empty($find) | is_null($find)) {
                            $mail = new MailList();
                            $mail->setCreatedAt(new DateTime())
                                ->setEmail($formMails->getData()['email'])
                                ->setSecondOption($formMails->getData()['SecondOption'])
                                ->setPage($tracking);
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($mail);
                            $em->flush();
                            $this->addFlash('message', 'le mail a été ajouté avec succés !');
                        }else {
                            $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                            return $this->redirectToRoute('app_compta_analytique');
                        }
                    }  
        return $this->render('compta_analytique/index.html.twig', [
            'ventes' => $ventes,
            'annee' => $annee,
            'mois' => $mois,
            'title' => 'Compta Analytique par mois',
            'monthYear' => $form->createView(),
            'formMails' => $formMails->createView(),
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            ]);
    }

    // créer une fonction avec l'export qui sera aussi utilisé pour la génération du fichier Excel
    public function getRapport($annee, $mois){
        $exportVentes = $this->repoAnal->getRapportClient($annee, $mois);
        // exportation des ventes
        $ventes = [];
        for ($lig=0; $lig <count($exportVentes) ; $lig++) { 
                            $regime = "";
                            $port = 0;
                            $transport = 0;
                            $achat = [];
                            
                            $ventes[$lig]['Facture'] = $exportVentes[$lig]['Facture'];
                            $ventes[$lig]['Ref'] = $exportVentes[$lig]['Ref'];
                            $ventes[$lig]['Sref1'] = $exportVentes[$lig]['Sref1'];
                            $ventes[$lig]['Sref2'] = $exportVentes[$lig]['Sref2'];
                            $ventes[$lig]['Designation'] = $exportVentes[$lig]['Designation'];
                            $ventes[$lig]['Uv'] = $exportVentes[$lig]['Uv'];
                            $ventes[$lig]['Op'] = $exportVentes[$lig]['Op'];
                            $ventes[$lig]['Article'] = $exportVentes[$lig]['Article'];
                            $ventes[$lig]['Client'] = $exportVentes[$lig]['Client'];
                            $ventes[$lig]['QteSign'] = $exportVentes[$lig]['qteVtl'];
                            $ventes[$lig]['CoutRevient'] = $exportVentes[$lig]['CoutRevient'];
                            $ventes[$lig]['CoutMoyenPondere'] = $exportVentes[$lig]['CoutMoyenPondere'];
                            $ventes[$lig]['type'] = '';
                            $ventes[$lig]['color'] = '';
                            $ventes[$lig]['estimation'] = '';
                            $ventes[$lig]['estimationTotal'] = '';
                            // rapprocher les achats
                            $achat = $this->repoAnal->getRapportFournisseurAvecSref(
                                        $exportVentes[$lig]['VentAss'], 
                                        $exportVentes[$lig]['Ref'], 
                                        $exportVentes[$lig]['Sref1'], 
                                        $exportVentes[$lig]['Sref2']);
                            $pa = 0;
                            /*if ($ventes[$lig]['Ref'] == 'EVD3073') {
                                dd($achat);
                            }*/
                            if ($achat)
                                {$pa = $achat['pa'];}
                            $ventes[$lig]['Cma'] = $pa;
                            $crt = 0;
                            $cmpt = 0;
                            $cmat = 0;
                            if ($exportVentes[$lig]['CoutRevient'] <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $crt  = abs($exportVentes[$lig]['qteVtl']) * $exportVentes[$lig]['CoutRevient']; }
                            $ventes[$lig]['TotalCoutRevient'] = $crt;
                            if ($exportVentes[$lig]['CoutMoyenPondere'] <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $cmpt  = abs($exportVentes[$lig]['qteVtl']) * $exportVentes[$lig]['CoutMoyenPondere']; }
                            $ventes[$lig]['TotalCoutMoyenPondere'] = $cmpt;
                            if ($pa <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $cmat  = abs($exportVentes[$lig]['qteVtl']) * $pa; }
                            $ventes[$lig]['TotalCoutCma'] = $cmat;
                            if ($achat['regimePiece']) {
                                $regime = $achat['regimePiece'];
                            }else {
                                $regime = $exportVentes[$lig]['regimeFou'];
                            }
                            if ($regime == 0) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'];
                            }elseif ($regime == 1) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'] + 10000;
                            }elseif ($regime == 2) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'] + 20000;
                            }
                            $ventes[$lig]['CompteAchat'] = $compteAchat;
                            if ($achat['pinoFou']) {
                                // ramener la somme des montants du transport sur cette piéce
                                $port = $this->repoAnal->getTransportFournisseur($achat['pinoFou'], $exportVentes[$lig]['Article']);
                                if ($port['montant'] > 0 && $port['montant'] <> 'null' && $cmat > 0) {
                                    // ramener le détail de la piéce fournisseur
                                    $ventes[$lig]['type'] = 'truck';
                                    $ventes[$lig]['color'] = 'secondary';
                                    $transport = $this->repoAnal->getDetailPieceFournisseur($achat['pinoFou']);
                                    // La quantité pour les produits qui ne sont pas des articles de transport
                                    $estim = $this->repoAnal->getQteHorsPortFournisseur($achat['pinoFou']);
                                    if ($estim['qte'] > 0 && $port['montant'] > 0) {
                                        try {
                                            $ventes[$lig]['estimation'] = ($port['montant'] / $estim['qte']);
                                        } catch (Exception $e) {
                                            echo 'Exception reçue : ',  $e->getMessage() . $port['montant'] . ' - ' . $estim['qte'], "\n";
                                        }
                                        if ($exportVentes[$lig]['qteVtl'] <> 0 ) {
                                            $ventes[$lig]['estimationTotal'] = $exportVentes[$lig]['qteVtl'] * ($port['montant']/$estim['qte']);
                                        }
                                    }
                                }elseif (($port['montant'] == 0 | $port['montant'] == 'null') && $cmat > 0) {
                                    // ramener le détail de la piéce fournisseur
                                    $ventes[$lig]['type'] = 'dollar-sign';
                                    $ventes[$lig]['color'] = 'warning';
                                    $transport = $this->repoAnal->getDetailPieceFournisseur($achat['pinoFou']);
                                }
                            }
                            if ($cmat) {
                                $ventes[$lig]['prixRetenu'] = $cmat;
                            }elseif ($cmpt) {
                                $ventes[$lig]['prixRetenu'] = $cmpt;
                            }elseif ($crt) {
                                $ventes[$lig]['prixRetenu'] = $crt;
                            }else {
                                $ventes[$lig]['prixRetenu'] = 0;
                            }
                            $ventes[$lig]['DetailFacture'] = [];
                            $ventes[$lig]['DetailFacture'] = $transport;
                            unset($achat);
                            
                        }
                        return $ventes;
    }     
     
    /**
     * @Route("compta/compta_analytique/send/mail", name="app_compta_analytique_send_mail")
     */
    public function sendMail(): Response
    {
       // jour actuel, n'envoyer que si le jour est bien celui choisi par les utilisateurs
       $d = new DateTime();
       $day = $d->format('d');
       $now = date("Y-m-d");
       $mois = date("m", strtotime($now."- 1 months"));
       $annee = date("Y", strtotime($now."- 1 months"));
       // envoyer un mail
       $treatementMails = $this->repoMail->findBy(['page' => 'app_compta_analytique', 'SecondOption' => $day]);
       if ($treatementMails) {

           $mails = $this->adminEmailController->formateEmailList($treatementMails); 
           $excel = $this->get_states_excel_metier($annee, $mois, 'mail');
           $html = $this->renderView('mails/sendMailComptaAnalytique.html.twig');
           $email = (new Email())
           ->from($this->mailEnvoi)
           ->to(...$mails)
           ->subject('Compta Analytique')
           ->html($html)
           ->attachFromPath($excel);
           $this->mailer->send($email);
           unlink($excel);
        }

       return $this->redirectToRoute('app_compta_analytique');
    }
    
    /**
     * @Route("compta/compta_analytique/export/excel/{annee}/{mois}/{type}", name="app_compta_analytique_export_excel")
     */ 
 
     public function get_states_excel_metier($annee, $mois, $type){
 
        //$request = new Request();
        // tracking user page for stats
         //$tracking = $request->attributes->get('_route');
         //$this->setTracking($tracking);
         
         ini_set('memory_limit', '1024M');
         ini_set('max_execution_time', 0);
 
         $spreadsheet = new Spreadsheet();
         
         $sheet = $spreadsheet->getActiveSheet();
         
         $sheet->setTitle('detail');
         // Entête de colonne
         $sheet->getCell('A5')->setValue('Facture');
         $sheet->getCell('B5')->setValue('Ref');
         $sheet->getCell('C5')->setValue('Sref1');
         $sheet->getCell('D5')->setValue('Sref2');
         $sheet->getCell('E5')->setValue('Désignation');
         $sheet->getCell('F5')->setValue('Uv');
         $sheet->getCell('G5')->setValue('OP');
         $sheet->getCell('H5')->setValue('CR');
         $sheet->getCell('I5')->setValue('CMP');
         $sheet->getCell('J5')->setValue('CA');
         $sheet->getCell('K5')->setValue('Article');
         $sheet->getCell('L5')->setValue('Client');
         $sheet->getCell('M5')->setValue('Compte Achat');
         $sheet->getCell('N5')->setValue('Qte Vendu');
         $sheet->getCell('O5')->setValue('Total CR');
         $sheet->getCell('P5')->setValue('Total CMP');
         $sheet->getCell('Q5')->setValue('Total CA');
         $sheet->getCell('R5')->setValue('Total Proposé');
         $sheet->getCell('S5')->setValue('Port total estimé');
         $sheet->getCell('T5')->setValue('Pu Corrigé');
         $sheet->getCell('U5')->setValue('Total Corrigé');
         $sheet->getCell('V5')->setValue('Port Corrigé');
         $sheet->getCell('W5')->setValue('Total Port Corrigé');

         // Increase row cursor after header write
         $sheet->fromArray($this->getDataDetail($annee, $mois),null, 'A6', true);
         $dernLign = count($this->getDataDetail($annee, $mois)) + 5;
         
         $sheet->setCellValue("O4", "=SUBTOTAL(9,O6:O{$dernLign})"); // Total CR
         $sheet->setCellValue("P4", "=SUBTOTAL(9,P6:P{$dernLign})"); // Total CMP
         $sheet->setCellValue("Q4", "=SUBTOTAL(9,Q6:Q{$dernLign})"); // Total CA
         $sheet->setCellValue("R4", "=SUBTOTAL(9,R6:R{$dernLign})"); // Total proposé
         $sheet->setCellValue("S4", "=SUBTOTAL(9,S6:S{$dernLign})"); // Total port Estimé
         $sheet->setCellValue("T4", "=SUBTOTAL(9,T6:T{$dernLign})"); // Total Modification
         $sheet->setCellValue("U4", "=SUBTOTAL(9,U6:U{$dernLign})"); // Total Corrigé
         $sheet->setCellValue("V4", "=SUBTOTAL(9,V6:V{$dernLign})"); // Port Corrigé
         $sheet->setCellValue("W4", "=SUBTOTAL(9,W6:W{$dernLign})"); // Total port Corrigé

         for ($j=6; $j <= $dernLign ; $j++) { 
            $sheet->setCellValue("U" . $j ,"=IF(T" . $j . "<>0,T" . $j . "*N" . $j . ",R" . $j . " )"); // Total Corrigé
            $sheet->setCellValue("W" . $j ,"=IF(V" . $j . "<>0,V" . $j . ",S" . $j . " )"); // Total port Corrigé
         }
         
         $d = new DateTime('NOW');
         $dateTime = $d->format('d-m-Y') ;
         $nomFichier = 'Compta Analytique détail du ' . $mois . '-' . $annee . ' le '. $dateTime ;
         // Titre de la feuille
         $sheet->getCell('A1')->setValue($nomFichier);
         $sheet->getCell('A1')->getStyle()->getFont()->setSize(20);
         $sheet->getCell('A1')->getStyle()->getFont()->setUnderline(true);
         // Le style du tableau
         $styleArray = [
             'font' => [
                 'bold' => false,
             ],
             'alignment' => [
                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
             ],
             'borders' => [
                 'allBorders' => [
                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 ],
             ],
             'fill' => [
                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                 'startColor' => [
                     'argb' => 'FFFFFFFF',
                 ],
             ],
         ];
         $spreadsheet->getActiveSheet()->getStyle("A5:W{$dernLign}")->applyFromArray($styleArray);
         // Le style cr en BLEU CLAIR
         $spreadsheet->getActiveSheet()->getStyle("H6:H{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('bee5eb');
         $spreadsheet->getActiveSheet()->getStyle("O4:O{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('bee5eb');
         // Le style cmp en JAUNE
         $spreadsheet->getActiveSheet()->getStyle("I6:I{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('ffeeba');
         $spreadsheet->getActiveSheet()->getStyle("P4:P{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('ffeeba');
         // Le style CA en vERT CLAIR
         $spreadsheet->getActiveSheet()->getStyle("J6:J{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('c3e6cb');
         $spreadsheet->getActiveSheet()->getStyle("Q4:Q{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('c3e6cb');
         // Le style qTE en ROUGE
         $spreadsheet->getActiveSheet()->getStyle("N6:N{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('f5c6cb');
         // Le style TOTAL RETENU et port estimé en BLEU FONCE
         $spreadsheet->getActiveSheet()->getStyle("R4:R{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('b8daff');
         $spreadsheet->getActiveSheet()->getStyle("S4:S{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('b8daff');
         // Le style Champs modifiable en rose CLAIR
         $spreadsheet->getActiveSheet()->getStyle("T4:T{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('FBD5ED');
         $spreadsheet->getActiveSheet()->getStyle("V4:V{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('FBD5ED');
         // Le style totaux modifiés en violet CLAIR
         $spreadsheet->getActiveSheet()->getStyle("U4:U{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('C9A7E3');
         $spreadsheet->getActiveSheet()->getStyle("W4:W{$dernLign}")->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()->setARGB('C9A7E3');
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
                     'argb' => '9B5BCA',
                 ],
             ],
         ];
         
         $spreadsheet->getActiveSheet()->getStyle("A5:W5")->applyFromArray($styleEntete);

         // Le style de l'entête
        $styleEnteteBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];        
        $spreadsheet->getSheetByName('detail')->getStyle("O4:W4")->applyFromArray($styleEnteteBorders);
         
         $sheet->getStyle("F1:W{$dernLign}")
         ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         // Espacement automatique sur toutes les colonnes sauf la A
         $sheet->setAutoFilter("A5:W{$dernLign}");
         $sheet->getColumnDimension('A')->setWidth(10, 'pt');
         $sheet->getColumnDimension('B')->setAutoSize(true);
         $sheet->getColumnDimension('C')->setAutoSize(true);
         $sheet->getColumnDimension('D')->setAutoSize(true);
         $sheet->getColumnDimension('E')->setAutoSize(true);
         $sheet->getColumnDimension('F')->setAutoSize(true);
         $sheet->getColumnDimension('G')->setAutoSize(true);
         $sheet->getColumnDimension('H')->setAutoSize(true);
         $sheet->getColumnDimension('I')->setAutoSize(true);
         $sheet->getColumnDimension('J')->setAutoSize(true);
         $sheet->getColumnDimension('K')->setAutoSize(true);
         $sheet->getColumnDimension('L')->setAutoSize(true);
         $sheet->getColumnDimension('M')->setAutoSize(true);
         $sheet->getColumnDimension('N')->setAutoSize(true);
         $sheet->getColumnDimension('O')->setAutoSize(true);
         $sheet->getColumnDimension('P')->setAutoSize(true);
         $sheet->getColumnDimension('Q')->setAutoSize(true);
         $sheet->getColumnDimension('R')->setAutoSize(true);
         $sheet->getColumnDimension('S')->setAutoSize(true);
         $sheet->getColumnDimension('T')->setAutoSize(true);
         $sheet->getColumnDimension('U')->setAutoSize(true);
         $sheet->getColumnDimension('V')->setAutoSize(true);
         $sheet->getColumnDimension('W')->setAutoSize(true);


         // Format nombre € colonne montant
        $sheet->getStyle("H4:J" . $dernLign)
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
         
         // Format nombre € colonne montant Totaux
        $sheet->getStyle("O4:W" . $dernLign)
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
        // figer les volets 
        $spreadsheet->getSheetByName('detail')->freezePane('H6');

         // Mise en place d'une protection sur le fichier pour éviter les erreurs d'intentions
        $protection = $spreadsheet->getActiveSheet()->getProtection();
        $protection->setPassword('intranet');
        $protection->setSheet(true);
        $protection->setSort(true); // PROTECTION CONTRE LE TRIE
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
         // ne pas protéger la colonne permettant de corriger
        $spreadsheet->getSheetByName('detail')->getStyle('T6:T' . $dernLign)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $spreadsheet->getSheetByName('detail')->getStyle('V6:V' . $dernLign)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

         // Create a new worksheet called "My Data"
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'resume');

        // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
        $spreadsheet->addSheet($myWorkSheet, 0);

        $sheetResume = $spreadsheet->getSheetByName('resume');

         // Entête de colonne
         $sheetResume->getCell('B5')->setValue('Compte Achat');
         $sheetResume->getCell('C5')->setValue('Article');
         $sheetResume->getCell('D5')->setValue('Client');
         $sheetResume->getCell('E5')->setValue('Montant Initial');
         $sheetResume->getCell('F5')->setValue('Montant Modifié');
         $sheetResume->getCell('G5')->setValue('Montant Port Initial');
         $sheetResume->getCell('H5')->setValue('Montant Port Modifié');
        
         $sheetResume->fromArray($this->getDataCompte($annee, $mois),null, 'B6', true);
         $dernLignResume = count($this->getDataCompte($annee, $mois)) + 5;

         $nomFichierResume = 'Compta Analytique résumé du ' . $mois . '-' . $annee . ' le '. $dateTime ;

         $sheetResume->setCellValue("E4", "=SUBTOTAL(9,E6:E{$dernLign})"); // Total Initial
         $sheetResume->setCellValue("F4", "=SUBTOTAL(9,F6:F{$dernLign})"); // Total Corrigé
         $sheetResume->setCellValue("G4", "=SUBTOTAL(9,G6:G{$dernLign})"); // Total port Estimé
         $sheetResume->setCellValue("H4", "=SUBTOTAL(9,H6:H{$dernLign})"); // Total port Corrigé

         // Titre de la feuille
         $sheetResume->getCell('A1')->setValue($nomFichierResume);
         $sheetResume->getCell('A1')->getStyle()->getFont()->setSize(20);
         $sheetResume->getCell('A1')->getStyle()->getFont()->setUnderline(true);

         
         // Le style du tableau
         $styleArrayCompte = [
            'font' => [
                'bold' => false,
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
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $spreadsheet->getSheetByName('resume')->getStyle("B5:H{$dernLignResume}")->applyFromArray($styleArrayCompte);
        
        // Le style MONTANT INITIAL en BLEU foncé
       $spreadsheet->getSheetByName('resume')->getStyle("E4:E{$dernLignResume}")->getFill()
       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
       ->getStartColor()->setARGB('b8daff');
       $spreadsheet->getSheetByName('resume')->getStyle("G4:G{$dernLignResume}")->getFill()
       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
       ->getStartColor()->setARGB('b8daff');
       // Le style MONTANT MODIFIE en Violet clair
       $spreadsheet->getSheetByName('resume')->getStyle("F4:F{$dernLignResume}")->getFill()
       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
       ->getStartColor()->setARGB('C9A7E3');
       $spreadsheet->getSheetByName('resume')->getStyle("H4:H{$dernLignResume}")->getFill()
       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
       ->getStartColor()->setARGB('C9A7E3');

        // Le style de l'entête
        $styleEnteteCompteBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        
        $spreadsheet->getSheetByName('resume')->getStyle("E4:H4")->applyFromArray($styleEnteteCompteBorders);

        // Le style de l'entête
        $styleEnteteCompte = [
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
                    'argb' => '9B5BCA',
                ],
            ],
        ];
        
        $spreadsheet->getSheetByName('resume')->getStyle("B5:H5")->applyFromArray($styleEnteteCompte);

        // Le style de l'entête
        $styleTotauxCompte = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];
        $spreadsheet->getSheetByName('resume')->getStyle("B4:H4")->applyFromArray($styleTotauxCompte);

        $sheetResume->getColumnDimension('B')->setAutoSize(true);
        $sheetResume->getColumnDimension('C')->setAutoSize(true);
        $sheetResume->getColumnDimension('D')->setAutoSize(true);
        $sheetResume->getColumnDimension('E')->setAutoSize(true);
        $sheetResume->getColumnDimension('F')->setAutoSize(true);
        $sheetResume->getColumnDimension('G')->setAutoSize(true);
        $sheetResume->getColumnDimension('H')->setAutoSize(true);
        
        for ($i=6; $i <= $dernLignResume ; $i++) { 
            $sheetResume->setCellValue("E" . $i, "=SUMIFS(detail!R:R,detail!M:M,B" . $i . ",detail!K:K,C" . $i . ",detail!L:L,D" . $i . ")"); // Montant Initial
            $sheetResume->setCellValue("F" . $i, "=SUMIFS(detail!U:U,detail!M:M,B" . $i . ",detail!K:K,C" . $i . ",detail!L:L,D" . $i . ")"); // Montant Corrigé
            $sheetResume->setCellValue("G" . $i, "=SUMIFS(detail!S:S,detail!M:M,B" . $i . ",detail!K:K,C" . $i . ",detail!L:L,D" . $i . ")"); // Montant port estimé
            $sheetResume->setCellValue("H" . $i, "=SUMIFS(detail!W:W,detail!M:M,B" . $i . ",detail!K:K,C" . $i . ",detail!L:L,D" . $i . ")"); // Montant port Corrigé
        }

        // Format nombre € colonne montant
        $sheetResume->getStyle("E4:H" . $dernLignResume)
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

        // Mise en place d'une protection sur le fichier pour éviter les erreurs d'intentions
        $protection = $spreadsheet->getSheetByName('resume')->getProtection();
        $protection->setPassword('intranet');
        $protection->setSheet(true);
        $protection->setSort(true); // PROTECTION CONTRE LE TRIE
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
         
               $writer = new Xlsx($spreadsheet);
               // Create a Temporary file in the system
               $fileName = $nomFichier . '.xlsx';
               // Return the excel file as an attachment
        if ($type == "download") { 
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);
            return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
        }elseif ($type == "mail") {
            
            $chemin = 'doc/Compta Analytique/';
            $fichier = $chemin . '/' . $fileName;
            $writer->save($fichier);
            return $fichier;
        }
 
     }

     // générer un fichier Excel qui sera envoyé par mail aux adresses renseignées
     public function getDataDetail($annee, $mois): array
     {
         /**
          * @var $ticket Ticket[]
          */
         ini_set('memory_limit', '-1');
         ini_set('max_execution_time', 0);
         $list = [];
         $donnees = [];
         
         $donnees = $this->getRapport($annee, $mois);
         // désactiver le Garbage Collector
         gc_disable();      
         for ($d=0; $d < count($donnees); $d++) {
                 
             $donnee = $donnees[$d];
             if ($donnee['estimationTotal']) {
                 $port = $donnee['estimationTotal'];
             }else {
                $port = 0 ;
             }
 
             $list[] = [
                 $donnee['Facture'],
                 $donnee['Ref'],
                 $donnee['Sref1'],
                 $donnee['Sref2'],
                 $donnee['Designation'],
                 $donnee['Uv'],
                 $donnee['Op'],
                 $donnee['CoutRevient'],
                 $donnee['CoutMoyenPondere'],
                 $donnee['Cma'],
                 $donnee['Article'],
                 $donnee['Client'],
                 $donnee['CompteAchat'],
                 $donnee['QteSign'],
                 $donnee['TotalCoutRevient'],
                 $donnee['TotalCoutMoyenPondere'],  
                 $donnee['TotalCoutCma'],
                 $donnee['prixRetenu'],  
                 $port,                       
             ];
             // lancement manuel du Garbage Collector pour libérer de la mémoire
             gc_collect_cycles();
         } 
         return $list;
     }

     // générer un fichier Excel qui sera envoyé par mail aux adresses renseignées
     public function getDataCompte($annee, $mois): array
     {
         /**
          * @var $ticket Ticket[]
          */
         ini_set('memory_limit', '-1');
         ini_set('max_execution_time', 0);
         $listCompte = [];
         $donnees = [];
         
         $donnees = $this->getRapport($annee, $mois);
         // désactiver le Garbage Collector
         gc_disable();      
         for ($d=0; $d < count($donnees); $d++) {
                 
             $donnee = $donnees[$d];

             $listCompte[] = [
                $donnee['CompteAchat'],
                $donnee['Article'],
                $donnee['Client']
             ];
             // lancement manuel du Garbage Collector pour libérer de la mémoire
             gc_collect_cycles();
         }
         // Increase row cursor after header write
         $compteArticleClient = array_values(array_unique($listCompte, SORT_REGULAR)); // faire une liste des comptes, Articles, Clients sans doublon 
         return $compteArticleClient;
     }

}
