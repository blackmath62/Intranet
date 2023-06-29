<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

abstract class AbstractCoreCommand extends Command
{
    const WORKING_PATH = 'C:\wamp64\www\Intranet\bin\\';

    protected $powershell;
    protected $filePath;

    public function __construct(string $name = null)
    {
        $this->powershell = new Process(['powershell', self::WORKING_PATH . $this->filePath]);

        parent::__construct($name);
    }
}
