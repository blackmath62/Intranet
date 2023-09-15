<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    private $dompdf;

    public function __construct()
    {
        // Créez une instance de Dompdf avec des options personnalisées si nécessaire
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $this->dompdf = new Dompdf($options);
    }

    public function generatePdfFromHtml($htmlContent)
    {
        // Chargez le contenu HTML dans Dompdf
        $this->dompdf->loadHtml($htmlContent);

        // Rendez le PDF (vous pouvez également enregistrer le PDF sur le disque si nécessaire)
        $this->dompdf->render();

        // Renvoie le PDF généré en tant que flux de données
        return $this->dompdf->output();
    }
}
