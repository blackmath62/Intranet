<?php

namespace App\Service;

use DateTime;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenImportDivaltoXlsxService
{

    // TODO je n'ai pas encore adapté le code pour que la création et validation de piéce Tiers fonctionne avec ce fichier, ces entêtess ne servent donc pour l'instant à rien

    // Entête de colonne pour la création de mouvements tiers
    public function getEnteteCreationPieceTiers()
    {
        $entete = [
            'FICHE',
            'DOSSIER',
            'ETABLISSEMENT',
            'REF_PIECE',
            'TYPE_TIERS',
            'TYPE_PIECE',
            'CODE_TIERS',
            'CODE_OP',
            'DEPOT',
            'ENT.PIDT',
            'ENT.PIREF',
            'NO_SOUS_LIGNE',
            'REFERENCE',
            'SREF1',
            'SREF2',
            'DESIGNATION',
            'REF_FOURNISSEUR',
            'MOUV.OP',
            'QUANTITE',
            'MOUV.PPAR',
            'MOUV.PUB',
            'EMPLACEMENT',
            'SERIE',
            'QUANTITE_VTL',
            'ERREUR',
        ];

        return $entete;
    }

    // Entête de colonne pour les validations de mouvements tiers
    public function getEnteteValidationPartiellePieceTiers()
    {
        $entete = [
            'FICHE',
            'DOSSIER',
            'ETABLISSEMENT',
            'REF_PIECE',
            'TYPE_TIERS',
            'TYPE_PIECE',
            'TYPE_PIECE_FINALE',
            'NO_PIECE',
            'NO_PIECE_FINALE',
            'CODE_TIERS',
            'CODE_OP',
            'DEPOT',
            'ENT.PIDT',
            'ENT.PIREF',
            'ENRNO',
            'NO_SOUS_LIGNE',
            'REFERENCE',
            'SREF1',
            'SREF2',
            'DESIGNATION',
            'REF_FOURNISSEUR',
            'QUANTITE',
            'MOUV.OP',
            'MOUV.PPAR',
            'MOUV.PUB',
            'VTLNO',
            'EMPLACEMENT',
            'SERIE',
            'QUANTITE_VTL',
            'ERREUR',
        ];

        return $entete;
    }

    // Entête de colonne pour la création de mouvements interne stock
    public function getEnteteRegularisationInterne()
    {
        $entetes = [
            'FICHE',
            'DOSSIER',
            'ETABLISSEMENT',
            'REF_PIECE',
            'CODE_TIERS',
            'CODE_OP',
            'DEPOT',
            'DEPOT_DESTINATION',
            'ENT.PIDT',
            'ENT.PIREF',
            'NO_SOUS_LIGNE',
            'REFERENCE',
            'SREF1',
            'SREF2',
            'DESIGNATION',
            'REF_FOURNISSEUR',
            'MOUV.OP',
            'QUANTITE',
            'MOUV.PPAR',
            'MOUV.PUB',
            'EMPLACEMENT',
            'EMPLACEMENT_DESTINATION',
            'SERIE',
            'QUANTITE_VTL',
            'ERREUR',
        ];

        return $entetes;
    }

    // Entête de colonne pour la création de mouvements interne stock
    public function getEnteteSart()
    {
        $entetes = [
            'DOSSIER',
            'REFERENCE',
            'SREFERENCE1',
            'SREFERENCE2',
            'CONF',
            'EAN_ARTICLE_SOUS_REFERENCE',
            'POIDSBRUT',
            'POIDSNET',
            'PRIXACHAT',
            'DATEPRIXACHAT',
            'CMPUNITAIRE',
            'DATECMP',
            'CRUNITAIRE',
            'DATECR',
            'CMPUNITAIRE',
            'DATECMP',
            'Anomalies',
            'Alertes',
        ];

        return $entetes;
    }

    public function param($typeTiers, $dos, $depot, $date = null, $tiers = null, $piece = null)
    {
        $param['dos'] = $dos;
        $param['depot'] = $depot;
        $param['date'] = $date;
        $param['typeTiers'] = $typeTiers;

        if ($typeTiers == 'I') {
            $param['tiers'] = 'I0000000';
            $param['op'] = 'JI';
            $param['entetes'] = $this->getEnteteRegularisationInterne();
        } elseif ($tiers) {
            $param['tiers'] = $tiers;
            if ($piece) {
                $param['op'] = $piece->getOp();
                $param['entetes'] = $this->getEnteteValidationPartiellePieceTiers();
            } else {
                $param['op'] = $typeTiers;
                $param['entetes'] = $this->getEnteteCreationPieceTiers();
            }
        } else {
            return 'Pas de tiers renseigné';
        }

        return $param;

    }

    // exemple tableau param['C', 1 , 2 , 'C0238687' , 59874 ]
    // choix typeTiers ['I','C','F']
    public function get_export_excel($param, $donnees)
    {
        //dd($param);
        //$reglages = $this->param($param['typeTiers'], $param['dos'], $param['depot'], $param['date'], $param['tiers'], $param['piece']);
        $d = '';
        $d = new DateTime('NOW');
        $dateTime = $d->format('Ymdhis');
        $nomFichier = $param['typeTiers'] . $dateTime;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($param['typeTiers']);

        $entetes = $param['entetes'];

        // Écriture des entêtes de colonnes
        foreach ($entetes as $index => $label) {
            $cellCoordinate = Coordinate::stringFromColumnIndex($index + 1) . '3';
            if ($label == 'FICHE') {
                $colFiche = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'DOSSIER') {
                $colDos = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'CODE_TIERS') {
                $colCodeTiers = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'CODE_OP') {
                $colOp = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'DEPOT') {
                $colDepot = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'DEPOT_DESTINATION') {
                $colDepotDest = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'ENT.PIDT') {
                $colPidt = Coordinate::stringFromColumnIndex($index + 1);
            } elseif ($label == 'ENT.PIREF') {
                $colPiref = Coordinate::stringFromColumnIndex($index + 1);
            }
            $sheet->getCell($cellCoordinate)->setValue($label);
        }
        // Écrire l'identifiant de la pièce IPAR
        $sheet->getCell($colFiche . '4')->setValue('IPAR');
        $sheet->getCell($colDos . '4')->setValue($param['dos']); // Exemple de valeur fixe

        // Écrire l'identifiant de la pièce ENT
        $sheet->getCell($colFiche . '5')->setValue('ENT');
        $sheet->getCell($colCodeTiers . '5')->setValue($param['tiers']);
        $sheet->getCell($colOp . '5')->setValue($param['op']);
        $sheet->getCell($colDepot . '5')->setValue($param['depot']);
        // Vérifier si l'entête DEPOT_DESTINATION existe dans le tableau des entêtes
        if (isset($colDepotDest)) {
            $sheet->getCell($colDepotDest . '5')->setValue($param['depot']);
        }
        if (!$param['date']) {
            $date = new DateTime();
        } else {
            $date = new DateTime($param['date']);
        }
        //$date->format('d/m/Y');
        $sheet->getCell($colPidt . '5')->setValue($date->format('d/m/Y'));
        $sheet->getCell($colPiref . '5')->setValue($nomFichier);

        // Increase row cursor after header write
        $sheet->fromArray($this->getData($entetes, $donnees), null, 'A6', true);

        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        // Return the excel file as an attachment

        $chemin = 'doc/Logistique/';
        $fichier = $chemin . '/' . $fileName;
        $writer->save($fichier);

        return $fichier;
    }

    public function get_export_excel_art($param, $donnees)
    {
        $d = '';
        $d = new DateTime('NOW');
        $dateTime = $d->format('Ymdhis');
        $nomFichier = $param['title'] . $dateTime;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($param['title']);

        $entetes = $param['entetes'];
        $sheet->getCell('A4')->setValue('Données');

        // Écriture des entêtes de colonnes
        foreach ($entetes as $index => $label) {
            $cellCoordinate = Coordinate::stringFromColumnIndex($index + 1) . '6';
            $sheet->getCell($cellCoordinate)->setValue($label);
        }

        // Increase row cursor after header write
        $sheet->fromArray($this->getData($entetes, $donnees), null, 'A7', true);

        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = $nomFichier . '.xlsx';
        // Return the excel file as an attachment

        $chemin = 'doc/Logistique/';
        $fichier = $chemin . '/' . $fileName;
        $writer->save($fichier);
        return $fichier;
    }

    // générer un fichier Excel qui sera envoyé par mail à l'utilisateur
    public function getData($entetes, $donnees): array
    {
        $list = [];

        foreach ($donnees as $donnee) {

            $row = [];
            foreach ($entetes as $col) {
                if (strpos($col, 'SREF') === 0 && $donnee[$col] != '') {
                    $value = "'" . $donnee[$col];
                } else {
                    $value = $donnee[$col] ?? ''; // Si une colonne n'existe pas dans les données, on met une chaîne vide
                }
                $row[] = $value;
            }
            $list[] = $row;
        }

        return $list;
    }
}
