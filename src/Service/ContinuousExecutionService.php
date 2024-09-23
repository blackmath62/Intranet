<?php
namespace App\Service;

use App\Controller\AdminEmailController;
use App\Controller\AffairesController;
use App\Controller\AnomaliesController;
use App\Controller\ClientFeuRougeOrangeController;
use App\Controller\CmdRobyDelaiAccepteReporteController;
use App\Controller\ComptaAnalytiqueController;
use App\Controller\ContratCommissionnaireController;
use App\Controller\FscAttachedFileController;
use App\Controller\FscPieceClientController;
use App\Controller\FtpController;
use App\Controller\HolidayController;
use App\Repository\Divalto\ArtRepository;
use App\Repository\Divalto\CliRepository;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\FouRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\ControlesAnomaliesRepository;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    private $anomaliesController;
    private $clientFeuRougeOrangeController;
    private $affairesController;
    private $fscAttachedFileController;
    private $movementBillFscController;
    private $ftpController;
    private $repoAno;
    private $repoCompta;
    private $repoArtStockMouvEf;
    private $repoCli;
    private $repoArt;
    private $repoFou;
    private $repoMouv;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $repoUsers;
    private $cmdRobyDelaiAccepteReporteController;

    public function __construct(
        ManagerRegistry $registry,
        MailerInterface $mailer,
        Environment $twig,
        HolidayController $holidayController,
        AdminEmailController $adminEmailController,
        ContratCommissionnaireController $contratCommissionnaireController,
        ClientFeuRougeOrangeController $clientFeuRougeOrangeController,
        FtpController $ftpController,
        ComptaAnalytiqueController $comptaAnalytiqueController,
        AnomaliesController $anomaliesController,
        FscPieceClientController $movementBillFscController,
        FscAttachedFileController $fscAttachedFileController,
        CmdRobyDelaiAccepteReporteController $cmdRobyDelaiAccepteReporteController,
        AffairesController $affairesController,
        UsersRepository $repoUsers,
        MailListRepository $repoMail,
        MouvRepository $repoMouv,
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
        $this->repoMouv = $repoMouv;
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->repoUsers = $repoUsers;
        $this->holidayController = $holidayController;
        $this->ftpController = $ftpController;
        $this->adminEmailController = $adminEmailController;
        $this->anomaliesController = $anomaliesController;
        $this->cmdRobyDelaiAccepteReporteController = $cmdRobyDelaiAccepteReporteController;
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

        try {
            error_log("Début de executeFunctionsAccordingToCriteria");

            $functionsEveryday = [];
            $functionsOneDay = [];

            $currentDayOfWeek = date('N'); // Renvoie 1 pour lundi, 2 pour mardi, etc.
            $currentHour = date('H'); // Récupérer l'heure actuelle
            // Déterminer s'il est entre 12h00 et 14h00 pour les fonctions AM, 00h00 et 02h00 pour les fonctions PM
            if ($currentHour >= 11 && $currentHour < 23) {
                $creneau = 'am';
            } elseif ($currentHour >= 0 && $currentHour < 11) {
                $creneau = 'pm';
            }

            $functionsToExecuteEveryday = [
                'always' => [
                    [$this->clientFeuRougeOrangeController, 'sendMail'],
                    [$this->anomaliesController, 'eanEnDouble'],
                    [$this->affairesController, 'updateAffaire'],
                    [$this->ftpController, 'download'],
                    [$this->movementBillFscController, 'updateWithoutFlash'],
                    [$this->anomaliesController, 'run_auto_wash'],
                    [$this->anomaliesController, 'ControleRegimeFournisseurPieces'],
                    [$this->anomaliesController, 'ControleRegimeFournisseurPays'],
                    [$this->anomaliesController, 'ControleGeneralFournisseur'],
                    [$this->anomaliesController, 'ControleAnomaliesUniteArticle'],
                    [$this->anomaliesController, 'ControleAnomaliesMauvaisArticleFsc'],
                    [$this->anomaliesController, 'ControleAnomaliesRegimeArticlePiece'],
                    [$this->anomaliesController, 'ControleAnomaliesSaisieArticleFerme'],
                    [$this->anomaliesController, 'ControleAnomaliesToutesSrefArticleFerme'],
                    [$this->anomaliesController, 'ControlArticleAFermer'],
                    [$this->anomaliesController, 'ControlSrefArticleAFermer'],
                    [$this->anomaliesController, 'ControleRegimeClientPiece'],
                    [$this->anomaliesController, 'ControleRegimeClientPays'],
                    [$this->anomaliesController, 'ControleAnomalieClientPhyto'],
                    [$this->anomaliesController, 'ControleGeneralClient'],
                ],
                'am' => [
                    [$this->fscAttachedFileController, 'majFscOrderListFromDivalto'],
                    [$this->anomaliesController, 'ControlePieces'],
                    [$this->anomaliesController, 'sendContratCommissionnaire'],
                ],
                'pm' => [
                    [$this->comptaAnalytiqueController, 'sendMail'],
                    [$this->anomaliesController, 'sendMailForSummerAllUsers'],
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
                    'am' => [
                        [$this->cmdRobyDelaiAccepteReporteController, 'MajCmdRobyAccepteReporte'],
                    ],
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
                    'am' => [
                        [$this->cmdRobyDelaiAccepteReporteController, 'MajCmdRobyAccepteReporte'],
                    ],
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

            error_log("Fin de executeFunctionsAccordingToCriteria");
        } catch (\Exception $e) {
            error_log("Erreur dans executeFunctionsAccordingToCriteria : " . $e->getMessage());
            error_log("Trace de l'erreur : " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function tryCatch($function)
    {
        // Exécution de la fonction dans un bloc try-catch
        try {
            if (is_array($function) && count($function) == 2 && is_object($function[0]) && method_exists($function[0], $function[1])) {
                // Journaliser avant l'appel de la fonction
                error_log("Appel de la fonction : " . get_class($function[0]) . " - " . $function[1]);
                call_user_func([$function[0], $function[1]]);
                // Journaliser après l'appel de la fonction
                error_log("Fonction exécutée avec succès : " . get_class($function[0]) . " - " . $function[1]);
            } else {
                // Journaliser le problème avant de lancer l'exception
                error_log("Invalid function format or method does not exist: " . print_r($function, true));
                // Gérer le cas où $function n'est pas un tableau valide ou la méthode n'existe pas
                throw new \Exception("Invalid function format or method does not exist: " . print_r($function, true));
            }
        } catch (\Exception $e) {
            // Envoyer un e-mail d'alerte en cas d'erreur
            $controller = get_class($function[0]);
            $errorMessage = $e->getMessage();
            $errorFunction = isset($function[1]) ? $controller . ' - ' . $function[1] : 'Unknown Function';
            $this->sendErrorEmail("Error in function " . $errorFunction . " : " . $errorMessage);
            // Journaliser l'erreur avec plus de détails
            error_log("Erreur dans la fonction : " . $errorFunction . " : " . $errorMessage);
            error_log("Trace de l'erreur : " . $e->getTraceAsString());
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

        // Envoyez l'e-mail
        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log("Erreur lors de l'envoi de l'e-mail : " . $e->getMessage());
        }
    }

}
