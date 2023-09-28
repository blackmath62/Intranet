<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

// Import de l'attribut

#[AsCommand(
    name: 'etiquettes:queue', // Nom de la commande
    description: "File d'attente d'impression"
)]
class EtiquettesQueueCommand extends AbstractCoreCommand
{
    const WORKING_PATH = 'C:\wamp64\www\Intranet\bin\\';

    protected $filePath = 'printQueue.ps1';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 0; $i <= 29; $i++) {
            $this->powershell->mustRun();
            // executes after the command finishes
            if (!$this->powershell->isSuccessful()) {
                throw new ProcessFailedException($this->powershell);
            }

            sleep(2);
        }

        return 1;
    }
}
