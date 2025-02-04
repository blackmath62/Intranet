<?php
namespace App\Controller;

use App\Form\SearchAndFouCodeTarifType;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\MailListRepository;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class TarifVenteDivaltoController extends AbstractController
{

    private $repoMail;
    private $mailEnvoi;
    private $mailer;

    public function __construct(MailListRepository $repoMail, MailerInterface $mailer)
    {
        $this->repoMail  = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailer    = $mailer;
    }

    #[Route("/Lhermitte/tarif/vente/divalto", name: "app_tarif_vente_divalto")]

    public function index(MouvRepository $repo, Request $request, StatsAchatController $mef): Response
    {

        $tarifs = "";
        $title  = "Tarifs de Vente Lhermitte frères le " . (new DateTime())->format("d-m-Y") . ' édité par ' . $this->getUser()->getPseudo();
        $codes  = "";
        $year   = "";
        $form   = $this->createForm(SearchAndFouCodeTarifType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prefixe  = $form->getData()['search'];
            $fous     = $mef->miseEnForme($form->getData()['fournisseurs']);
            $familles = $mef->miseEnForme($form->getData()['familles']);
            $codes    = $form->getData()['codeTarif'];
            $lock     = $form->getData()['lock'];

            if ($form->get('filtrer')->isClicked()) {
                $tarifs = $repo->tarifsVentesDivalto($prefixe, $fous, $familles, $codes);
            }
            if ($form->get('exporter')->isClicked()) {
                $year = (int) $form->getData()['year'];

                $remise = $form->getData()['remise'];
                $tarifs = $repo->tarifsVentesDivaltoUneColonneTarif($prefixe, $fous, $familles, $codes, $year);
                if ($tarifs) {
                    $this->addFlash('message', 'Votre export a été réalisé avec succés ! Veuillez consulter votre boite mail.');
                    $this->generateExcel($tarifs, $title . '.xlsx', $lock, $remise);
                    return $this->redirectToRoute("app_tarif_vente_divalto");
                } else {
                    $this->addFlash('danger', 'Aucune donnée à extraire !');
                    return $this->redirectToRoute("app_tarif_vente_divalto");
                }

            }
        }

        return $this->render('tarif_vente_divalto/index.html.twig', [
            'tarifs' => $tarifs,
            'form'   => $form->createView(),
            'title'  => $title,
            'codes'  => $codes,
        ]);
    }

    private function generateExcel(array $data, string $fileName, $lock, $remise = null)
    {
        // Ajouter les données
        $row         = 11;
        $famille     = null;
        $spreadsheet = new Spreadsheet();
        $i           = 0;

        // Étape 1 : Extraire les codes
        $codes = array_column($data, 'code');
        // Étape 2 : Supprimer les doublons
        $codesUnique = array_unique($codes);
        // Étape 3 : Concaténer les codes avec une virgule
        $codesString = implode(',', $codesUnique);

        foreach ($data as $item) {
            $sheet = $spreadsheet->getActiveSheet();

            if ($famille != $item['famille']) {
                if ($row > 11) {

                    $range = 'A11:H' . $row - 1;

                    // Appliquer des bordures à une plage de cellules
                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,    // Style de bordure (ici, fine)
                                'color'       => ['argb' => 'FF000000'], // Couleur de la bordure (noir)
                            ],
                        ],
                    ]);

                    // Définir l'alignement du texte pour les colonnes C à H
                    $sheet->getStyle('C:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    // Appliquer un filtre sur une plage de cellules (par exemple, A1:H10)
                    $sheet->setAutoFilter("A10:H10");
                    // figer les volets
                    $sheet->freezePane('A11');

                    // Ajuster automatiquement la largeur des colonnes de A à H
                    foreach (range('A', 'H') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true);
                    }

                    // Pour la colonne F
                    $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0.000 €');

                    // Pour la colonne G
                    $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.000 €');

                    // Définir l'orientation en paysage
                    $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                    // Définir les marges étroites
                    $sheet->getPageMargins()->setTop(0.5);
                    $sheet->getPageMargins()->setRight(0.5);
                    $sheet->getPageMargins()->setBottom(0.5);
                    $sheet->getPageMargins()->setLeft(0.5);

                                                                // Ajuster les colonnes pour qu'elles tiennent sur une page
                    $sheet->getPageSetup()->setFitToPage(true); // Ajuster à une page
                    $sheet->getPageSetup()->setFitToWidth(1);   // Une page de large
                    $sheet->getPageSetup()->setFitToHeight(0);  // Aucune restriction de hauteur (peut être ajusté si nécessaire)

                    if ($lock == true) {
                        // Activer la protection de la feuille
                        $sheet->getProtection()->setSheet(true);

                        // Autoriser l'utilisation des filtres et du tri tout en protégeant la feuille
                        $sheet->getProtection()->setSort(false);
                        $sheet->getProtection()->setAutoFilter(false);

                        // Définir un mot de passe pour la protection (facultatif)
                        $sheet->getProtection()->setPassword('Lhermitte@62');
                    }

                    if (! $remise) {
                        // Supprimer la colonne Prix remisé
                        $sheet->removeColumn('G');
                    }

                    // On créé un nouvel onglet
                    $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $item['libelle']);
                    $spreadsheet->addSheet($myWorkSheet, 0);
                    $spreadsheet->setActiveSheetIndex(0); // L'index 0 est pour la première feuille ajoutée
                    $row = 11;
                }
                $sheet = $spreadsheet->getActiveSheet();
                // Ajuster la hauteur de la ligne 1 pour s'adapter à l'image si nécessaire
                $sheet->getRowDimension(1)->setRowHeight(100);

                $sheet->setCellValue('B1', $fileName);
                $sheet->setCellValue('B2', $codesString);

                // Création d'un nouvel objet Drawing pour l'image
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Entête Lhermitte');
                $drawing->setPath('C:\wamp64\www\Intranet\public\img\autre\entete L.F..png'); // Chemin de ton image
                $drawing->setHeight(200);                                                     // Hauteur de l'image
                $drawing->setCoordinates('A1');                                               // Position de l'image dans le fichier Excel

                // Ajouter l'image à la feuille active
                $drawing->setWorksheet($sheet);
                $sheet->getSheetView()->setZoomScale(60);
                $sheet->getRowDimension(7)->setVisible(false);
                $sheet->getRowDimension(9)->setVisible(false);

                $sheet->setTitle($item['libelle']);

                $sheet->setCellValue('A8', $item['libelle']);

                // Appliquer le style: taille augmentée et souligné
                $sheet->getStyle('A8')->applyFromArray([
                    'font' => [
                        'bold'      => true,                                                   // Optionnel : mettre en gras
                        'size'      => 22,                                                     // Taille de la police
                        'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
                    ],
                ]);

                if ($remise) {
                    $sheet->setCellValue('A6', 'Remise de ' . $remise . ' % est appliquée sur notre tarif public !');
                }
                // Fusionner les cellules de A6 à H6
                $sheet->mergeCells('A6:H6');
                // Appliquer le style: texte en rouge, taille augmentée et souligné
                $sheet->getStyle('A6')->applyFromArray([
                    'font'      => [
                        'bold'      => true,                                                   // Optionnel : mettre en gras
                        'color'     => ['argb' => 'FF800000'],                                 // Couleur rouge (ARGB: Alpha, Red, Green, Blue)
                        'size'      => 14,                                                     // Taille de la police
                        'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Centrer le texte
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,   // Centrer verticalement
                    ],
                ]);

                // Définir les en-têtes de colonnes (adapte en fonction des données)
                $sheet->setCellValue('A10', 'Référence');
                $sheet->setCellValue('B10', 'Désignation');
                $sheet->setCellValue('C10', 'Sref1');
                $sheet->setCellValue('D10', 'Sref2');
                $sheet->setCellValue('E10', 'Uv');
                $sheet->setCellValue('F10', 'Tarif public HT');
                $sheet->setCellValue('G10', 'Prix remisé HT');
                $sheet->setCellValue('H10', 'Tva');

                                               // Définir la couleur de fond (R16-V72-B97) en ARGB
                $backgroundColor = 'FF104857'; // ARGB (avec FF pour l'opacité)

                // Appliquer le style à la ligne A10:H10
                $sheet->getStyle('A10:H10')->applyFromArray([
                    'fill'      => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => $backgroundColor,
                        ],
                    ],
                    'font'      => [
                        'bold'  => true,
                        'color' => [
                            'argb' => Color::COLOR_WHITE, // Texte en blanc
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }

            $pu = $item['pu'];
            // garde fou si le tarif extrait n'est pas un Tarif public
            if (substr($item['code'], -2) !== 'TP' && $remise) {
                $pu = $pu / (1 - ($remise / 100));
            }

            if ($item['ppar'] > 0) {
                $pu = $pu / $item['ppar'];
            }

            $sheet->setCellValue('A' . $row, $item['ref']);
            $sheet->setCellValue('B' . $row, $item['designation']);
            $sheet->setCellValue('C' . $row, $item['sref1']);
            $sheet->setCellValue('D' . $row, $item['sref2']);
            $sheet->setCellValue('E' . $row, $item['uv']);
            $sheet->setCellValue('F' . $row, $pu);
            $sheet->setCellValue('G' . $row, $pu * (1 - ($remise / 100)));

            if ($item['tva'] == 1) {
                $tva = 20;
            } elseif ($item['tva'] == 2) {
                $tva = 10;
            } elseif ($item['tva'] == 7) {
                $tva = 5.5;
            } else {
                $tva = "à définir";
            }

            $sheet->setCellValue('H' . $row, $tva);

            $famille = $item['famille'];
            $i++;
            if ($i == count($data)) {
                $range = 'A11:H' . $row - 1;

                // Appliquer des bordures à une plage de cellules
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,    // Style de bordure (ici, fine)
                            'color'       => ['argb' => 'FF000000'], // Couleur de la bordure (noir)
                        ],
                    ],
                ]);

                // Définir l'alignement du texte pour les colonnes C à H
                $sheet->getStyle('C:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Appliquer un filtre sur une plage de cellules (par exemple, A1:H10)
                $sheet->setAutoFilter("A10:H10");
                // figer les volets
                $sheet->freezePane('A11');

                // Ajuster automatiquement la largeur des colonnes de A à H
                foreach (range('A', 'H') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Pour la colonne F
                $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0.000 €');

                // Pour la colonne G
                $sheet->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.000 €');

                // Définir l'orientation en paysage
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                // Définir les marges étroites
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.5);

                                                            // Ajuster les colonnes pour qu'elles tiennent sur une page
                $sheet->getPageSetup()->setFitToPage(true); // Ajuster à une page
                $sheet->getPageSetup()->setFitToWidth(1);   // Une page de large
                $sheet->getPageSetup()->setFitToHeight(0);  // Aucune restriction de hauteur (peut être ajusté si nécessaire)

                // Définir le format de la page en A4
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                                                                              // Ajouter des numéros de page
                                                                              // Vous pouvez ajouter des numéros de page dans l'en-tête ou le pied de page
                $sheet->getHeaderFooter()->setOddHeader('&C&P / &N');         // Centrer les numéros de page
                $sheet->getHeaderFooter()->setOddFooter('&L Page &P sur &N'); // Numéro de page avec texte à gauche

                if ($lock == true) {
                    // Activer la protection de la feuille
                    $sheet->getProtection()->setSheet(true);

                    // Autoriser l'utilisation des filtres et du tri tout en protégeant la feuille
                    $sheet->getProtection()->setSort(false);
                    $sheet->getProtection()->setAutoFilter(false);

                    // Définir un mot de passe pour la protection (facultatif)
                    $sheet->getProtection()->setPassword('Lhermitte@62');
                }

                if (! $remise) {
                    // Supprimer la colonne Prix remisé
                    $sheet->removeColumn('G');
                }
            }

            $row++;
        }

        // On créé un nouvel onglet
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'CGV');
        $spreadsheet->addSheet($myWorkSheet, 0);
        $spreadsheet->setActiveSheetIndex(0); // L'index 0 est pour la première feuille ajoutée
        $sheet = $spreadsheet->getActiveSheet();

        // Création d'un nouvel objet Drawing pour l'image
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Entête Lhermitte');
        $drawing->setPath('C:\wamp64\www\Intranet\public\img\autre\entete L.F..png'); // Chemin de ton image
        $drawing->setHeight(200);                                                     // Hauteur de l'image
        $drawing->setCoordinates('A1');                                               // Position de l'image dans le fichier Excel

        // Ajouter l'image à la feuille active
        $drawing->setWorksheet($sheet);
        // Ajuster la hauteur de la ligne 1 pour s'adapter à l'image si nécessaire
        $sheet->getRowDimension(1)->setRowHeight(90);
        $sheet->getSheetView()->setZoomScale(60);

        // figer les volets
        $sheet->freezePane('A11');
                                                        // Définir la largeur de la colonne A
        $sheet->getColumnDimension('A')->setWidth(180); // Par exemple, 20 unités de largeur
                                                        // Activer les retours à la ligne pour toutes les cellules de la colonne A
        $sheet->getStyle('A')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('A10', 'EXTRAIT DE NOS CONDITIONS GENERALES DE VENTE');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A10')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 22,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A12', 'Objet et champ d’application');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A12')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A14', 'Toute commande de produits implique l’acceptation sans réserve par 1’acheteur et son adhésion pleine et entière aux conditions générales de vente qui prévalent sur tout autre document de l’acheteur, et notamment sur toutes conditions générales d’achat, sauf accord dérogatoire exprès et préalable de la société LHERMITTE FRERES. La vente ne sera définitive et ferme qu’à compter de la signature par LHERMITTE FRERES, de la confirmation de la commande. L’acceptation de la vente résulte d’une acceptation expresse de la société LHERMITTE FRERES. Le CLIENT confirme qu’il a tenu compte des lois et réglementations en vigueur concernant le permis de construire et l’implantation des serres, si cette prestation n’est pas clairement incluse dans les prestations du vendeur. Au moment de la signature, du bon de commande, le CLIENT est tenu de verser une somme équivalant à 20% du montant global de la commande, à titre d’acompte encaissé.');
        $sheet->setCellValue('A16', 'Agrément Phytopharmaceutique');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A16')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A18', 'L’entreprise dispose d’un certificat d’agrément permettant la mise en vente de produit phytopharmaceutique aux professionnelles. L’entreprise est auditée dans les délais décrit par la loi pour maintenir cet agrément à jour. L’entreprise dispose d’une assurance « RESPONSABILITE CIVILE DES ETABLISSEMENTS EXERCANT DES ACTIVITES DE MISE EN VENTE DE PRODUITS PHYTOPHARMACEUTIQUES » renouvelé tous les ans.');
        $sheet->setCellValue('A20', 'Livraisons');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A20')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A22', 'Les délais de livraison ne sont donnés qu’à titre informatif et indicatif.
												Le transfert des risques sur les produits vendus par notre société s’effectue à la remise des produits au transporteur ou à la sortie de nos entrepôts.
												Il appartient au CLIENT, en cas d’avarie des marchandises livrées ou de manquants, d’effectuer toutes les réserves nécessaires auprès du transporteur.
												Tout produit n’ayant pas fait l’objet de réserves par lettre recommandée avec Accusé Réception dans les 3 jours de sa réception auprès du transporteur, conformément à l’article L. 133-3 du code de commerce, et dont copie sera adressée simultanément à la société LHERMITTE FRERES, Parc d’activités de la Croisette, 25 rue de l’Abbé Jerzy Popiélusko, CS 80 412, 62 335 Lens Cedex, sera considéré accepté par le CLIENT.');
        $sheet->setCellValue('A24', 'Tarif — Prix — Modalités de paiement');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A24')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A26', 'Nos prix sont fixés par le tarif en vigueur au jour de la passation de la commande. Ils s’entendent toujours hors taxes. Ils pourront être revus à la hausse en cours d’année, après information préalable de nos CLIENTS.
													Toute modification tarifaire sera automatiquement applicable à la date indiquée sur le nouveau tarif.
													Les prix sont calculés nets et payables au plus tard à 60 jours à compter de la date d’émission de la facture selon les modalités suivantes :
												Chèque ou virement au plus tard à 60 jours.Ou par traite acceptée au plus tard à 60 jours, celle-ci devant nous être retournée dans les 10 jours.
												Aucun escompte pour paiement anticipé ne sera accordé.
												Pour toute première commande, un paiement comptant sera exigé (encaissement de la somme globale représentant la commande).
												Sauf en cas de livraison franco de port dans les conditions indiquées sur le tarif en vigueur, les frais de transport sont à la charge du CLIENT.
												Les frais de port s’élèvent à 25 € pour toutes commandes d’un montant inférieur au franco, soit 500 € HT.
												En cas de retard de paiement, l’acheteur sera redevable après mise en demeure par lettre recommandée, d’une pénalité calculée par application à l’intégralité des sommes restant dues, d’un taux égal au taux de refinancement de la banque centrale européenne (BCE) majoré de 10 points de pourcentage. Si, lors d’une précédente commande, le CLIENT, s’est soustrait à l’une de ses obligations (défaut ou retard de paiement, ces exemples n’étant pas limitatifs), un refus de vente pourra lui être opposé, à moins que cet acheteur ne fournisse des garanties suffisantes, satisfaisantes, ou un paiement comptant (encaissement avant départ de la marchandise).
												Le recours au service d’un organisme de recouvrement ou la voie judiciaire pour obtenir le règlement de factures impayées entraînera l’application d’une majoration de 10% des sommes restant dues ou d’une somme forfaitaire de 76.22 euros minimum, à titre de clause pénale et sans préjudice de tous intérêts moratoires, frais, accessoires, et frais irrépétibles engagés.
												Selon l’article 121 de la loi n°2012-387 adoptée le 23 mars 2012, une indemnité forfaitaire pour frais de recouvrement sera due à raison de 40€ (Article D441-5 du code du commerce) en cas de retard de paiement.');
        $sheet->setCellValue('A28', 'Réserve de propriété');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A28')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A30', 'Le transfert de propriété de nos produits est suspendu jusqu’à complet paiement du prix de ceux-ci par le CLIENT, en principal et accessoires, même en cas d’octroi de délais de paiement. Toute clause contraire, notamment insérée dans les conditions générales d’achat, est réputée non écrite, conformément à l’article L. 624-16 du code de commerce.
												De convention expresse, notre société pourra faire jouer les droits qu’elle détient au titre de la présente clause de réserve de propriété, pour l’une quelconque de ses créances, sur la totalité de ses produits en possession du CLIENT, ces derniers étant conventionnellement présumés être ceux impayés, et notre société pourra les reprendre ou les revendiquer en dédommagement de toutes ses factures impayées, sans préjudice de son droit de résolution des ventes en cours.																																																						Nonobstant la présente clause de réserve de propriété, les risques de perte, de vol, de détérioration des marchandises sont à la charge exclusive de l’acheteur dès la livraison des marchandises, la signature du récépissé transport faisant foi.');
        $sheet->setCellValue('A32', 'Défaut de conformité et conditions générales de garantie');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A32')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A34', 'Dès l’achèvement des travaux, il sera procédé à leur réception définitive, cette date de réception ou la date de paiement complet des travaux sera la date de départ de la garantie.
												Le CLIENT doit vérifier les matériels à la livraison, ce contrôle devant notamment porter sur la qualité, les quantités et les références des marchandises et leur conformité à la commande. Aucune réclamation n’est prise en compte passé le délai de 8 jours à compter du jour de livraison.
												Le matériel, comportant un défaut de conformité signalé dans le délai sus-indiqué et reconnu, fait l’objet d’un replacement ou d’une remise en état, à l’exclusion de tout dédommagement, à quelque titre que ce soit.
												La garantie est strictement limitée aux matériels affectés d’un vice de fabrication, au sens de l’article 1641 du code civil, 1.	La garantie comprend, gratuitement, l’échange des pièces défectueuses, ainsi que les frais de main d’œuvre. 1.1.	Le vice de fabrication doit apparaître dans une période de 6 mois à compter de la livraison. 1.2.	Tous les matériels sont réputés réparables par l’intermédiaire de nos monteurs.
												2.	Toutefois, la société LHERMITTE FRERES, ne sera tenue à aucune garantie gratuite pour toutes les causes qui ne résultent pas d’une utilisation normale.A titre d’exemple et sans que cette liste ne soit limitative, les cas suivants ne seront pas garantis :
												Accidents, chocs, surtensions, foudre, inondation, incendie et toutes causes autres que celles résultant d’une utilisation normale.
												Mauvais fonctionnement résultant d’adjonction de pièces ou dispositifs ne provenant pas de la société LHERMITTE FRERES.
												Défaillance ou variation du courant électrique.
												Modification des spécifications des appareils, ou matériels (serres, systèmes d’arrosage…), déplacement de l’installation, ou de l’appareil, des matériels.
												Difficultés d’utilisation dues à des causes relevant de la force majeure, du fait d’un tiers ou de causes externes.
												3.	Dès lors que la société LHERMITTE FRERES aura rempli ses obligations de garantie, la société LHERMITTE FRERES ne saura être tenue pour responsable des dommages directs ou indirects par suite de la défaillance du matériel vendu. 4.	En outre, il est expressément convenu entre la société LHERMITTE FRERES et le CLIENT que l’engagement de garantie de la société LHERMITTE FRERES, sera suspendu automatiquement sans qu’il soit besoin d’aucune notification par la société LHERMITTE FRERES, au CLIENT dès lors que celui-ci n’aura pas satisfait à l’obligation de payer le prix total du matériel aux échéances prévues et convenues.
												5.	Les échanges des pièces ou éventuellement leur remise en état, au titre de la garantie gratuite ne peuvent avoir comme effet de prolonger cette garantie gratuite.
												6.	L’application de la garantie gratuite ne peut en aucun cas obliger la société LHERMITTE FRERES, à une reprise du, des matériels.');
        $sheet->setCellValue('A36', 'Attribution de juridiction');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A36')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A38', 'L’élection de domicile est faite par la société LHERMITTE FRERES, à son siège social : Parc d’activités de la Croisette, 25 rue de l’Abbé Jerzy Popiélusko, CS 80 412, 62 335 Lens Cedex.
												Tout différent au sujet de l’application des présentes conditions générales de vente et de leur interprétation, de leur exécution et des contrats de vente conclus par notre société, ou au paiement du prix sera porté devant le tribunal de commerce du siège de notre société, quel que soit le lieu de la commande, de la livraison, et du paiement et le mode de paiement, et même en cas d’appel en garantie ou de pluralité de défendeurs.');
        $sheet->setCellValue('A40', 'Annexe aux CGV relative à la protection des données à caractère personnel');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A40')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A42', '1. Objet et portée. Conformément au Règlement Général sur la Protection des Données 2016/679 du 27 avril 2016, notre société informe ses clients du traitement des données à caractère personnel (« données personnelles ») collectées. La demande d’ouverture de compte et la passation de toutes commandes de nos produits emporte acceptation des termes de la présente annexe à nos conditions générales de vente.
								2. Responsable du traitement et destinataires. Les données personnelles des personnes physiques agissant pour le compte du client, collectées à notre demande par notre société, sont traitées par notre société en qualité de responsable du traitement. Y ont accès les services support (administration des ventes, commerce, logistique, crédit management, comptabilité informatique et marketing) de notre société. Les données personnelles peuvent être rendues accessibles aux sociétés d’audit et de contrôle de notre société et à nos prestataires techniques, juridiques et logistiques (« sous-traitants » au sens de la réglementation), pour les stricts besoins de leur mission. Nos sociétés affiliées peuvent également en être destinataires.
								3. Caractéristiques du traitement. La collecte des données personnelles (contacts commerciaux ou comptables notamment) est nécessaire à l’exécution de nos relations commerciales ; sans ces données, votre compte ne pourra être créé et nous ne pourrons exécuter nos obligations contractuelles relatives au traitement de vos commandes et de ses conséquences. Les données personnelles peuvent également être utilisées (i) avec le consentement des personnes concernées, pouvant être retiré à tout moment, pour l’organisation de jeux-concours, loteries ou autres opérations promotionnelles, ou (ii) sauf opposition de la part des personnes concernées, et dans la limite de leurs intérêts et droits, pour répondre aux besoins légitimes de notre société en matière de prospection
								Commerciale, réalisation d’études, sondages ou tests produits, de statistiques commerciales, gestion de vos avis sur nos produits, et ce, aux fins d’amélioration de nos produits, d’analyse statistique ou de marketing, ou encore (iii) pour répondre à nos obligations légales, comptables ou fiscales (de gestion des demandes de droit d’accès, de rectification et d’opposition ou de tenue d’une liste d’opposition à la prospection notamment).
								4. Conservation. Les données personnelles sont conservées pendant la durée nécessaire à la gestion de la relation commerciale et pendant 3 ans après l’exécution de votre dernière commande et/ou, à des fins de prospection commerciale, pendant 3 années à compter de notre dernier contact.
								5. Transfert. Notre société ne transfère pas les données personnelles vers un pays tiers à l’Espace Economique Européen.
								6. Information des personnes physiques concernées. Le client fait son affaire d’informer les personnes concernées de son entreprise du traitement des données personnelles mis en œuvre par notre société aux fins d’exercice de leurs droits. Notre responsabilité ne pourra être engagée en cas d’absence d’information des personnes concernées.
								7. Droits des personnes. Dans les cas et selon les limites prévues par la réglementation, les personnes physiques de l’entreprise du client dont notre Société traite les données personnelles disposent d’un droit d’accès aux données qui les concernent, du droit d’en demander la rectification, l’effacement ou la portabilité, ainsi que du droit de demander la limitation du traitement de leurs données personnelles, de s’y opposer et de retirer leur consentement. Ces droits peuvent être exercés à tout moment auprès du relais du délégué à la protection des données à l’adresse du siège social ou à contact@lhermitte.fr, en joignant tout justificatif d’identité.
								8. Coordonnées du Délégué à la Protection des Données (DPO). Toute demande peut être adressée à notre relais DPO à : contact@lhermitte.fr. En cas de difficulté non résolue, vous pouvez contacter l’autorité de contrôle compétente (la CNIL en France).');
        $sheet->setCellValue('A44', 'ANNEXE FINANCIERE');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A44')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 16,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A46', 'Révision des taux de révision du marché public');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A46')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A48', 'Conformément à l\'article R.2194-5 du Code de la commande publique, on se réserve le droit de réviser les taux de révision du marché public en cas de :');
        // Appliquer le style: taille augmentée et souligné
        $sheet->getStyle('A48')->applyFromArray([
            'font' => [
                'bold'      => true,                                                   // Optionnel : mettre en gras
                'size'      => 14,                                                     // Taille de la police
                'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE, // Soulignement simple
            ],
        ]);
        $sheet->setCellValue('A50', 'Évolution significative des prix des matières premières : Il s\'agit notamment des cas où les prix des matières premières subissent une variation notable, supérieure à un seuil fixé contractuellement, sur une période déterminée. Cette variation peut être due à des facteurs exogènes tels que des crises internationales, des catastrophes naturelles ou des fluctuations des cours mondiaux.');

        $sheet->setCellValue('A51', 'Modification des dispositions réglementaires ou fiscales : Il s\'agit notamment des cas où des modifications législatives ou réglementaires impactent significativement les coûts de production ou de prestation du titulaire du marché. Ces modifications peuvent concerner, par exemple, les normes de sécurité, les exigences environnementales ou la fiscalité applicable au secteur d\'activité du titulaire.');

        $sheet->setCellValue('A52', 'Événements imprévisibles : Il s\'agit notamment des cas où des événements extraordinaires, tels que des catastrophes naturelles ou des crises sanitaires, surviennent en cours d\'exécution du marché et affectent de manière conséquente les conditions économiques du contrat.');

        $sheet->setCellValue('A54', 'Dans l\'hypothèse où l\'une des situations susmentionnées se concrétiserait, on se concertera afin de déterminer les nouveaux taux de révision applicables. Ces nouveaux taux devront tenir compte de l\'évolution des coûts supportés et préserver l\'équilibre économique du contrat.');

        // Définir l'orientation en paysage
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);

        // Définir les marges étroites
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.5);

                                                    // Ajuster les colonnes pour qu'elles tiennent sur une page
        $sheet->getPageSetup()->setFitToPage(true); // Ajuster à une page
        $sheet->getPageSetup()->setFitToWidth(1);   // Une page de large
        $sheet->getPageSetup()->setFitToHeight(0);  // Aucune restriction de hauteur (peut être ajusté si nécessaire)
                                                    // Définir le format de la page en A4
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                                                                      // Ajouter des numéros de page
                                                                      // Vous pouvez ajouter des numéros de page dans l'en-tête ou le pied de page
        $sheet->getHeaderFooter()->setOddHeader('&C&P / &N');         // Centrer les numéros de page
        $sheet->getHeaderFooter()->setOddFooter('&L Page &P sur &N'); // Numéro de page avec texte à gauche

        if ($lock == true) {
            // Activer la protection de la feuille
            $sheet->getProtection()->setSheet(true);

            // Définir un mot de passe pour la protection (facultatif)
            $sheet->getProtection()->setPassword('Lhermitte@62');
        }

        $writer = new Xlsx($spreadsheet);

        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->getUser()->getEmail())
            ->subject('Votre export de tarifs publics est arrivé ! ')
            ->text('Veuillez trouver votre fichier Excel en pièce jointe.')
            ->attachFromPath($temp_file, $fileName);

        // Envoyez l'e-mail avec le fichier Excel en pièce jointe
        $this->mailer->send($email);

        // Supprimez le fichier temporaire après l'envoi de l'e-mail
        unlink($temp_file);
    }
}
