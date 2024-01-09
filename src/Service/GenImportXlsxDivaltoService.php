<?php

namespace App\Service;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\SecurityBundle\Security;

class GenImportXlsxDivaltoService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUserPseudo()
    {
        // Vérifier si l'utilisateur est authentifié
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Accéder à l'utilisateur actuel
            $utilisateur = $this->security->getUser();

            // Vérifier si l'utilisateur a une méthode getPseudo()
            if (method_exists($utilisateur, 'getPseudo')) {
                // Obtenir le pseudo de l'utilisateur
                $pseudo = $utilisateur->getPseudo();
                return $pseudo;
            }
        }

        return null;
    }

    public function getTypePiece($tiers = null)
    {
        if ($tiers) {
            if (strpos($tiers, "C") === 0) {
                $typeMouv['entree'] = 'C';
                $typeMouv['sortie'] = 'D';
                $typeMouv['typePiece'] = '3-Bl Client ';
            } elseif (strpos($tiers, "F") === 0) {
                $typeMouv['entree'] = 'F';
                $typeMouv['sortie'] = 'G';
                $typeMouv['typePiece'] = '3-Bl Fourn ';
            }
            $typeMouv['indexColonne'] = $this->getEnteteMouvTiers();
        } else {
            $typeMouv['entree'] = 'JI';
            $typeMouv['sortie'] = 'II';
            $typeMouv['typePiece'] = 'Stock ';
            $typeMouv['indexColonne'] = $this->getEnteteMouvStock();
        }
        return $typeMouv;
    }
    // Entête de colonne pour les mouvements tiers
    public function getEnteteMouvTiers()
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
    // Entête de colonne pour les mouvements interne stock
    public function getEnteteMouvStock()
    {
        $entete = [
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
        ];

        return $entete;
    }
    // Entête de colonne pour les mouvements info dépôt

    public function getEnteteInfoDepot()
    {
        $entete = [
            'DOSSIER',
            'REFERENCE',
            'SREFERENCE1',
            'SREFERENCE2',
            'DEPOT',
            'NATURESTOCK',
            'NUMERONOTE',
            'EMPLACEMENT',
            'STLGTSORCOD',
            'FLAGORDOSMC',
            'FAMRANGEMENT',
            'ELIGIBLESLOTTING',
            'WMPROFILCOD',
            'WMEMPLPREP',
            'RESJRNB',
            'WMEMPCONTNB',
            'Anomalies',
            'Alertes',
        ];

        return $entete;
    }
    // Entête de colonne pour les mouvements info stock
    public function getEnteteInfoStock()
    {
        $entete = [
            'DOSSIER',
            'REFERENCE',
            'DEPOT',
            'NATURESTOCK',
            'NUMERONOTE',
            'DATEINV',
            'FAMILLEINV',
            'PERIODEINV',
            'INVIMPPAGCOD',
            'INVPAGNB',
            'CRITEREINV',
            'Anomalies',
            'Alertes',
        ];

        return $entete;
    }

    // Création du fichier excel info depot et info stock
    public function get_export_excel_info_stock_depot($typePiece, $donneesStock, $donneesDepot)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Article_Informations_Stock');
        // Titre de la feuille
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);
        $sheet->getCell('A1')->setValue('Article_Informations_Stock - Article informations liées au stock ( ARTSTOC )');
        $sheet->getCell('A4')->setValue('Données');
        // Entête de colonne
        $sheet->getCell('A6')->setValue('DOSSIER'); // => 1
        $sheet->getCell('B6')->setValue('REFERENCE');
        $sheet->getCell('C6')->setValue('DEPOT'); // => 2
        $sheet->getCell('D6')->setValue('NATURESTOCK'); // => N
        $sheet->getCell('E6')->setValue('NUMERONOTE');
        $sheet->getCell('F6')->setValue('DATEINV');
        $sheet->getCell('G6')->setValue('FAMILLEINV');
        $sheet->getCell('H6')->setValue('PERIODEINV'); // => 20
        $sheet->getCell('I6')->setValue('INVIMPPAGCOD'); // => 1
        $sheet->getCell('J6')->setValue('INVPAGNB'); // => 0
        $sheet->getCell('K6')->setValue('CRITEREINV');
        $sheet->getCell('L6')->setValue('Anomalies');
        $sheet->getCell('M6')->setValue('Alertes');

        // Increase row cursor after header write
        $sheet->fromArray($this->getData_info_stock($donneesStock), null, 'A7', true);

        // Create a new worksheet called "My Data"
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Article_Informations_Depot');

        // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
        $spreadsheet->addSheet($myWorkSheet, 0);

        $sheetDepot = $spreadsheet->getSheetByName('Article_Informations_Depot');

        // Titre de la feuille
        $sheetDepot->getCell('A1')->getStyle()->getFont()->setSize(16);
        $sheetDepot->getCell('A1')->setValue('Article_Informations_Depot - Article informations dépôt ( ARTDEPO )');
        $sheetDepot->getCell('A4')->setValue('Données');
        // Entête de colonne
        $sheetDepot->getCell('A6')->setValue('DOSSIER'); // => 1
        $sheetDepot->getCell('B6')->setValue('REFERENCE');
        $sheetDepot->getCell('C6')->setValue('SREFERENCE1');
        $sheetDepot->getCell('D6')->setValue('SREFERENCE2');
        $sheetDepot->getCell('E6')->setValue('DEPOT'); // => 2
        $sheetDepot->getCell('F6')->setValue('NATURESTOCK'); // => N
        $sheetDepot->getCell('G6')->setValue('NUMERONOTE');
        $sheetDepot->getCell('H6')->setValue('EMPLACEMENT');
        $sheetDepot->getCell('I6')->setValue('STLGTSORCOD'); // 9
        $sheetDepot->getCell('J6')->setValue('FLAGORDOSMC'); // 1
        $sheetDepot->getCell('K6')->setValue('FAMRANGEMENT');
        $sheetDepot->getCell('L6')->setValue('ELIGIBLESLOTTING'); // 9
        $sheetDepot->getCell('M6')->setValue('WMPROFILCOD');
        $sheetDepot->getCell('N6')->setValue('WMEMPLPREP');
        $sheetDepot->getCell('O6')->setValue('RESJRNB'); // 0
        $sheetDepot->getCell('P6')->setValue('WMEMPCONTNB'); // 0
        $sheetDepot->getCell('Q6')->setValue('Anomalies');
        $sheetDepot->getCell('R6')->setValue('Alertes');

        // Increase row cursor after header write
        $sheetDepot->fromArray($this->getData_info_depot($donneesDepot), null, 'A7', true);

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = $typePiece . ' ' . $dateTime;

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
    public function getData_info_stock($donnees): array
    {
        $list = [];

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];
            $dt = new DateTime();

            $list[] = [
                $donnee['DOSSIER'],
                $donnee['REFERENCE'],
                $donnee['DEPOT'],
                "N", // NATURESTOCK
                $donnee['NUMERONOTE'],
                $dt->format('d/m/Y'), // DATEINV
                $donnee['FAMILLEINV'],
                20, // PERIODEINV
                1, // INVIMPPAGCOD
                0, // INVPAGNB
                $donnee['CRITEREINV'],
                $donnee['Anomalies'],
                $donnee['Alertes'],
            ];
        }
        return $list;
    }

    // générer un fichier Excel qui sera envoyé par mail à l'utilisateur
    public function getData_info_depot($donnees): array
    {
        $list = [];

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['DOSSIER'],
                $donnee['REFERENCE'],
                $donnee['SREFERENCE1'],
                $donnee['SREFERENCE2'],
                $donnee['DEPOT'],
                "N", // NATURESTOCK
                $donnee['NUMERONOTE'],
                $donnee['EMPLACEMENT'],
                9, // STLGTSORCOD
                1, // FLAGORDOSMC
                $donnee['FAMRANGEMENT'],
                9, // ELIGIBLESLOTTING
                $donnee['WMPROFILCOD'],
                $donnee['WMEMPLPREP'],
                0, // RESJRNB
                0, // WMEMPCONTNB
                $donnee['Anomalies'],
                $donnee['Alertes'],

            ];
        }
        return $list;
    }

    public function get_export_excel_stock($typePiece, $donnees)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Piece');
        // Entête de colonne
        $sheet->getCell('A3')->setValue('FICHE'); //IPAR
        $sheet->getCell('B3')->setValue('DOSSIER'); //IPAR
        $sheet->getCell('C3')->setValue('ETABLISSEMENT'); //IPAR
        $sheet->getCell('D3')->setValue('REF_PIECE'); //IPAR
        $sheet->getCell('E3')->setValue('CODE_TIERS'); //ENT
        $sheet->getCell('F3')->setValue('CODE_OP'); //ENT
        $sheet->getCell('G3')->setValue('DEPOT'); //ENT
        $sheet->getCell('H3')->setValue('DEPOT_DESTINATION'); //ENT
        $sheet->getCell('I3')->setValue('ENT.PIDT'); //ENT
        $sheet->getCell('J3')->setValue('ENT.PIREF'); //ENT
        $sheet->getCell('K3')->setValue('NO_SOUS_LIGNE'); //MOUV
        $sheet->getCell('L3')->setValue('REFERENCE'); //MOUV
        $sheet->getCell('M3')->setValue('SREF1'); //MOUV
        $sheet->getCell('N3')->setValue('SREF2'); //MOUV
        $sheet->getCell('O3')->setValue('DESIGNATION'); //MOUV
        $sheet->getCell('P3')->setValue('REF_FOURNISSEUR'); //MOUV
        $sheet->getCell('Q3')->setValue('MOUV.OP'); //MOUV
        $sheet->getCell('R3')->setValue('QUANTITE'); //MOUV
        $sheet->getCell('S3')->setValue('MOUV.PPAR'); //MOUV
        $sheet->getCell('T3')->setValue('MOUV.PUB'); //MOUV
        $sheet->getCell('U3')->setValue('EMPLACEMENT'); //MVTL
        $sheet->getCell('V3')->setValue('EMPLACEMENT_DESTINATION'); //MVTL
        $sheet->getCell('W3')->setValue('SERIE'); //MVTL
        $sheet->getCell('X3')->setValue('QUANTITE_VTL'); //MVTL
        $sheet->getCell('Y3')->setValue('ERREUR');

        // Information sur la piéce IPAR ET ENT
        $sheet->getCell('A4')->setValue('IPAR'); //IPAR
        $sheet->getCell('B4')->setValue('1'); //IPAR DOSSIER
        $sheet->getCell('C4')->setValue(''); //IPAR ETABLISSEMENT
        $sheet->getCell('D4')->setValue(''); //IPAR REF_PIECE
        $sheet->getCell('A5')->setValue('ENT'); //ENT
        $sheet->getCell('E5')->setValue('I0000000'); //ENT CODE_TIERS
        $sheet->getCell('F5')->setValue('JI'); //ENT CODE_OP
        $sheet->getCell('G5')->setValue('2'); //ENT DEPOT
        $sheet->getCell('H5')->setValue('2'); //ENT DEPOT_DESTINATION
        $sheet->getCell('I5')->setValue(new DateTime()); //ENT ENT.PIDT
        $sheet->getCell('J5')->setValue('Import xlsx par ' . $this->getUserPseudo()); //ENT ENT.PIREF

        // Increase row cursor after header write
        $sheet->fromArray($this->getData_stock($donnees), null, 'A6', true);

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = 'Import ' . $typePiece . ' ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('C1')->setValue('Mise à jour via Import du Stock');
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);

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
    public function getData_stock($donnees): array
    {
        $list = [];

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['FICHE'],
                $donnee['DOSSIER'],
                $donnee['ETABLISSEMENT'],
                $donnee['REF_PIECE'],
                $donnee['CODE_TIERS'],
                $donnee['CODE_OP'],
                $donnee['DEPOT'],
                $donnee['DEPOT_DESTINATION'],
                $donnee['ENT.PIDT'],
                $donnee['ENT.PIREF'],
                $donnee['NO_SOUS_LIGNE'],
                $donnee['REFERENCE'],
                $donnee['SREF1'],
                $donnee['SREF2'],
                $donnee['DESIGNATION'],
                $donnee['REF_FOURNISSEUR'],
                $donnee['MOUV.OP'],
                $donnee['QUANTITE'],
                $donnee['MOUV.PPAR'],
                $donnee['MOUV.PUB'],
                $donnee['EMPLACEMENT'],
                $donnee['EMPLACEMENT_DESTINATION'],
                $donnee['SERIE'],
                $donnee['QUANTITE_VTL'],
            ];
        }
        return $list;
    }

    public function get_export_excel_mouv_tiers($typePiece, $donnees, $tiers)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Piece');
        // Entête de colonne
        $sheet->getCell('A3')->setValue('FICHE'); //IPAR
        $sheet->getCell('B3')->setValue('DOSSIER'); //IPAR
        $sheet->getCell('C3')->setValue('ETABLISSEMENT'); //IPAR
        $sheet->getCell('D3')->setValue('REF_PIECE'); //IPAR
        $sheet->getCell('E3')->setValue('TYPE_TIERS'); //IPAR
        $sheet->getCell('F3')->setValue('TYPE_PIECE'); //IPAR
        $sheet->getCell('G3')->setValue('CODE_TIERS'); //ENT
        $sheet->getCell('H3')->setValue('CODE_OP'); //ENT
        $sheet->getCell('I3')->setValue('DEPOT'); //ENT
        $sheet->getCell('J3')->setValue('ENT.PIDT'); //ENT
        $sheet->getCell('K3')->setValue('ENT.PIREF'); //ENT
        $sheet->getCell('L3')->setValue('NO_SOUS_LIGNE'); //MOUV
        $sheet->getCell('M3')->setValue('REFERENCE'); //MOUV
        $sheet->getCell('N3')->setValue('SREF1'); //MOUV
        $sheet->getCell('O3')->setValue('SREF2'); //MOUV
        $sheet->getCell('P3')->setValue('DESIGNATION'); //MOUV
        $sheet->getCell('Q3')->setValue('REF_FOURNISSEUR'); //MOUV
        $sheet->getCell('R3')->setValue('MOUV.OP'); //MOUV
        $sheet->getCell('S3')->setValue('QUANTITE'); //MOUV
        $sheet->getCell('T3')->setValue('MOUV.PPAR'); //MOUV
        $sheet->getCell('U3')->setValue('MOUV.PUB'); //MOUV
        $sheet->getCell('V3')->setValue('EMPLACEMENT'); //MVTL
        $sheet->getCell('W3')->setValue('SERIE'); //MVTL
        $sheet->getCell('X3')->setValue('QUANTITE_VTL'); //MVTL
        $sheet->getCell('Y3')->setValue('ERREUR');

        // Information sur la piéce IPAR ET ENT
        $sheet->getCell('A4')->setValue('IPAR'); //IPAR
        $sheet->getCell('B4')->setValue('1'); //IPAR DOSSIER
        $sheet->getCell('C4')->setValue(''); //IPAR ETABLISSEMENT
        $sheet->getCell('D4')->setValue(''); //IPAR REF_PIECE
        $sheet->getCell('E4')->setValue(substr($tiers, 0, 1)); //IPAR TYPE_TIERS
        $sheet->getCell('F4')->setValue(substr($typePiece, 0, 1)); //IPAR TYPE_PIECE
        $sheet->getCell('A5')->setValue('ENT'); //ENT
        $sheet->getCell('G5')->setValue($tiers); //ENT CODE_TIERS
        $sheet->getCell('H5')->setValue(substr($tiers, 0, 1)); //ENT CODE_OP
        $sheet->getCell('I5')->setValue('2'); //ENT DEPOT
        $sheet->getCell('J5')->setValue(new DateTime()); //ENT ENT.PIDT
        $sheet->getCell('K5')->setValue('Import xlsx par ' . $this->getUserPseudo()); //ENT ENT.PIREF

        // Increase row cursor after header write
        $sheet->fromArray($this->getData_Mouv_tiers($donnees), null, 'A6', true);

        $d = new DateTime('NOW');
        $dateTime = $d->format('d-m-Y');
        $nomFichier = $typePiece . ' ' . $dateTime;
        // Titre de la feuille
        $sheet->getCell('C1')->setValue('Intégration piéce tiers');
        $sheet->getCell('A1')->getStyle()->getFont()->setSize(16);

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
    public function getData_Mouv_tiers($donnees): array
    {
        $list = [];

        for ($d = 0; $d < count($donnees); $d++) {

            $donnee = $donnees[$d];

            $list[] = [
                $donnee['FICHE'],
                $donnee['DOSSIER'],
                $donnee['ETABLISSEMENT'],
                $donnee['REF_PIECE'],
                $donnee['TYPE_TIERS'],
                $donnee['TYPE_PIECE'],
                $donnee['CODE_TIERS'],
                $donnee['CODE_OP'],
                $donnee['DEPOT'],
                $donnee['ENT.PIDT'],
                $donnee['ENT.PIREF'],
                $donnee['NO_SOUS_LIGNE'],
                $donnee['REFERENCE'],
                $donnee['SREF1'],
                $donnee['SREF2'],
                $donnee['DESIGNATION'],
                $donnee['REF_FOURNISSEUR'],
                $donnee['MOUV.OP'],
                $donnee['QUANTITE'],
                $donnee['MOUV.PPAR'],
                $donnee['MOUV.PUB'],
                $donnee['EMPLACEMENT'],
                $donnee['SERIE'],
                $donnee['QUANTITE_VTL'],
            ];
        }
        return $list;
    }
}
