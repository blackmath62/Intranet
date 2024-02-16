<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// actuellement non utilisé ça devait servir pour envoyer les fichiers excels de maniére asynchrone mais ça n'a jamais fonctionné

class ProcessInBackgroundCommand extends Command
{
    protected static $defaultName = 'app:process-in-background';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Execute a background process with a given URL')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL to execute');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        // Execute the URL or perform any other necessary background task here
        // For demonstration purposes, we'll simply output the URL
        $output->writeln("Executing URL: $url");

        return Command::SUCCESS;
    }
}
