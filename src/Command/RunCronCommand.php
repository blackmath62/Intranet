<?php

namespace App\Command;

use App\Controller\ControleAnomaliesController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// Import de l'attribut

#[AsCommand(
    name: 'RunCron', // Nom de la commande
    description: 'Commande de lancement des tâches CRON'
)]
class RunCronCommand extends Command
{
    private $controller;

    public function __construct(ControleAnomaliesController $controller)
    {
        $this->controller = $controller;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $this->controller->Run_Cron();

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('La requête a été effectuée avec succès ! Utilisez --help pour voir vos options.');

        return Command::SUCCESS;
    }
}
