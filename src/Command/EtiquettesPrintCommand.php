<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EtiquettesPrintCommand extends AbstractCoreCommand
{
    protected static $defaultName = 'etiquettes:print';
    protected static $defaultDescription = "Automatisation de l'impression des étiquettes";
    protected $filePath = 'printEtiquette.ps1';

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $i = 0;
        do {
            $i++;
            $output->writeLn('Execution #' . $i);
            $this->powershell->mustRun();
            if (!$this->powershell->isSuccessful()) {
                throw new ProcessFailedException($this->powershell);
            }

            sleep(10);
        } while (1 == 1);

        return 1;
    }
}
