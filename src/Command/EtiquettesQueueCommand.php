<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EtiquettesQueueCommand extends AbstractCoreCommand
{
    const WORKING_PATH = 'C:\wamp64\www\Intranet\bin\\';
    protected static $defaultName = 'etiquettes:queue';
    protected static $defaultDescription = "File d'attente d'impression";
    protected $filePath = 'printQueue.ps1';

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 0; $i <= 29; $i++) {
            //$route = 'C:\wamp64\www\Intranet\bin\defautPrint.ps1';
            $this->powershell->mustRun();
            // executes after the command finishes
            //dd($process->getOutput());
            if (!$this->powershell->isSuccessful()) {
                throw new ProcessFailedException($this->powershell);
            }

            sleep(2);
        }

        return 1;
    }

}
