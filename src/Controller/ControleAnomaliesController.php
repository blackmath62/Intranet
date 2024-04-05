<?php

namespace App\Controller;

//use App\Command\ContinuousExecutionCommand;
use App\Controller\AdminEmailController;
use App\Controller\ContratCommissionnaireController;
use App\Controller\HolidayController;
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
use App\Service\ContinuousExecutionService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class ControleAnomaliesController extends AbstractController
{
    private $entityManager;
    private $mailer;
    private $continuousExecutionService;
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
        ContinuousExecutionService $continuousExecutionService,
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
        $this->continuousExecutionService = $continuousExecutionService;
        $this->repoAno = $repoAno;
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

    #[Route("/controle/anomalies", name: "app_controle_anomalies")]

    public function Show_Anomalies()
    {
        $anomaliesCount = $this->repoAno->getCountAnomalies();

        return $this->render('controle_anomalies/anomalies.html.twig', [
            'title' => 'Liste des anomalies',
            'anomalies' => $this->repoAno->findAll(),
            'anomaliesCount' => $anomaliesCount,
            'fermerProduits' => $this->repoArt->getControleArticleAFermer(),
        ]);
    }

    #[Route("/controle/anomalies/run/script", name: "app_controle_anomalies_run")]

    public function executeFunctionsControle()
    {
        $this->continuousExecutionService->executeFunctionsAccordingToCriteria();

        $this->addFlash('message', 'Les scripts ont bien été lancés !');
        return $this->redirectToRoute('app_controle_anomalies');
    }

}
