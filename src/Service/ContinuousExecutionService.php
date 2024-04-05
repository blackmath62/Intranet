<?php
namespace App\Service;

use App\Controller\AdminEmailController;
use App\Controller\AffairesController;
use App\Controller\ClientFeuRougeOrangeController;
use App\Controller\ComptaAnalytiqueController;
use App\Controller\ContratCommissionnaireController;
use App\Controller\FscAttachedFileController;
use App\Controller\FscPieceClientController;
use App\Controller\HolidayController;
use App\Entity\Main\CmdRobyDelaiAccepteReporte;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ContinuousExecutionService
{

    private $entityManager;
    private $mailer;
    private $twig;
    private $adminEmailController;
    private $contratCommissionnaireController;
    private $holidayController;
    private $comptaAnalytiqueController;
    private $clientFeuRougeOrangeController;
    private $affairesController;
    private $fscAttachedFileController;
    private $movementBillFscController;
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
        ClientFeuRougeOrangeController $clientFeuRougeOrangeController,
        ComptaAnalytiqueController $comptaAnalytiqueController,
        FscPieceClientController $movementBillFscController,
        FscAttachedFileController $fscAttachedFileController,
        AffairesController $affairesController,
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
        $this->entityManager = $registry->getManager();
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->repoAno = $repoAno;
        $this->repoCompta = $repoCompta;
        $this->repoArtStockMouvEf = $repoArtStockMouvEf;
        $this->repoCli = $repoCli;
        $this->repoArt = $repoArt;
        $this->repoFou = $repoFou;
        $this->repoEnt = $repoEnt;
        $this->repoCmdRoby = $repoCmdRoby;
        $this->repoMouv = $repoMouv;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->repoUsers = $repoUsers;
        $this->holidayController = $holidayController;
        $this->adminEmailController = $adminEmailController;
        $this->contratCommissionnaireController = $contratCommissionnaireController;
        $this->fscAttachedFileController = $fscAttachedFileController;
        $this->movementBillFscController = $movementBillFscController;
        $this->comptaAnalytiqueController = $comptaAnalytiqueController;
        $this->clientFeuRougeOrangeController = $clientFeuRougeOrangeController;
        $this->affairesController = $affairesController;
        //parent::__construct();
    }

    public function executeFunctionsAccordingToCriteria()
    {
        $functionsEveryday = [];
        $functionsOneDay = [];

        $currentDayOfWeek = date('N'); // Renvoie 1 pour lundi, 2 pour mardi, etc.
        $currentHour = date('H'); // Récupérer l'heure actuelle
        // Déterminer s'il est entre 12h00 et 14h00 pour les fonctions AM, 00h00 et 02h00 pour les fonctions PM
        if ($currentHour >= 12 && $currentHour < 23) {
            $creneau = 'am';
        } elseif ($currentHour >= 0 && $currentHour < 11) {
            $creneau = 'pm';
        }

        $functionsToExecuteEveryday = [
            'always' => [
                [$this->clientFeuRougeOrangeController, 'sendMail'],
                [$this, 'eanEnDouble'],
                [$this->affairesController, 'update'],
                [$this->movementBillFscController, 'update'],
                [$this, 'run_auto_wash'],
                [$this, 'ControleRegimeFournisseurPieces'],
                [$this, 'ControleRegimeFournisseurPays'],
                [$this, 'ControleGeneralFournisseur'],
                [$this, 'ControleAnomaliesUniteArticle'],
                [$this, 'ControleAnomaliesMauvaisArticleFsc'],
                [$this, 'ControleAnomaliesRegimeArticlePiece'],
                [$this, 'ControleAnomaliesSaisieArticleFerme'],
                [$this, 'ControleAnomaliesToutesSrefArticleFerme'],
                [$this, 'ControlArticleAFermer'],
                [$this, 'ControlSrefArticleAFermer'],
                [$this, 'ControleRegimeClientPiece'],
                [$this, 'ControleRegimeClientPays'],
                [$this, 'ControleAnomalieClientPhyto'],
                [$this, 'ControleGeneralClient'],
            ],
            'am' => [
                [$this->fscAttachedFileController, 'majFscOrderListFromDivalto'],
                [$this, 'ControlePieces'],
                [$this, 'sendContratCommissionnaire'],
            ],
            'pm' => [
                [$this->comptaAnalytiqueController, 'sendMail'],
                [$this, 'sendMailForSummerAllUsers'],
            ],
        ];

        // On récupére les fonctions à exécuter
        $functionsEveryday = $functionsToExecuteEveryday[$creneau];
        // On ajoute le tableau always à celui du créneau
        $functionsEveryday = array_merge($functionsEveryday, $functionsToExecuteEveryday['always']);

        // Fonctions à exécuter tous les jours de la semaine
        foreach ($functionsEveryday as $function) {
            $this->tryCatch($function);
        }

        $functionsToExecuteOneDay = [
            '1' => [
                'am' => [$this, 'MajCmdRobyAccepteReporte'],
                'pm' => [],
                'always' => [],
            ],
            '2' => [
                'am' => [],
                'pm' => [],
                'always' => [],
            ],
            '3' => [
                'am' => [],
                'pm' => [],
                'always' => [],
            ],
            '4' => [
                'am' => [],
                'pm' => [],
                'always' => [],
            ],
            '5' => [
                'am' => [$this, 'MajCmdRobyAccepteReporte'],
                'pm' => [],
                'always' => [],
            ],
        ];
        // On récupére les fonctions à exécuter
        $functionsOneDay = $functionsToExecuteOneDay[$currentDayOfWeek][$creneau];
        // On ajoute le tableau always à celui du créneau
        $functionsOneDay = array_merge($functionsOneDay, $functionsToExecuteOneDay[$currentDayOfWeek]['always']);

        // Fonctions à exécuter uniquement certains jours de la semaine
        if ($functionsOneDay) {
            foreach ($functionsOneDay as $function) {
                if ($function) {
                    $this->tryCatch($function);
                }
            }
        }
    }

    private function tryCatch($function)
    {
        // Exécution de la fonction dans un bloc try-catch
        try {
            if (is_array($function) && count($function) == 2 && is_object($function[0]) && is_string($function[1])) {
                call_user_func([$function[0], $function[1]]);
            } else {
                // Gérer le cas où $function n'est pas un tableau valide
                throw new \Exception("Invalid function format");
            }
        } catch (\Exception $e) {
            // Envoyer un e-mail d'alerte en cas d'erreur
            $errorMessage = $e->getMessage();
            $errorFunction = isset($function[1]) ? $function[1] : 'Unknown Function';
            $this->sendErrorEmail("Error in function " . $errorFunction . " : " . $errorMessage);
        }
    }

    private function sendErrorEmail($erreur)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        // Envoyer un e-mail d'alerte en cas d'erreur
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->mailTreatement)
            ->subject('Error in Continuous Execution Command on ' . $currentDateTime)
            ->text('An error occurred during function execution on ' . $currentDateTime . ': ' . $erreur);

        $this->mailer->send($email);
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

    public function MajCmdRobyAccepteReporte()
    {
        $donnees = $this->repoEnt->majCmdsRobyDelaiAccepteReporte();
        for ($lig = 0; $lig < count($donnees); $lig++) {
            $id = $donnees[$lig]['Identification'];
            $search = $this->repoCmdRoby->findOneBy(['identification' => $id]);
            // si elle n'existe pas, on la créér
            if (empty($search)) {
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
                } else {
                    $listCmd->setDelaiAccepte(null);
                }
                if ($donnees[$lig]['DelaiReporte']) {
                    $listCmd->setDelaiReporte(new DateTime($donnees[$lig]['DelaiReporte']));
                } else {
                    $listCmd->setDelaiReporte(null);
                }
            } elseif (!is_null($search)) {
                $listCmd = $search;
                $listCmd->setNotreRef($donnees[$lig]['NotreRef']);
                $listCmd->setTel($donnees[$lig]['Tel']);
                if ($donnees[$lig]['DelaiAccepte']) {
                    $listCmd->setDelaiAccepte(new DateTime($donnees[$lig]['DelaiAccepte']));
                } else {
                    $listCmd->setDelaiAccepte(null);
                }
                if ($donnees[$lig]['DelaiReporte']) {
                    $listCmd->setDelaiReporte(new DateTime($donnees[$lig]['DelaiReporte']));
                } else {
                    $listCmd->setDelaiReporte(null);
                }
            }
            $em = $this->entityManager;
            $em->persist($listCmd);
            $em->flush();
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

    #[Route("/controle/pieces", name: "app_controle_pieces")]

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
    public function eanEnDouble()
    {
        // envoyer un mail
        $donnees = $this->repoArt->getDoubleEan();
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
                    $MailsList = $this->adminEmailController->formateEmailList($treatementMails);
                }
            }
            if ($ME == true) {
                if ($EV == true | $HP == true) {
                    $treatementMails = $this->repoUsers->getFindUsersEvHpMe();
                    $MailsList = $this->adminEmailController->formateEmailList($treatementMails);
                } else {
                    $treatementMails = $this->repoUsers->findBy(['me' => 1]);
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

}
