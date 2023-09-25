<?php

namespace App\Command;

use App\Controller\ControleAnomaliesController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCronCommand extends Command
{
    protected static $defaultName = 'RunCron';
    protected static $defaultDescription = 'Commande de lancement des tâches CRON';
    private $controller;

    public function __construct(ControleAnomaliesController $controller)
    {
        $this->controller = $controller;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
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

        $io->success('La requête a été effectué avec succés ! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
