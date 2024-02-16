<?php

// src/Controller/ExecuteCommandController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

// actuellement non utilisé ça devait servir pour envoyer les fichiers excels de maniére asynchrone mais ça n'a jamais fonctionné

class ExecuteCommandController extends AbstractController
{
    public function executeCommand(Request $request, KernelInterface $kernel): Response
    {
        $command = $request->query->get('command');
        $url = $request->query->get('url');

        if (!$command || !$url) {
            return new Response('Missing command or URL parameters.', Response::HTTP_BAD_REQUEST);
        }

        // Execute the command with Symfony Console
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $command,
            'url' => $url,
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        // Return the output of the command
        return new Response($output->fetch());
    }
}
