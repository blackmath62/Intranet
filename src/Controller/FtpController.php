<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class FtpController extends AbstractController
{

    #[Route('/upload-via-ftp', name: 'app_upload_via_ftp')]
    public function upload(): Response
    {
        $ftpHost = 'poes.o2switch.net';
        $ftpUsername = 'pbpl9904';
        $ftpPassword = 'vUqnI9ecB5q4';
        $remoteFile = '/public_html/import/Maj_Article.csv';
        $port = 21; // Port FTPS
        // Chemin local du fichier à transférer
        $localFile = 'C:/wamp64/www/Intranet/public/tmp/Maj_Article.csv';

        if (is_file($localFile)) {

            // Connexion au serveur FTP
            $ftpConn = ftp_connect($ftpHost);
            if (!$ftpConn) {
                return new Response('Failed to connect to FTP server', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Authentification
            $login = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
            if (!$login) {
                return new Response('Failed to authenticate to FTP server', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Transfert de fichier
            $upload = ftp_put($ftpConn, $remoteFile, $localFile, FTP_BINARY);
            if (!$upload) {
                return new Response('Failed to upload file via FTP', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Fermeture de la connexion FTP
            ftp_close($ftpConn);
        } else {
            // Le chemin spécifié ne correspond pas à un fichier
            echo 'Le chemin spécifié ne correspond pas à un fichier.';
        }
        return new Response('File transferred successfully via FTP', Response::HTTP_OK);

    }

    #[Route('/download-via-ftp', name: 'app_download_via_ftp')]
    public function download(): Response
    {
        $ftpHost = 'poes.o2switch.net';
        $ftpUsername = 'pbpl9904';
        $ftpPassword = 'vUqnI9ecB5q4';
        $remoteDir = '/public_html/wp-content/uploads/vj-wp-import-export/export/';
        $localFile = 'C:/wamp64/www/Intranet/public/tmp/export.csv';
        $port = 21; // Port FTP

        // Connexion au serveur FTP
        $ftpConn = ftp_connect($ftpHost, $port);
        if (!$ftpConn) {
            return new Response('Failed to connect to FTP server', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Authentification
        $login = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
        if (!$login) {
            ftp_close($ftpConn);
            return new Response('Failed to authenticate to FTP server', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Activer le mode passif
        ftp_pasv($ftpConn, true);

        // Lister les répertoires
        $dirs = ftp_rawlist($ftpConn, $remoteDir);
        if ($dirs === false) {
            ftp_close($ftpConn);
            return new Response('Failed to list directories', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Trouver le dossier le plus récent
        $latestDir = '';
        $latestTime = 0;
        foreach ($dirs as $dirInfo) {
            // Parsing the raw directory list to find folder names and timestamps
            $parts = preg_split('/\s+/', $dirInfo);
            $isDir = $parts[0][0] === 'd'; // Check if it's a directory
            $dirName = $parts[count($parts) - 1];
            if ($isDir && $dirName != '.' && $dirName != '..') {
                // Construire une chaîne de date
                $dateStr = $parts[5] . ' ' . $parts[6] . ' ' . $parts[7];
                // Convertir la chaîne en timestamp Unix
                $dirTime = strtotime($dateStr);
                if ($dirTime > $latestTime) {
                    $latestTime = $dirTime;
                    $latestDir = $dirName;
                }
            }
        }

        if (!$latestDir) {
            ftp_close($ftpConn);
            return new Response('No directories found or failed to determine the most recent directory', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Construire le chemin du fichier export.csv
        $remoteFile = $remoteDir . $latestDir . '/export.csv';

        // Télécharger le fichier
        if (ftp_get($ftpConn, $localFile, $remoteFile, FTP_BINARY)) {
            $message = "File downloaded successfully.";
        } else {
            $message = "Failed to download the file.";
        }

        // Fermeture de la connexion FTP
        ftp_close($ftpConn);

        return new Response($message, Response::HTTP_OK);
    }

}
