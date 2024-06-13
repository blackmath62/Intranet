<?php

namespace App\Controller;

//use App\Command\ContinuousExecutionCommand;
use App\Controller\AdminEmailController;
use App\Controller\ContratCommissionnaireController;
use App\Controller\HolidayController;
use App\Entity\Main\ControlesAnomalies;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\FouRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\CmdRobyDelaiAccepteReporteRepository;
use App\Repository\Main\ControlesAnomaliesRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\UsersRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

#[IsGranted("ROLE_USER")]

class AnomaliesController extends AbstractController
{
    private $entityManager;
    private $mailer;
    private $twig;
    private $adminEmailController;
    private $contratCommissionnaireController;
    private $holidayController;
    private $repoAno;
    private $repoCompta;
    private $repoArtStockMouvEf;
    private $repoCli;
    private $repoArt;
    private $repoFou;
    private $repoEnt;
    private $repoCmdRoby;
    private $repoMouv;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $repoUsers;

    public function __construct(
        ManagerRegistry $registry,
        MailerInterface $mailer,
        Environment $twig,
        HolidayController $holidayController,
        AdminEmailController $adminEmailController,
        ContratCommissionnaireController $contratCommissionnaireController,
        UsersRepository $repoUsers,
        MailListRepository $repoMail,
        MouvRepository $repoMouv,
        CmdRobyDelaiAccepteReporteRepository $repoCmdRoby,
        EntRepository $repoEnt,
        FouRepository $repoFou,
        ArtRepository $repoArt,
        CliRepository $repoCli,
        ControleArtStockMouvEfRepository $repoArtStockMouvEf,
        ControlesAnomaliesRepository $repoAno,
        ControleComptabiliteRepository $repoCompta,
    ) {
        $this->mailer = $mailer;
        $this->repoAno = $repoAno;
        $this->twig = $twig;
        $this->repoCompta = $repoCompta;
        $this->repoArtStockMouvEf = $repoArtStockMouvEf;
        $this->repoCli = $repoCli;
        $this->repoArt = $repoArt;
        $this->repoFou = $repoFou;
        $this->repoEnt = $repoEnt;
        $this->repoCmdRoby = $repoCmdRoby;
        $this->contratCommissionnaireController = $contratCommissionnaireController;
        $this->repoMouv = $repoMouv;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->adminEmailController = $adminEmailController;
        $this->repoUsers = $repoUsers;
        $this->holidayController = $holidayController;
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/anomalies/pieces", name: "app_controle_pieces")]

    public function ControlePieces()
    {
        $donnees = $this->repoMouv->getCmdBlDepot1(2, null);
        foreach ($donnees as $user) {
            $donneesUsers = $this->repoMouv->getCmdBlDepot1(2, $user['mail']);
            if (count($donneesUsers) > 0) {
                $template = 'mails/sendMailAnomaliePiecesDepot1.html.twig';
                // envoyer un mail
                $html = $this->twig->render($template, ['donneesUsers' => $donneesUsers, 'piece' => 'Commandes']);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($user['mail']) // $user['mail']
                    ->subject('Commandes avec Dépôt 1, merci de corriger')
                    ->html($html);
                $this->mailer->send($email);
            }
        }
        $donnees = $this->repoMouv->getCmdBlDepot1(3, null);
        foreach ($donnees as $user) {
            $donneesUsers = $this->repoMouv->getCmdBlDepot1(3, $user['mail']);
            if (count($donneesUsers) > 0) {
                $template = 'mails/sendMailAnomaliePiecesDepot1.html.twig';
                // envoyer un mail
                $html = $this->twig->render($template, ['donneesUsers' => $donneesUsers, 'piece' => 'Bons de livraison']);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($user['mail']) // $user['mail']
                    ->subject('Bons de livraison avec Dépôt 1, merci de corriger')
                    ->html($html);
                $this->mailer->send($email);
            }
        }

    }

    public function ControleRegimeFournisseurPieces()
    {
        // Contrôle régime fournisseur sur les piéces
        $donnees = $this->repoCompta->getSendMailErreurRegimeFournisseur(); // j'ai mis Utilisateur
        $libelle = 'RegimeFournisseur';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleRegimeFournisseurPays()
    {
        // Contrôle régime fournisseur en fonction du Pays
        $donnees = $this->repoFou->getControleRegimeFournisseur(); // j'ai mis Utilisateur
        $libelle = 'RegimeFournisseurPays';
        $template = 'mails/sendMailAnomalieRegimePaysFournisseur.html.twig';
        $subject = 'Probléme Régime TVA sur un fournisseur, incohérence avec le pays';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleGeneralFournisseur()
    {
        // Contrôle données générales du fournisseur
        $donnees = $this->repoFou->SurveillanceFournisseurLhermitteReglStatVrpTransVisaTvaPay(); // j'ai mis Utilisateur
        $libelle = 'DonneeFournisseur';
        $template = 'mails/sendMailAnomalieDonneesFournisseurs.html.twig';
        $subject = 'Erreur ou manquement sur une fiche Fournisseur';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleAnomaliesUniteArticle()
    {
        // Anomalies Unité et famille
        $donnees = $this->repoArt->getControleArt(); // j'ai mis Utilisateur
        $libelle = 'ProblémeArticle';
        $template = 'mails/sendMailAnomalieArticles.html.twig';
        $subject = 'Probléme Article à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleAnomaliesMauvaisArticleFsc()
    {
        // Anomalies mauvais article utilisé sur piéce FSC
        $donnees = $this->repoMouv->getCheckCodeAndDesArticles();
        $libelle = 'ProblémeArticleSurPiecesFsc';
        $template = 'mails/sendMailAnomalieArticlesPieceFsc.html.twig';
        $subject = 'Probléme Article sur Piece FSC à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleAnomaliesRegimeArticlePiece()
    {
        // Anomalies Régime article sur piéce
        $donnees = $this->repoArtStockMouvEf->getControleRegimeArtOnOrder(); // j'ai mis Utilisateur
        $libelle = 'ProblémeRegimeArticle';
        $template = 'mails/sendMailAnomalieRegimeArticle.html.twig';
        $subject = 'Probléme Régime TVA Article sur piéce à corriger';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleAnomaliesSaisieArticleFerme()
    {
        // Contrôle la présence d'article ou de sous référence fermée
        $donnees = $this->repoArtStockMouvEf->getControleSaisieArticlesSrefFermes(); // j'ai mis Utilisateur
        $libelle = 'ArticleSrefFerme';
        $template = 'mails/sendMailSaisieArticlesSrefFermes.html.twig';
        $subject = 'Saisie sur un article ou une sous référence article fermé';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleAnomaliesToutesSrefArticleFerme()
    {
        // Contrôle que toutes les sous références ne sont pas fermées sur un article
        $donnees = $this->repoArt->ControleToutesSrefFermeesArticle(); // j'ai mis Utilisateur
        $libelle = 'ToutesSrefFermeesArticleOuvert';
        $template = 'mails/sendMailControleToutesSrefFermeesArticle.html.twig';
        $subject = 'Toutes les sous références sont fermées un article ouvert';
        $this->Execute($donnees, $libelle, $template, $subject);
    }

    public function ControleRegimeClientPiece()
    {
        // Contrôle Régime piéce avec régime Client
        $donnees = $this->repoCompta->getSendMailErreurRegimeClient(); // j'ai mis Utilisateur
        $libelle = 'RegimeClient';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);
    }
    public function ControleRegimeClientPays()
    {
        // Contrôle cohérence pays régime client
        $donnees = $this->repoCli->SendMailProblemePaysRegimeClients(); // j'ai mis Utilisateur
        $libelle = 'RegimeClientPays';
        $template = 'mails/sendMailAnomalieRegimePaysClient.html.twig';
        $subject = 'Probléme Régime TVA sur un client, incohérence avec le pays';
        $this->Execute($donnees, $libelle, $template, $subject);
    }
    public function ControleAnomalieClientPhyto()
    {
        // Contrôle mise à jour client Phyto
        $donnees = $this->repoCli->SendMailMajCertiphytoClient(); // j'ai mis Utilisateur
        $libelle = 'CertiphytoClient';
        $template = 'mails/sendMailAnomalieMajPhytoClients.html.twig';
        $subject = 'Mise à jour d\'un certiphyto client';
        $this->Execute($donnees, $libelle, $template, $subject);
    }
    public function ControleGeneralClient()
    {
        // Contrôle données générales du client
        $donnees = $this->repoCli->SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay(); // j'ai mis Utilisateur
        $libelle = 'DonneeClient';
        $template = 'mails/sendMailAnomalieDonneesClients.html.twig';
        $subject = 'Erreur ou manquement sur une fiche client';
        $this->Execute($donnees, $libelle, $template, $subject);
    }
    public function Execute($donnees, $libelle, $template, $subject)
    {
        $dateDuJour = new DateTime();
        // TODO il existe un cas de figure ou ma macro ne mets rien à jour, même pas l'update,
        //c'est quand $donnees est vide ! donc je peux encore attendre d'avoir une mise à jour de la updatedAt, car elle n'arrivera jamais
        for ($lig = 0; $lig < count($donnees); $lig++) {
            $id = $donnees[$lig]['Identification'];
            $ano = $this->repoAno->findOneBy(['idAnomalie' => $id, 'type' => $libelle]);

            // si elle n'existe pas, on la créér
            if (empty($ano)) {

                // créer une nouvelle anomalie
                $createAnomalie = new ControlesAnomalies();

                $createAnomalie->setIdAnomalie($id)
                    ->setUser($donnees[$lig]['Utilisateur'])
                    ->setUpdatedAt($dateDuJour)
                    ->setCreatedAt($dateDuJour)
                    ->setModifiedAt($dateDuJour)
                    ->setType($libelle);
                $em = $this->entityManager;
                $em->persist($createAnomalie);
                $em->flush();

                // envoyer un mail
                $html = $this->twig->render($template, ['anomalie' => $donnees[$lig]]);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($donnees[$lig]['Email'])
                    ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                    ->html($html);
                $this->mailer->send($email);

                // si elle existe on envoit un mail et on mets à jours la date
            } elseif (!is_null($ano)) {
                // mettre la date de modification à jour si ça fait plus de 0 jours que le mail à été envoyé
                $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                if ($datediff > 0) {
                    // envoyer un mail
                    $html = $this->twig->render($template, ['anomalie' => $donnees[$lig]]);
                    $email = (new Email())
                        ->from($this->mailEnvoi)
                        ->to($donnees[$lig]['Email'])
                        ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                        ->html($html);
                    $this->mailer->send($email);

                    $ano->setUser($donnees[$lig]['Utilisateur']);
                    $ano->setUpdatedAt($dateDuJour);
                    $ano->setModifiedAt($dateDuJour);
                    $em = $this->entityManager;
                    $em->persist($ano);
                    $em->flush();
                } else {
                    $ano->setUser($donnees[$lig]['Utilisateur']);
                    $ano->setUpdatedAt($dateDuJour);
                    $em = $this->entityManager;
                    $em->persist($ano);
                    $em->flush();
                }
            }
        }
    }

    // suppression des anomalies trop vieilles
    public function run_auto_wash()
    {
        $dateDuJour = new DateTime();
        $controleAno = $this->repoAno->findAll();
        foreach ($controleAno as $key) {
            $dateModif = $key->getModifiedAt();
            $datediff = $dateModif->diff($dateDuJour)->format("%a");
            // si l'écart de date entre date début et modif est supérieur à 2 jours on supprime, c'est que le probléme est résolu
            if ($datediff > 1 && $key->getIdAnomalie() != '999999999999'
                && $key->getIdAnomalie() != '999999999997'
                && $key->getIdAnomalie() != '999999999996'
                && $key->getType() != 'StockDirect'
                && $key->getType() != 'SrefArticleAFermer'
                && $key->getType() != 'ArticleAFermer') {
                $em = $this->entityManager;
                $em->remove($key);
                $em->flush();
            }
        }
    }

    ///////////////////////////////////// SOUS REFERENCES ARTICLES
    //TODO à voir, ça ne fonctionne pas toujours correctement, faire des tests.
    public function ControlSrefArticleAFermer()
    {

        $ano = $this->repoAno->findOneBy(['idAnomalie' => '999999999996', 'type' => 'SrefArticleAFermer']);
        $dateDuJour = new DateTime();
        $metiers = [];
        $MailsList = [];
        $dateModif = $ano->getModifiedAt();
        $datediff = $dateModif->diff($dateDuJour)->format("%a");
        $produits = $this->repoArt->getControleArticleAFermer();
        $Srefs = $this->repoArt->getControleSousRefArticleAFermer();
        if ($datediff > 0 && !empty($Srefs)) {
            $this->exportSrefArticleAFermer();
            $ano->setUpdatedAt($dateDuJour)
                ->setUser('JEROME')
                ->setModifiedAt($dateDuJour);
            $em = $this->entityManager;
            $em->persist($ano);
            $em->flush();
        } else {
            $ano->setUpdatedAt($dateDuJour)
                ->setUser('JEROME');
            $em = $this->entityManager;
            $em->persist($ano);
            $em->flush();
        }
        // Envoyer un mail aux utilisateurs pour les avertirs des fermetures

        if ((!empty($produits) | !empty($Srefs) && $datediff > 0)) {

            // On verifie les métiers des articles à fermer pour n'envoyer qu'aux personnes pertinentes

            for ($i = 0; $i < count($Srefs); $i++) {
                $metiers[] = $Srefs[$i]['Metier'];
            }
            $metier = array_values(array_unique($metiers, SORT_REGULAR));
            $EV = in_array('EV', $metier);
            $HP = in_array('HP', $metier);
            $ME = in_array('ME', $metier);
            if ($ME == false) {
                if (($EV == true | $HP == true)) {
                    $treatementMails = $this->repoUsers->getFindUsersEvHp();
                    //dd($treatementMails);
                    //$commerciauxString = implode(', ', $treatementMails);
                    $MailsList = $this->adminEmailController->formateEmailList($treatementMails);
                }
            }
            if ($ME == true) {
                if ($EV == true | $HP == true) {
                    $treatementMails = $this->repoUsers->getFindUsersEvHpMe();
                    //dd($treatementMails);
                    $MailsList = $this->adminEmailController->formateEmailList($treatementMails);
                } else {
                    $treatementMails = $this->repoUsers->findBy(['me' => 1]);
                    //dd($treatementMails);
                    $MailsList = $this->adminEmailController->formateEmailList($treatementMails);
                }
            }
            if ($MailsList) {
                $html = $this->twig->render('mails/sendMailForUsersArticleAFermer.html.twig', ['produits' => $produits, 'Srefs' => $Srefs]);
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
        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['Dos'],
                $donnee['Ref'],
                $donnee['Sref1'],
                $donnee['Sref2'],
                'Usrd',
            ];
        }
        return $list;
    }

    public function exportSrefArticleAFermer()
    {
        $donnees = $this->repoArt->getControleSousRefArticleAFermer();

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
        $sheet->fromArray($this->getDataSrefArticleAFermer($donnees), null, 'A7', true);
        $nomFichier = 'Article_Sous_Reference - Article Sous-référence ( SART )';
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        $produits = [];
        for ($i = 0; $i < count($donnees); $i++) {
            //dd($donnees);
            if ($donnees[$i]['Alerte'] != null | $donnees[$i]['Cmd'] != null | $donnees[$i]['Bl'] != null) {
                $produits[$i]['Ref'] = $donnees[$i]['Ref'];
                $produits[$i]['Sref1'] = $donnees[$i]['Sref1'];
                $produits[$i]['Sref2'] = $donnees[$i]['Sref2'];
                $produits[$i]['Alerte'] = $donnees[$i]['Alerte'];
                $produits[$i]['Cmd'] = $donnees[$i]['Cmd'];
                $produits[$i]['Bl'] = $donnees[$i]['Bl'];
            }
        }

        $html = $this->twig->render('mails/sendMailArticleAFermer.html.twig', ['produits' => $produits]);
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->mailTreatement)
            ->subject('Sous références Article Divalto à fermer')
            ->html($html)
            ->attachFromPath($temp_file, $fileName, 'application/msexcel');
        $this->mailer->send($email);
    }

    //////////////////////////////////// ARTICLES
    public function ControlArticleAFermer()
    {

        $ano = $this->repoAno->findOneBy(['idAnomalie' => '999999999997', 'type' => 'ArticleAFermer']);

        $dateDuJour = new DateTime();
        $dateModif = $ano->getModifiedAt();
        $datediff = $dateModif->diff($dateDuJour)->format("%a");
        $produits = $this->repoArt->getControleArticleAFermer();
        if ($datediff > 0 && !empty($produits)) {
            $this->exportArticleAFermer($produits);
            $ano->setUpdatedAt($dateDuJour)
                ->setUser('JEROME')
                ->setModifiedAt($dateDuJour);
            $em = $this->entityManager;
            $em->persist($ano);
            $em->flush();
        } else {
            $ano->setUpdatedAt($dateDuJour);
            $ano->setUser('JEROME');
            $em = $this->entityManager;
            $em->persist($ano);
            $em->flush();
        }
    }

    private function getDataArticleAFermer($donnees): array
    {
        $dateDuJour = new DateTime();
        $date_1 = date_modify($dateDuJour, '-1 Day');

        $list = [];
        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['Dos'],
                $donnee['Ref'],
                $date_1->format('d-m-Y'),
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
        $sheet->fromArray($this->getDataArticleAFermer($donnees), null, 'A7', true);
        $nomFichier = 'Article - Article ( ART )';
        // Titre de la feuille
        $sheet->getCell('A1')->setValue($nomFichier);

        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        // Return the excel file as an attachment
        $produits = [];
        for ($i = 0; $i < count($donnees); $i++) {
            //dd($donnees);
            if ($donnees[$i]['Alerte'] != null | $donnees[$i]['Cmd'] != null | $donnees[$i]['Bl'] != null) {
                $produits[$i]['Ref'] = $donnees[$i]['Ref'];
                $produits[$i]['Sref1'] = $donnees[$i]['Sref1'];
                $produits[$i]['Sref2'] = $donnees[$i]['Sref2'];
                $produits[$i]['Alerte'] = $donnees[$i]['Alerte'];
                $produits[$i]['Cmd'] = $donnees[$i]['Cmd'];
                $produits[$i]['Bl'] = $donnees[$i]['Bl'];
            }
        }

        $html = $this->twig->render('mails/sendMailArticleAFermer.html.twig', ['produits' => $produits]);
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->mailTreatement)
            ->subject('Article Divalto à fermer')
            ->html($html)
            ->attachFromPath($temp_file, $fileName, 'application/msexcel');
        $this->mailer->send($email);
    }

    public function eanEnDouble()
    {
        try {
            error_log("Début de la fonction eanEnDouble");

            $donnees = $this->repoArt->getDoubleEan();
            // produits les plus récents
            foreach ($donnees as $donnee) {
                $ref = '';
                $sref1 = '';
                $sref2 = '';
                $produit = $this->repoArt->selectSartEanFromLastEanDoublon($donnee['EAN']);
                // produits avec ou sans sous référence
                $ref = $produit['REF'];
                $sref1 = $produit['SREF1'];
                $sref2 = $produit['SREF2'];
                if ($sref1 || $sref2) {
                    // Modifier le SART
                } else {
                    // Modifier le ART
                }
            }

            // modification du code EAN sur le produit pour mettre le dernier existant + 1 ?
            // faire un mail pour afficher la liste des correctifs

            // envoyer un mail
            if ($donnees) {
                $template = 'mails/MailEanDouble.html.twig';
                $html = $this->twig->render($template, ['donnees' => $donnees]);
                $email = (new Email())
                    ->from($this->mailEnvoi)
                    ->to($this->mailTreatement)
                    ->subject('Code Ean en doublon dans Divalto')
                    ->html($html);
                $this->mailer->send($email);
            }

            error_log("Fin de la fonction eanEnDouble");
        } catch (\Exception $e) {
            error_log("Erreur dans eanEnDouble : " . $e->getMessage());
            error_log("Trace de l'erreur : " . $e->getTraceAsString());
            throw $e; // Relancer l'exception après l'avoir journalisée
        }

    }

    public function sendMailForSummerAllUsers()
    {
        $currentDate = date('Y-m-d');
        if ($currentDate === date('Y') . '-03-01') {
            $this->holidayController->sendMailSummerForAllUsers();
        }
    }

    public function sendContratCommissionnaire()
    {
        $currentDayOfMonth = date('j'); // Renvoie le jour du mois actuel (de 1 à 31)
        if ($currentDayOfMonth === 20) {
            $this->contratCommissionnaireController->sendMail();
        }
    }

}
