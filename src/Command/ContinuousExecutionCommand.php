<?php
namespace App\Command;

use App\Repository\Main\MailListRepository;
use App\Service\ContinuousExecutionService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContinuousExecutionCommand extends Command
{
    protected static $defaultName = 'app:continuous-execution';

    private $continuousExecutionService;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $mailer;

    public function __construct(
        ContinuousExecutionService $continuousExecutionService,
        MailListRepository $repoMail,
        MailerInterface $mailer,
    ) {
        $this->continuousExecutionService = $continuousExecutionService;
        $this->mailer = $mailer;
        $this->mailEnvoi = "intranet@groupe-axis.fr";
        $this->mailTreatement = "jpochet@groupe-axis.fr";
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Continuous execution command')
            ->setHelp('This command executes functions continuously.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            while (true) {
                // Vérifiez si c'est un samedi ou un dimanche
                $currentDayOfWeek = date('N'); // Renvoie 1 pour lundi, 2 pour mardi, etc.
                if ($currentDayOfWeek >= 6) {
                    // Si c'est un samedi ou un dimanche, calculer le temps à attendre jusqu'au prochain lundi
                    $nextMondayTime = strtotime('next Monday');
                    $currentTime = time();
                    $sleepTime = $nextMondayTime - $currentTime;
                    sleep($sleepTime);
                }

                // Définissez les horaires d'exécution pour la journée
                $firstExecutionTime = strtotime('today 12:45');
                $secondExecutionTime = strtotime('today 00:45') + 24 * 3600; // Ajouter un jour pour le prochain jour

                $sleep = 0;
                // Attendre jusqu'à ce que l'heure actuelle soit dans le créneau horaire souhaité
                $currentTime = time();
                if ($currentTime < $firstExecutionTime) {
                    $sleep = $firstExecutionTime - $currentTime;
                } elseif ($currentTime < $secondExecutionTime) {
                    $sleep = $secondExecutionTime - $currentTime;
                }

                $this->sendEmailTreatment($sleep);
                sleep($sleep);

                error_log("Début de l'exécution des fonctions selon les critères");
                $this->continuousExecutionService->executeFunctionsAccordingToCriteria();
                error_log("Fin de l'exécution des fonctions selon les critères");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            error_log("Erreur dans ContinuousExecutionCommand : " . $e->getMessage());
            error_log("Trace de l'erreur : " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    private function sendEmailTreatment($sleep)
    {
        $formattedSleep = sprintf(
            "%02d:%02d:%02d",
            ($sleep / 3600), // heures
            ($sleep / 60 % 60), // minutes
            ($sleep % 60) // secondes
        );

        $currentDateTime = date('Y-m-d H:i:s');
        // Envoyer un e-mail d'alerte en cas d'erreur
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->mailTreatement)
            ->subject('Execution de la commande : ' . $currentDateTime)
            ->text('La commande à bien été executée... prochaine execution ' . $formattedSleep);

        $this->mailer->send($email);
    }

}
