<?php

$filePath = $_GET['filePath'];

// Vérifier si le fichier existe
if (file_exists($filePath)) {
    // Obtenir l'extension du fichier
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

    // Déterminer le type de contenu en fonction de l'extension du fichier
    switch ($fileExtension) {
        case 'jpg':
        case 'jpeg':
            $contentType = 'image/jpeg';
            break;
        case 'png':
            $contentType = 'image/png';
            break;
        // Ajoutez d'autres cas pour les types de fichiers supplémentaires si nécessaire

        default:
            // Type de fichier non pris en charge, utilisez le type de contenu générique
            $contentType = 'application/octet-stream';
            break;
    }

    // Renvoyer le fichier en tant que réponse HTTP avec le bon type de contenu
    header("Content-Type: $contentType");
    readfile($filePath);
}
