<?php

namespace App\Controller;

use App\Repository\Divalto\CliRepository;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class CoupeStationChargeurMajController extends AbstractController
{
    #[Route("/Lhermitte/coupe/station/chargeur/maj", name: "app_coupe_station_chargeur_maj")]

    public function index(CliRepository $repo): Response
    {
        $clients = $repo->getClientsForCoupe();

        foreach ($clients as $value) {
            $document = '';
            $formatter = new HtmlFormatter('UTF-8');
            try {
                $document = new Document($value['InstructionLiv']);
                $clis[] = [
                    'CdDestinataire' => $value['CdDestinataire'],
                    'CdInseePays' => $value['CdInseePays'],
                    'Nom' => $value['Nom'],
                    'MotDirecteur' => '',
                    'Adresse1' => $value['Adresse1'],
                    'Adresse2' => $value['Adresse2'],
                    'CdPostal' => $value['CdPostal'],
                    'Ville' => $value['Ville'],
                    'Telephone' => $value['Telephone'],
                    'InstructionLiv' => $formatter->Format($document),
                    'Email' => $value['Email'],
                    'sService' => $value['sService'],
                ];
            } catch (Throwable $th) {
                $clis[] = [
                    'CdDestinataire' => $value['CdDestinataire'],
                    'CdInseePays' => $value['CdInseePays'],
                    'Nom' => $value['Nom'],
                    'MotDirecteur' => '',
                    'Adresse1' => $value['Adresse1'],
                    'Adresse2' => $value['Adresse2'],
                    'CdPostal' => $value['CdPostal'],
                    'Ville' => $value['Ville'],
                    'Telephone' => $value['Telephone'],
                    'InstructionLiv' => '',
                    'Email' => $value['Email'],
                    'sService' => $value['sService'],
                ];
            }
        }
        return $this->render('coupe_station_chargeur_maj/index.html.twig', [
            'title' => 'Export Coupé Csv',
            'clients' => $clis,
        ]);
    }
}
