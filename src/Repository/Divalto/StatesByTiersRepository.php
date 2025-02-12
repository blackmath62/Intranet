<?php
namespace App\Repository\Divalto;

use App\Controller\StatesParFamilleController;
use App\Entity\Divalto\Mouv;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StatesByTiersRepository extends ServiceEntityRepository
{
    private $artBan;
    private $statesParFamilleController;

    public function __construct(ManagerRegistry $registry, StatesParFamilleController $statesParFamilleController)
    {
        parent::__construct($registry, Mouv::class);
        $artBan                           = "'ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP','ECOCONTRIBUTION10', 'ECOCONTRIBUTION10EV', 'ECOCONTRIBUTION20'";
        $this->artBan                     = $artBan;
        $this->statesParFamilleController = $statesParFamilleController;
    }

    // bandeau avec les states par commerciaux
    public function getStatesTotauxParCommerciaux($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT Periode AS Periode,SecteurMouvement AS SecteurMouvement, Commercial, CommercialId,COUNT(DISTINCT Bl) AS NbBl,COUNT(DISTINCT Facture) AS NbFacture,COUNT(DISTINCT Tiers) AS NbTiers,
        SUM(MontantSignDepot) As CADepot,  SUM(MontantSignDirect) As CADirect,  SUM(MontantSign) As CATotal
        FROM(	SELECT YEAR(MOUV.FADT) AS Annee,MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
                CASE
                WHEN MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN 'PeriodeN1'
                WHEN MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN 'PeriodeN'
                END AS Periode,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.NOM
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                END AS Commercial,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.TIERS
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
                END AS CommercialId,
                MOUV.TIERS AS Tiers,
                LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C', 'C 2','CO') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('D', 'D 2','DO') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDepot,
                CASE
                WHEN MOUV.OP IN('CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDirect,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSign
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP WITH (INDEX = INDEX_C_VRP) ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan)

                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN('EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL')
                AND ((MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' ) OR (MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY Commercial,CommercialId,SecteurMouvement, Periode
        ORDER BY Commercial,SecteurMouvement, Periode";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States Excel par commercial
    public function getStatesExcelParCommercial($metiers, $dateDebutN, $dateFinN, $commercial, $dossier): array
    {
        $dateDebutN1 = date_create($dateDebutN);
        $dateDebutN1 = date_modify($dateDebutN1, '-1 Year');
        $dateDebutN1 = $dateDebutN1->format('Y') . '-' . $dateDebutN1->format('m') . '-' . $dateDebutN1->format('d');
        $dateFinN1   = date_create($dateFinN);
        $dateFinN1   = date_modify($dateFinN1, '-1 Year');
        $dateFinN1   = $dateFinN1->format('Y') . '-' . $dateFinN1->format('m') . '-' . $dateFinN1->format('d');

        $dateDebutN2 = date_create($dateDebutN1);
        $dateDebutN2 = date_modify($dateDebutN2, '-1 Year');
        $dateDebutN2 = $dateDebutN2->format('Y') . '-' . $dateDebutN2->format('m') . '-' . $dateDebutN2->format('d');
        $dateFinN2   = date_create($dateFinN1);
        $dateFinN2   = date_modify($dateFinN2, '-1 Year');
        $dateFinN2   = $dateFinN2->format('Y') . '-' . $dateFinN2->format('m') . '-' . $dateFinN2->format('d');

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT LTRIM(RTRIM(Commercial)) AS Commercial, LTRIM(RTRIM(Famille_Client)) AS Famille_Client, LTRIM(RTRIM(Client)) AS Client, LTRIM(RTRIM(nom)) as Nom,LTRIM(RTRIM(Pays)) as Pays,
        LTRIM(RTRIM(Famille_Article)) AS Fam_Art, LTRIM(RTRIM(Ref)) AS Ref,LTRIM(RTRIM(Designation)) AS Designation,
        LTRIM(RTRIM(Sref1)) AS Sref1,LTRIM(RTRIM(Sref2)) AS Sref2, LTRIM(RTRIM(UV)) AS Uv,Mois,
        LTRIM(RTRIM(SUM(QteSignN1))) AS QteSignN1, LTRIM(RTRIM(SUM(MontantSignN1))) AS MontantSignN1,
        LTRIM(RTRIM(SUM(QteSignN))) AS QteSignN, LTRIM(RTRIM(SUM(MontantSignN))) AS MontantSignN,
        LTRIM(RTRIM(SUM(QteSignN2))) AS QteSignN2, LTRIM(RTRIM(SUM(MontantSignN2))) AS MontantSignN2
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT CLI.STAT_0001 AS Famille_Client, MOUV.TIERS as Client, CLI.NOM AS nom, CLI.PAY AS Pays, MOUV.DEV AS Devise,MONTH(MOUV.FADT) AS Mois, MOUV.OP,MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2,MOUV.VENUN AS UV, MOUV.FAQTE AS Qte,MOUV.MONT AS Montant,
        MOUV.REMPIEMT_0004 AS Remise,MOUV.FADT AS DateFacture,MOUV.FANO AS Facture, ART.FAM_0001 AS Famille_Article,
                        CASE
                        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                        WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                        ELSE 'WTF !'
                        END AS SecteurMouvement,
                        CASE
                        WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.NOM
                        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                        END AS Commercial,
                        CASE
                        WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.TIERS
                        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
                        END AS CommercialId,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND  MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND  MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN,
        CASE
            WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND  MOUV.FADT <= '$dateFinN' THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND  MOUV.FADT <= '$dateFinN' THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSignN,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <=  '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <=  '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN1,
        CASE
            WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <=  '$dateFinN1' THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <=  '$dateFinN1' THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSignN1,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN2,
        CASE
            WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSignN2
        FROM MOUV
        INNER JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND ART.DOS = MOUV.DOS
        INNER JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND CLI.DOS = MOUV.DOS
        LEFT JOIN VRP WITH (INDEX = INDEX_C_VRP) ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
        WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan)
        AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN'
        AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN('EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL')
        AND MOUV.OP IN('C','CD','DD','D', 'C 2','CO', 'D 2','DO')) Reponse
        WHERE SecteurMouvement IN( $metiers ) AND CommercialId IN ($commercial)
        GROUP BY Commercial, Famille_Client, Client, nom,Pays, Famille_Article, Ref,Designation,Sref1,Sref2,UV, Mois
        ORDER BY Client";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States Excel par metier
    public function getStatesExcelParMetier($metiers, $dateDebutN, $dateFinN, $dossier): array
    {
        $dateDebutN1 = date_create($dateDebutN);
        $dateDebutN1 = date_modify($dateDebutN1, '-1 Year');
        $dateDebutN1 = $dateDebutN1->format('Y') . '-' . $dateDebutN1->format('m') . '-' . $dateDebutN1->format('d');
        $dateFinN1   = date_create($dateFinN);
        $dateFinN1   = date_modify($dateFinN1, '-1 Year');
        $dateFinN1   = $dateFinN1->format('Y') . '-' . $dateFinN1->format('m') . '-' . $dateFinN1->format('d');

        $dateDebutN2 = date_create($dateDebutN1);
        $dateDebutN2 = date_modify($dateDebutN2, '-1 Year');
        $dateDebutN2 = $dateDebutN2->format('Y') . '-' . $dateDebutN2->format('m') . '-' . $dateDebutN2->format('d');
        $dateFinN2   = date_create($dateFinN1);
        $dateFinN2   = date_modify($dateFinN2, '-1 Year');
        $dateFinN2   = $dateFinN2->format('Y') . '-' . $dateFinN2->format('m') . '-' . $dateFinN2->format('d');

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT LTRIM(RTRIM(Commercial)) AS Commercial, LTRIM(RTRIM(Famille_Client)) AS Famille_Client, LTRIM(RTRIM(Client)) AS Client, LTRIM(RTRIM(nom)) as Nom,LTRIM(RTRIM(Pays)) as Pays,
        LTRIM(RTRIM(Famille_Article)) AS Fam_Art, LTRIM(RTRIM(Ref)) AS Ref,LTRIM(RTRIM(Designation)) AS Designation,
        LTRIM(RTRIM(Sref1)) AS Sref1,LTRIM(RTRIM(Sref2)) AS Sref2, LTRIM(RTRIM(UV)) AS Uv,Mois,
        LTRIM(RTRIM(SUM(QteSignN1))) AS QteSignN1, LTRIM(RTRIM(SUM(MontantSignN1))) AS MontantSignN1,
        LTRIM(RTRIM(SUM(QteSignN))) AS QteSignN, LTRIM(RTRIM(SUM(MontantSignN))) AS MontantSignN,
        LTRIM(RTRIM(SUM(QteSignN2))) AS QteSignN2, LTRIM(RTRIM(SUM(MontantSignN2))) AS MontantSignN2
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT CLI.STAT_0001 AS Famille_Client, MOUV.TIERS as Client, CLI.NOM AS nom, CLI.PAY AS Pays, MOUV.DEV AS Devise,MONTH(MOUV.FADT) AS Mois, MOUV.OP,MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2,MOUV.VENUN AS UV, MOUV.FAQTE AS Qte,MOUV.MONT AS Montant,
        MOUV.REMPIEMT_0004 AS Remise,MOUV.FADT AS DateFacture,MOUV.FANO AS Facture, ART.FAM_0001 AS Famille_Article,
                        CASE
                        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                        WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                        ELSE 'WTF !'
                        END AS SecteurMouvement,
                        CASE
                        WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.NOM
                        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                        END AS Commercial,
                        CASE
                        WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.TIERS
                        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
                        END AS CommercialId,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN,
        CASE
            WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSignN,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN1,
        CASE
            WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSignN1,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN2,
        CASE
            WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSignN2
        FROM MOUV
        INNER JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND ART.DOS = MOUV.DOS
        INNER JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND CLI.DOS = MOUV.DOS
        LEFT JOIN VRP WITH (INDEX = INDEX_C_VRP) ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
        WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan)
        AND ((MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' ) OR (MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' ) OR (MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' ) )
        AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN('EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL')

        AND MOUV.OP IN('C','CD','DD','D', 'C 2','CO', 'D 2','DO')) Reponse
        WHERE SecteurMouvement IN( $metiers )
        GROUP BY Commercial, Famille_Client, Client, nom,Pays, Famille_Article, Ref,Designation,Sref1,Sref2,UV, Mois
        ORDER BY Client";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // NOM DU COMMERCIAL

    // States Excel par commercial
    public function getCommercialName($commercialid, $dossier)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT LTRIM(RTRIM(VRP.SELCOD)) AS SELCOD FROM VRP
        WHERE VRP.TIERS = $commercialid AND VRP.DOS = $dossier";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States TOP 10 Familles produits
    public function getTop10FamillesProduits($dateDebutN, $dateFinN, $dossier)
    {
        $dateDebutN1 = date_create($dateDebutN);
        $dateDebutN1 = date_modify($dateDebutN1, '-1 Year');
        $dateDebutN1 = $dateDebutN1->format('Y') . '-' . $dateDebutN1->format('m') . '-' . $dateDebutN1->format('d');
        $dateFinN1   = date_create($dateFinN);
        $dateFinN1   = date_modify($dateFinN1, '-1 Year');
        $dateFinN1   = $dateFinN1->format('Y') . '-' . $dateFinN1->format('m') . '-' . $dateFinN1->format('d');

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT TOP 5 LTRIM(RTRIM(Famille_Article)) AS Fam_Article,
		LTRIM(RTRIM(SUM(MontantSignN1))) AS MontantSignN1,
        LTRIM(RTRIM(SUM(MontantSignN))) AS MontantSignN
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT MOUV.MONT AS Montant,
        MOUV.REMPIEMT_0004 AS Remise, ART.FAM_0001 AS Famille_Article,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN1
        FROM MOUV
        INNER JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND ART.DOS = MOUV.DOS
        WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan) AND ((MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' ) OR (MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' ) )
        AND ART.FAM_0002 IN('EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL') AND ART.FAM_0001 NOT IN ('REMISE')

        AND MOUV.OP IN('C','CD','DD','D', 'C 2','CO', 'D 2','DO')) Reponse
        GROUP BY Famille_Article
        ORDER BY SUM(MontantSignN) DESC";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Bandeau avec CA du secteur d'extraction
    public function getStatesTotauxParSecteur($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT Periode AS Periode,SecteurMouvement AS SecteurMouvement, COUNT(DISTINCT Bl) AS NbBl,COUNT(DISTINCT Facture) AS NbFacture,COUNT(DISTINCT Tiers) AS NbTiers,
        SUM(MontantSignDepot) As CADepot,  SUM(MontantSignDirect) As CADirect,  SUM(MontantSign) As CATotal
        FROM(	SELECT YEAR(MOUV.FADT) AS Annee,MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
                CASE
                WHEN MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN 'PeriodeN1'
                WHEN MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN 'PeriodeN'
                END AS Periode,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                MOUV.TIERS AS Tiers,
                LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C', 'C 2','CO') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('D', 'D 2','DO') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDepot,
                CASE
                WHEN MOUV.OP IN('CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDirect,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSign
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP WITH (INDEX = INDEX_C_VRP) ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan)

                AND CLI.STAT_0002 IN( 'EV','HP','RB' ) AND ART.FAM_0002 IN( 'EV','HP','ME','MO', 'RB', 'D', 'RG', 'RL', 'S', 'BL' )
                AND ((MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' ) OR (MOUV.FADT >= '$dateDebutN1'  AND MOUV.FADT <= '$dateFinN1' )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY SecteurMouvement, Periode
        ORDER BY SecteurMouvement, Periode";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getStatesDetailClient($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT SecteurMouvement AS SecteurMouvement, Commercial AS Commercial,commercialId AS commercialId, Tiers AS Tiers, Nom AS Nom,  SUM(MontantSignN1) As CATotalN1,  SUM(MontantSignN) As CATotalN
        FROM(	SELECT MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.NOM
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                END AS Commercial,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.TIERS
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
                END AS commercialId,
                LTRIM(RTRIM(MOUV.TIERS)) AS Tiers,CLI.NOM AS Nom,LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP WITH (INDEX = INDEX_C_VRP) ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan)

                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL' )
                AND ((MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN') OR (MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY Commercial,commercialId,SecteurMouvement, Tiers, Nom
        ORDER BY Commercial";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // CA Par métiers
    public function getStatesMetier($dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT	SecteurMouvement AS SecteurMouvement,Color AS Color,Icon AS Icon,  SUM(MontantSignN1) As CATotalN1,  SUM(MontantSignN) As CATotalN
        FROM(	SELECT MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'success'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'danger'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'orange'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'warning'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'primary'
                ELSE 'WTF !'
                END AS Color,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'fas fa-tree'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'fas fa-seedling'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'fas fa-carrot'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'fas fa-rainbow'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'fas fa-tree'
                ELSE 'WTF !'
                END AS Icon,
                LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                ELSE 0
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan)

                AND CLI.STAT_0002 IN('EV','HP', 'RB') AND ART.FAM_0002 IN('EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL')
                AND ((MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN') OR (MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' )))reponse
        GROUP BY SecteurMouvement, Color,Icon
        ORDER BY SecteurMouvement";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getStatesLhermitteByArticles($tiers, $metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {
        $dateDebutN2 = date_create($dateDebutN1);
        $dateDebutN2 = date_modify($dateDebutN2, '-1 Year');
        $dateDebutN2 = $dateDebutN2->format('Y') . '-' . $dateDebutN2->format('m') . '-' . $dateDebutN2->format('d');
        $dateFinN2   = date_create($dateFinN1);
        $dateFinN2   = date_modify($dateFinN2, '-1 Year');
        $dateFinN2   = $dateFinN2->format('Y') . '-' . $dateFinN2->format('m') . '-' . $dateFinN2->format('d');

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT MAX(Nom) AS Nom ,Mois AS Mois ,MAX(FamArticle) AS FamArticle, Ref AS Ref, MAX(Designation) AS Designation, Sref1 AS Sref1, Sref2 AS Sref2, Uv AS Uv, SUM(QteSignN2) As QteTotalN2, SUM(MontantSignN2) As CATotalN2, SUM(QteSignN1) As QteTotalN1, SUM(MontantSignN1) As CATotalN1, SUM(QteSignN) As QteTotalN,  SUM(MontantSignN) As CATotalN
        FROM(	SELECT MOUV.TIERS AS Tiers,CLI.NOM AS Nom, ART.FAM_0001 AS FamArticle, MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2, ART.VENUN AS Uv, MONTH(MOUV.FADT) AS Mois, LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN VRP.NOM
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                END AS Commercial,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (MOUV.FAQTE)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (-1 * MOUV.FAQTE)
				ELSE 0
                END AS QteSignN2,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN2,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.FAQTE)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.FAQTE)
				ELSE 0
                END AS QteSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.FAQTE)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.FAQTE)
				ELSE 0
                END AS QteSignN,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP WITH (INDEX = INDEX_C_VRP) ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan) AND MOUV.TIERS = '$tiers'

                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL' )
                AND ( (MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN') OR (MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1') OR (MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' ) ))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY Mois, Ref, Sref1, Sref2,Uv
        ORDER BY Mois, Ref";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // CA bandeau détail client famille produit
    public function getStatesBandeauClientFamilleProduit($tiers, $metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {
        $dateDebutN2 = date_create($dateDebutN1);
        $dateDebutN2 = date_modify($dateDebutN2, '-1 Year');
        $dateDebutN2 = $dateDebutN2->format('Y') . '-' . $dateDebutN2->format('m') . '-' . $dateDebutN2->format('d');
        $dateFinN2   = date_create($dateFinN1);
        $dateFinN2   = date_modify($dateFinN2, '-1 Year');
        $dateFinN2   = $dateFinN2->format('Y') . '-' . $dateFinN2->format('m') . '-' . $dateFinN2->format('d');

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT LTRIM(RTRIM(FamArticle)) AS FamArticle, SUM(MontantSignN2) As CATotalN2, SUM(MontantSignN1) As CATotalN1, SUM(MontantSignN) As CATotalN
        FROM(	SELECT ART.FAM_0001 AS FamArticle,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN2,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan) AND MOUV.TIERS = '$tiers'

                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL' )
                AND ( (MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN') OR (MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1') OR (MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' ) ))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY FamArticle
        ORDER BY FamArticle";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // CA global du commercial
    public function getStatesBandeauClientCaTotalCommercial($commercial, $metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1, $dossier): array
    {
        $dateDebutN2 = date_create($dateDebutN1);
        $dateDebutN2 = date_modify($dateDebutN2, '-1 Year');
        $dateDebutN2 = $dateDebutN2->format('Y') . '-' . $dateDebutN2->format('m') . '-' . $dateDebutN2->format('d');
        $dateFinN2   = date_create($dateFinN1);
        $dateFinN2   = date_modify($dateFinN2, '-1 Year');
        $dateFinN2   = $dateFinN2->format('Y') . '-' . $dateFinN2->format('m') . '-' . $dateFinN2->format('d');

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT SUM(MontantSignN2) As CATotalN2, SUM(MontantSignN1) As CATotalN1, SUM(MontantSignN) As CATotalN
        FROM(	SELECT ART.FAM_0001 AS FamArticle,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                WHEN ART.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN2,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1'THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD', 'C 2','CO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D', 'D 2','DO') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART WITH (INDEX = INDEX_A_MINI) ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI WITH (INDEX = INDEX_C_CLI) ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                WHERE MOUV.DOS = $dossier AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN($this->artBan) AND (CLI.REPR_0001 = $commercial OR CLI.REPR_0002 = $commercial )

                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL' )
                AND ( (MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' ) OR (MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' ) OR (MOUV.FADT >= '$dateDebutN2' AND MOUV.FADT <= '$dateFinN2' ) ) )reponse
                WHERE SecteurMouvement IN( $metiers )";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States par famille société Roby
    public function getStatesParFamilleRoby($dossier, $metier, $startN, $endN, $startN1, $endN1, $famille): array
    {

        if ($famille == "produits") {
            $type = "RTRIM(LTRIM(a.FAM_0001))";
        } elseif ($famille == "clients") {
            $type = "RTRIM(LTRIM(c.STAT_0001))";
        }

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = null;
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT tiers, nom, famille, SUM(montantSignN1) AS montantN1, SUM(montantSignN) AS montantN
        FROM(
        SELECT RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, $type AS famille,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN1,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND (m.FADT >= '$startN' AND m.FADT <= '$endN' or m.FADT >= '$startN1' AND m.FADT <= '$endN1' ) AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY tiers, nom, famille";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States par famille société Roby Totaux
    public function getStatesParFamilleRobyTotaux($dossier, $metier, $startN, $endN, $startN1, $endN1, $famille): array
    {

        if ($famille == "produits") {
            $type = "a.FAM_0001";
        } elseif ($famille == "clients") {
            $type = "c.STAT_0001";
        }

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == "EV") {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier = "AND a.FAM_0002 IN( 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT SUM(montantSignN1) AS montantN1, SUM(montantSignN) AS montantN
        FROM(
        SELECT m.TIERS AS tiers, c.NOM AS nom, $type AS famille,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN1,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND (m.FADT >= '$startN' AND m.FADT <= '$endN' or m.FADT >= '$startN1' AND m.FADT <= '$endN1' ) AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States société Roby Total
    public function getStatesRobyTotal($dossier, $metier, $startN, $endN)
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier = "AND a.FAM_0002 IN( 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT SUM(montantSignN) AS montantN
        FROM(
        SELECT m.TIERS AS tiers,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN' AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // States société Roby par clients
    public function getStatesRobyTotalParClient($dossier, $metier, $startN, $endN): array
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier = "AND a.FAM_0002 IN( 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT RTRIM(LTRIM(tiers)) AS tiers, RTRIM(LTRIM(nom)) AS nom, SUM(montantSignN) AS montantN
        FROM(
        SELECT m.TIERS AS tiers, c.NOM AS nom,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN' AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY tiers, nom";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States société Roby par produits
    public function getStatesRobyTotalParProduit($dossier, $metier, $startN, $endN): array
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier = "AND a.FAM_0002 IN( 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT RTRIM(LTRIM(ref)) AS ref, RTRIM(LTRIM(sref1)) AS sref1, RTRIM(LTRIM(sref2)) AS sref2,
        RTRIM(LTRIM(designation)) AS designation, RTRIM(LTRIM(uv)) AS uv, SUM(montantSignN) AS montantN
        FROM(
        SELECT m.REF AS ref, m.SREF1 AS sref1, m.SREF2 AS sref2, a.DES AS designation, a.VENUN as uv,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN' AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY ref, sref1, sref2, designation,uv";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States société Roby par clients/article 2 ans
    public function getStatesRobyTotalParClientArticle($dossier, $metier, $startN, $endN, $startN1, $endN1, $famille): array
    {
        if ($famille == "produits") {
            $type    = "RTRIM(LTRIM(ref)) AS ref, RTRIM(LTRIM(sref1)) AS sref1, RTRIM(LTRIM(sref2)) AS sref2, RTRIM(LTRIM(designation)) AS designation, RTRIM(LTRIM(uv)) AS uv";
            $groupBy = "ref, sref1, sref2, designation, uv";
        } elseif ($famille == "clients") {
            $type    = "RTRIM(LTRIM(tiers)) AS tiers, RTRIM(LTRIM(nom)) AS nom";
            $groupBy = "tiers, nom";
        } elseif ($famille == "mois") {
            $type    = "mois";
            $groupBy = "mois";
        }

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier = "AND a.FAM_0002 IN( 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT $type, SUM(montantSignN) AS montantN , SUM(montantSignN1) AS montantN1
        FROM(
        SELECT  MONTH(m.FADT) AS mois,m.TIERS AS tiers, c.NOM AS nom,
        m.REF AS ref, m.SREF1 AS sref1, m.SREF2 AS sref2, a.DES AS designation, a.VENUN as uv,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN1
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND (m.FADT >= '$startN' AND m.FADT <= '$endN' or m.FADT >= '$startN1' AND m.FADT <= '$endN1' ) AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY $groupBy ";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States par famille société Roby Totaux PAR FAMILLE
    public function getStatesParFamilleRobyTotauxParFamille($dossier, $metier, $startN, $endN, $startN1, $endN1, $famille): array
    {

        if ($famille == "produits") {
            $type = "RTRIM(LTRIM(a.FAM_0001))";
        } elseif ($famille == "clients") {
            $type = "RTRIM(LTRIM(c.STAT_0001))";
        }

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = null;
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT famille, SUM(montantSignN1) AS montantN1, SUM(montantSignN) AS montantN
        FROM(
        SELECT RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, $type AS famille,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$startN1' AND m.FADT <= '$endN1' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN1,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D2','DO') AND m.FADT >= '$startN' AND m.FADT <= '$endN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND (m.FADT >= '$startN' AND m.FADT <= '$endN' or m.FADT >= '$startN1' AND m.FADT <= '$endN1' ) AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY famille
        ORDER BY montantN DESC";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States par famille société Roby Totaux PAR FAMILLE produit
    public function getStatesParFamilleRobyTotauxParFamilleOneTrancheYear($dossier, $metier, $startN, $endN, $famille): array
    {

        if ($famille == "produits") {
            $type = "RTRIM(LTRIM(a.FAM_0001))";
        } elseif ($famille == "clients") {
            $type = "RTRIM(LTRIM(c.STAT_0001))";
        }

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier = "AND a.FAM_0002 IN( 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT famille, SUM(montantSignN) AS montantN
        FROM(
        SELECT RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, $type AS famille,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSignN
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN' AND m.PICOD = 4 AND m.TICOD = 'C' AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY famille
        ORDER BY montantN DESC";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States CA par année sur 6 années
    public function getStatesSevenYearsAgo($dossier, $metier, $type): array
    {

        $commercial = ',RTRIM(LTRIM(v.SELCOD)) AS commercial';

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == "EV") {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = ",a.FAM_0002 AS commercial";
        }

        if ($type == "annee") {
            $type = 'annee';
        } elseif ($type == "commercial") {
            $type = 'annee, commercial';
        }

        $d  = new DateTime('now');
        $n  = $d->format('Y');
        $n1 = $n - 1;
        $n2 = $n1 - 1;
        $n3 = $n2 - 1;
        $n4 = $n3 - 1;
        $n5 = $n4 - 1;

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT $type, SUM(montantSign) AS montant
        FROM(
        SELECT  YEAR(m.FADT) AS annee $commercial ,RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, RTRIM(LTRIM(m.REF)) AS ref, RTRIM(LTRIM(m.SREF1)) AS sref1,
		RTRIM(LTRIM(m.SREF2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(a.VENUN)) as uv,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSign
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        INNER JOIN VRP v WITH (INDEX = INDEX_C_VRP) ON v.DOS = m.DOS AND v.TIERS = c.REPR_0001
        WHERE m.DOS = $dossier AND YEAR(m.FADT) IN ($n, $n1, $n2, $n3, $n4, $n5) AND m.PICOD = 4 AND m.TICOD = 'C'
		AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY $type";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States CA par année sur 6 années Commerciaux
    public function getStatesSixYearsAgoCommerciaux($dossier, $metier): array
    {

        $commercial = ', RTRIM(LTRIM(v.SELCOD)) AS commercial';
        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = ",a.FAM_0002 AS commercial";
        }

        $d  = new DateTime('now');
        $n  = $d->format('Y');
        $n1 = $n - 1;
        $n2 = $n1 - 1;
        $n3 = $n2 - 1;
        $n4 = $n3 - 1;
        $n5 = $n4 - 1;

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT commercial, SUM(montantSign) AS montantN, SUM(montantSign1) AS montantN1, SUM(montantSign2) AS montantN2, SUM(montantSign3) AS montantN3
        , SUM(montantSign4) AS montantN4, SUM(montantSign5) AS montantN5
        FROM(
        SELECT  YEAR(m.FADT) AS annee $commercial ,RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, RTRIM(LTRIM(m.REF)) AS ref, RTRIM(LTRIM(m.SREF1)) AS sref1,
		RTRIM(LTRIM(m.SREF2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(a.VENUN)) as uv,
        CASE
             WHEN m.OP IN('C','CD', 'C 2','CO') AND YEAR(m.FADT) = '$n' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
             WHEN m.OP IN('DD','D', 'D 2','DO') AND YEAR(m.FADT) = '$n' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
         END AS montantSign,
         CASE
             WHEN m.OP IN('C','CD', 'C 2','CO') AND YEAR(m.FADT) = '$n1' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
             WHEN m.OP IN('DD','D', 'D 2','DO') AND YEAR(m.FADT) = '$n1' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
         END AS montantSign1,
         CASE
             WHEN m.OP IN('C','CD', 'C 2','CO') AND YEAR(m.FADT) = '$n2' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
             WHEN m.OP IN('DD','D', 'D 2','DO') AND YEAR(m.FADT) = '$n2' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
         END AS montantSign2,
         CASE
             WHEN m.OP IN('C','CD', 'C 2','CO') AND YEAR(m.FADT) = '$n3' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
             WHEN m.OP IN('DD','D', 'D 2','DO') AND YEAR(m.FADT) = '$n3' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
         END AS montantSign3,
         CASE
             WHEN m.OP IN('C','CD', 'C 2','CO') AND YEAR(m.FADT) = '$n4' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
             WHEN m.OP IN('DD','D', 'D 2','DO') AND YEAR(m.FADT) = '$n4' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
         END AS montantSign4,
        CASE
            WHEN m.OP IN('C','CD', 'C 2','CO') AND YEAR(m.FADT) = '$n5' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
            WHEN m.OP IN('DD','D', 'D 2','DO') AND YEAR(m.FADT) = '$n5' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
        END AS montantSign5
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        INNER JOIN VRP v WITH (INDEX = INDEX_C_VRP) ON v.DOS = m.DOS AND v.TIERS = c.REPR_0001
        WHERE m.DOS = $dossier AND YEAR(m.FADT) IN ( $n ,$n1, $n2, $n3, $n4, $n5) AND m.PICOD = 4 AND m.TICOD = 'C'
		AND a.REF NOT IN($this->artBan)
        $metier
        )reponse
        GROUP BY commercial
        ORDER BY montantN1 DESC";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States CA par année sur 6 années Commerciaux
    public function getStatesSixYearsAgoCommerciauxPeriode($dossier, $metier, $ddN, $dfN): array
    {

        $commercial = ", RTRIM(LTRIM(v.SELCOD)) AS commercial";
        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = ",a.FAM_0002 AS commercial";
        }

        $datesDiff = $this->statesParFamilleController->generateDateDiffs($ddN, $dfN, 5);

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT commercial, SUM(montantSign) AS montantN, SUM(montantSign1) AS montantN1, SUM(montantSign2) AS montantN2, SUM(montantSign3) AS montantN3
            , SUM(montantSign4) AS montantN4, SUM(montantSign5) AS montantN5
            FROM(
            SELECT  YEAR(m.FADT) AS annee $commercial ,RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, RTRIM(LTRIM(m.REF)) AS ref, RTRIM(LTRIM(m.SREF1)) AS sref1,
            RTRIM(LTRIM(m.SREF2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(a.VENUN)) as uv,
            CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$ddN' AND m.FADT <= '$dfN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$ddN' AND m.FADT <= '$dfN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[0]['dd']}' AND m.FADT <= '{$datesDiff[0]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[0]['dd']}' AND m.FADT <= '{$datesDiff[0]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign1,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[1]['dd']}' AND m.FADT <= '{$datesDiff[1]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[1]['dd']}' AND m.FADT <= '{$datesDiff[1]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign2,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[2]['dd']}' AND m.FADT <= '{$datesDiff[2]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[2]['dd']}' AND m.FADT <= '{$datesDiff[2]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign3,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '{$datesDiff[3]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '{$datesDiff[3]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign4,
            CASE
                WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[4]['dd']}' AND m.FADT <= '{$datesDiff[4]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[4]['dd']}' AND m.FADT <= '{$datesDiff[4]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
            END AS montantSign5
            FROM MOUV m
            INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
            INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
            INNER JOIN VRP v WITH (INDEX = INDEX_C_VRP) ON v.DOS = m.DOS AND v.TIERS = c.REPR_0001
            WHERE m.DOS = $dossier AND m.FADT >= '{$datesDiff[4]['dd']}' AND m.FADT <= '$dfN' AND m.PICOD = 4 AND m.TICOD = 'C'
            AND a.REF NOT IN($this->artBan)
            $metier
            )reponse
            GROUP BY commercial
            ORDER BY montantN1 DESC";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States par client, famille client, famille produit, produit
    public function StatesCommercial($dossier, $metier, $ddN, $dfN, $commercial, $type): array
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $commercial = trim($commercial);
            if ($commercial != null) {
                $metier = " AND a.FAM_0002 IN('$commercial')";
            } else {
                $metier = " AND a.FAM_0002 IN( 'ME', 'MO')";
            }
        }

        if ($commercial != null && $commercial != 'ME' && $commercial != 'MO') {
            $commercial = "AND v.SELCOD = '$commercial'";
        } else {
            $commercial = "";
        }

        if ($type == 'topClient') {
            $requete = 'tiers, nom';
        } elseif ($type == 'topFournisseur') {
            $requete = 'tiersFou, nomFou';
        } elseif ($type == 'topFamilleProduit') {
            $requete = 'familleProduit';
        } elseif ($type == 'topFamilleClient') {
            $requete = 'familleClient';
        } elseif ($type == 'topProduit') {
            $requete = 'ref, sref1, sref2, designation,familleProduit';
        } elseif ($type == 'topProduitResume') {
            $requete = 'ref, designation,familleProduit';
        }

        $datesDiff = $this->statesParFamilleController->generateDateDiffs($ddN, $dfN, 5);

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT $requete, SUM(montantSign) AS montantN, SUM(montantSign1) AS montantN1,SUM(montantSign2) AS montantN2, SUM(montantSign3) AS montantN3
         , SUM(montantSign4) AS montantN4
         FROM(
         SELECT  YEAR(m.FADT) AS annee, RTRIM(LTRIM(v.SELCOD)) AS commercial, f.TIERS AS tiersFou, f.NOM AS nomFou ,RTRIM(LTRIM(m.TIERS)) AS tiers,
         RTRIM(LTRIM(c.NOM)) AS nom, RTRIM(LTRIM(c.STAT_0001)) AS familleClient, RTRIM(LTRIM(a.FAM_0001)) AS familleProduit, RTRIM(LTRIM(m.REF)) AS ref,
         RTRIM(LTRIM(m.SREF1)) AS sref1,RTRIM(LTRIM(m.SREF2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(a.VENUN)) as uv,
         CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$ddN' AND m.FADT <= '$dfN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$ddN' AND m.FADT <= '$dfN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[0]['dd']}' AND m.FADT <= '{$datesDiff[0]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[0]['dd']}' AND m.FADT <= '{$datesDiff[0]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign1,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[1]['dd']}' AND m.FADT <= '{$datesDiff[1]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[1]['dd']}' AND m.FADT <= '{$datesDiff[1]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign2,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[2]['dd']}' AND m.FADT <= '{$datesDiff[2]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[2]['dd']}' AND m.FADT <= '{$datesDiff[2]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign3,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '{$datesDiff[3]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '{$datesDiff[3]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign4
         FROM MOUV m
         INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
         INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
         INNER JOIN FOU f WITH (INDEX = INDEX_C_FOU) ON f.DOS = m.DOS AND f.TIERS = a.TIERS
         INNER JOIN VRP v WITH (INDEX = INDEX_C_VRP) ON v.DOS = m.DOS AND v.TIERS = c.REPR_0001
         WHERE m.DOS = $dossier AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '$dfN' AND m.PICOD = 4 AND m.TICOD = 'C'
         AND a.REF NOT IN($this->artBan)
         $metier
         $commercial
         )reponse
         GROUP BY $requete
         ORDER BY montantN DESC";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Totaux des states commerciaux
    public function totauxStatesCommerciaux($dossier, $metier, $ddN, $dfN, $commercial): array
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $commercial = trim($commercial);
            if ($commercial != null) {
                $metier = " AND a.FAM_0002 IN('$commercial')";
            } else {
                $metier = " AND a.FAM_0002 IN( 'ME', 'MO')";
            }
        }

        if ($commercial != null && $commercial != 'ME' && $commercial != 'MO') {
            $commercial = "AND v.SELCOD = '$commercial'";
        } else {
            $commercial = "";
        }

        $datesDiff = $this->statesParFamilleController->generateDateDiffs($ddN, $dfN, 5);

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT SUM(montantSign) AS montantN, SUM(montantSign1) AS montantN1,SUM(montantSign2) AS montantN2, SUM(montantSign3) AS montantN3
         , SUM(montantSign4) AS montantN4
         FROM(
         SELECT  YEAR(m.FADT) AS annee, RTRIM(LTRIM(v.SELCOD)) AS commercial ,RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom, RTRIM(LTRIM(c.STAT_0001)) AS familleClient, RTRIM(LTRIM(a.FAM_0001)) AS familleProduit, RTRIM(LTRIM(m.REF)) AS ref, RTRIM(LTRIM(m.SREF1)) AS sref1,
         RTRIM(LTRIM(m.SREF2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(a.VENUN)) as uv,
         CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$ddN' AND m.FADT <= '$dfN' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$ddN' AND m.FADT <= '$dfN' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[0]['dd']}' AND m.FADT <= '{$datesDiff[0]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[0]['dd']}' AND m.FADT <= '{$datesDiff[0]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign1,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[1]['dd']}' AND m.FADT <= '{$datesDiff[1]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[1]['dd']}' AND m.FADT <= '{$datesDiff[1]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign2,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[2]['dd']}' AND m.FADT <= '{$datesDiff[2]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[2]['dd']}' AND m.FADT <= '{$datesDiff[2]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign3,
             CASE
                 WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '{$datesDiff[3]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                 WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[3]['dd']}' AND m.FADT <= '{$datesDiff[3]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
             END AS montantSign4,
            CASE
                WHEN m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '{$datesDiff[4]['dd']}' AND m.FADT <= '{$datesDiff[4]['df']}' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '{$datesDiff[4]['dd']}' AND m.FADT <= '{$datesDiff[4]['df']}' THEN (-1 * m.MONT)+(m.REMPIEMT_0004)
            END AS montantSign5
         FROM MOUV m
         INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
         INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
         INNER JOIN VRP v WITH (INDEX = INDEX_C_VRP) ON v.DOS = m.DOS AND v.TIERS = c.REPR_0001
         WHERE m.DOS = $dossier AND m.FADT >= '{$datesDiff[4]['dd']}' AND m.FADT <= '$dfN' AND m.PICOD = 4 AND m.TICOD = 'C'
         AND a.REF NOT IN($this->artBan)
         $metier
         $commercial
         )reponse";

        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States par famille et type Article
    public function getStatesParFamilleTypeArticle($dossier, $startN, $endN, $metier): array
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = null;
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT famille, typeArt, SUM(montant) AS montant
        FROM(
        SELECT RTRIM(LTRIM(a.FAM_0001)) AS famille, a.TYPEARTCOD AS typeArt,m.OP AS op,
        CASE
        WHEN m.OP IN ('C','CD', 'C 2','CO') THEN m.MONT - m.REMPIEMT_0004
        WHEN m.OP IN ('D','DD', 'D 2','DO') THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montant
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN'
        AND m.TICOD = 'C' AND m.PICOD = 4 --AND a.TYPEARTCOD NOT IN ('DIVERS')
        $metier
        AND a.REF NOT IN($this->artBan))reponse
        GROUP BY famille, typeArt
        ORDER BY famille";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // States montant total par famille produit
    public function getStatesTotalParFamille($dossier, $startN, $endN, $metier, $famille)
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = null;
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = " SELECT SUM(montant) AS montant
        FROM(
        SELECT RTRIM(LTRIM(a.FAM_0001)) AS famille,m.OP AS op,
        CASE
        WHEN m.OP IN ('C','CD', 'C 2','CO') THEN m.MONT - m.REMPIEMT_0004
        WHEN m.OP IN ('D','DD', 'D 2','DO') THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montant
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN'
        AND m.TICOD = 'C' AND m.PICOD = 4 AND a.FAM_0001 = '$famille'
        $metier
        AND a.REF NOT IN($this->artBan))reponse
        GROUP BY famille";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // States montant total par Type produit
    public function getStatesTotalParType($dossier, $startN, $endN, $metier, $type)
    {

        if ($dossier == 3) {
            $metier = "AND a.FAM_0002 IN( 'RB', 'D', 'RG', 'RL', 'S', 'BL' ) AND c.STAT_0002 IN('RB')";
        } elseif ($dossier == 1 && $metier == 'EV') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('EV')";
        } elseif ($dossier == 1 && $metier == 'HP') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 NOT IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'MA') {
            $metier = "AND a.FAM_0002 IN( 'EV', 'HP') AND c.STAT_0002 IN('HP') AND c.STAT_0001 IN ('MARAICHE','ASSO','AGRICULT')";
        } elseif ($dossier == 1 && $metier == 'ME') {
            $metier     = "AND a.FAM_0002 IN( 'ME', 'MO')";
            $commercial = null;
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT SUM(montant) AS montant
        FROM(
        SELECT RTRIM(LTRIM(a.FAM_0001)) AS famille,m.OP AS op,
        CASE
        WHEN m.OP IN ('C','CD', 'C 2','CO') THEN m.MONT - m.REMPIEMT_0004
        WHEN m.OP IN ('D','DD', 'D 2','DO') THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montant
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dossier AND m.FADT >= '$startN' AND m.FADT <= '$endN'
        AND m.TICOD = 'C' AND m.PICOD = 4 AND a.TYPEARTCOD = '$type'
        $metier
        AND a.REF NOT IN($this->artBan))reponse";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // States Excel par commercial
    public function getStatesExcelDetaille($metier, $year, $dossier): array
    {
        $year1 = $year - 1;
        $year2 = $year1 - 1;
        $year3 = $year2 - 1;

        $conn = $this->getEntityManager()->getConnection();
        $sql  = "SELECT LTRIM(RTRIM(Commercial)) AS Commercial, LTRIM(RTRIM(Famille_Client)) AS Famille_Client, LTRIM(RTRIM(Client)) AS Client,
		LTRIM(RTRIM(nom)) as Nom,LTRIM(RTRIM(Pays)) as Pays,
        LTRIM(RTRIM(Famille_Article)) AS Fam_Art, LTRIM(RTRIM(Ref)) AS Ref,LTRIM(RTRIM(Designation)) AS Designation,
        LTRIM(RTRIM(Sref1)) AS Sref1,LTRIM(RTRIM(Sref2)) AS Sref2, LTRIM(RTRIM(UV)) AS Uv,Mois, Op,
		LTRIM(RTRIM(SUM(QteSignCmd))) AS QteSignCmd, LTRIM(RTRIM(SUM(MontantSignCmd))) AS MontantSignCmd,
		LTRIM(RTRIM(SUM(QteSignBl))) AS QteSignBl, LTRIM(RTRIM(SUM(MontantSignBl))) AS MontantSignBl,
		LTRIM(RTRIM(SUM(QteSign))) AS QteSign, LTRIM(RTRIM(SUM(MontantSign))) AS MontantSign,
		LTRIM(RTRIM(SUM(QteSignN))) AS QteSignN, LTRIM(RTRIM(SUM(MontantSignN))) AS MontantSignN,
        LTRIM(RTRIM(SUM(QteSignN1))) AS QteSignN1, LTRIM(RTRIM(SUM(MontantSignN1))) AS MontantSignN1,
        LTRIM(RTRIM(SUM(QteSignN2))) AS QteSignN2, LTRIM(RTRIM(SUM(MontantSignN2))) AS MontantSignN2
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT c.STAT_0001 AS Famille_Client, m.TIERS as Client, c.NOM AS nom, c.PAY AS Pays, m.DEV AS Devise,
		m.OP AS Op,m.REF AS Ref, m.DES AS Designation, m.SREF1 AS Sref1, m.SREF2 AS Sref2,m.VENUN AS UV, m.FAQTE AS Qte,m.MONT AS Montant,
        m.REMPIEMT_0004 AS Remise,m.FADT AS DateFacture,m.FANO AS Facture, a.FAM_0001 AS Famille_Article,
                        CASE
                        WHEN a.FAM_0002 IN ('EV', 'HP') AND c.STAT_0002 = 'EV' THEN 'EV'
                        WHEN a.FAM_0002 IN ('EV', 'HP') AND c.STAT_0002 = 'HP' THEN 'HP'
                        WHEN a.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                        WHEN a.FAM_0002 IN ('RB', 'D', 'RG', 'RL', 'S', 'BL') THEN 'RB'
                        ELSE 'WTF !'
                        END AS SecteurMouvement,
                        CASE
                        WHEN a.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN r.NOM
                        WHEN a.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                        END AS Commercial,
                        CASE
                        WHEN a.FAM_0002 IN ('EV', 'HP', 'RB', 'D', 'RG', 'RL', 'S', 'BL') THEN r.TIERS
                        WHEN a.FAM_0002 IN ('ME', 'MO') THEN 2
                        END AS CommercialId,
        CASE -- Mois
			WHEN m.PICOD = 2 AND m.CDCE4 = 1 AND m.CDDT >= '$year1-09-01' AND m.CDDT <= '$year-12-31' THEN MONTH(m.CDDT)
			WHEN m.PICOD = 3 AND m.BLCE4 = 1 AND m.BLDT >= '$year1-09-01' AND m.BLDT <= '$year-12-31' THEN MONTH(m.BLDT)
			ELSE MONTH(m.FADT)
		END AS Mois,
		CASE -- Signature du montant Commande
                WHEN m.PICOD = 2 AND m.CDCE4 = 1 AND m.OP IN('C','CD', 'C 2','CO') AND m.CDDT >= '$year1-09-01' AND m.CDDT <= '$year-12-31' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.PICOD = 2 AND m.CDCE4 = 1 AND m.OP IN('DD','D', 'D 2','DO') AND m.CDDT >= '$year1-09-01' AND m.CDDT <= '$year-12-31' THEN (-1 * m.MONT)+(m.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignCmd,
        CASE
            WHEN m.PICOD = 2 AND m.CDCE4 = 1 AND m.OP IN('C','CD', 'C 2','CO') AND m.CDDT >= '$year1-09-01' AND m.CDDT <= '$year-12-31' THEN m.CDQTE
            WHEN m.PICOD = 2 AND m.CDCE4 = 1 AND m.OP IN('D','DD', 'D 2','DO') AND m.CDDT >= '$year1-09-01' AND m.CDDT <= '$year-12-31' THEN -1*m.CDQTE
            ELSE 0
        END AS QteSignCmd,
		CASE -- Signature du montant BL
                WHEN m.PICOD = 3 AND m.BLCE4 = 1 AND m.OP IN('C','CD', 'C 2','CO') AND m.BLDT >= '$year1-09-01' AND m.BLDT <= '$year-12-31' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.PICOD = 3 AND m.BLCE4 = 1 AND m.OP IN('DD','D', 'D 2','DO') AND m.BLDT >= '$year1-09-01' AND m.BLDT <= '$year-12-31' THEN (-1 * m.MONT)+(m.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignBl,
        CASE
            WHEN m.PICOD = 3 AND m.BLCE4 = 1 AND m.OP IN('C','CD', 'C 2','CO') AND m.BLDT >= '$year1-09-01' AND m.BLDT <= '$year-12-31' THEN m.BLQTE
            WHEN m.PICOD = 3 AND m.BLCE4 = 1 AND m.OP IN('D','DD', 'D 2','DO') AND m.BLDT >= '$year1-09-01' AND m.BLDT <= '$year-12-31' THEN -1*m.BLQTE
            ELSE 0
        END AS QteSignBl,
		CASE -- Signature du montant Année de référence
                WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year-01-01' AND m.FADT <= '$year-12-31' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.PICOD = 4 AND m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$year-01-01' AND m.FADT <= '$year-12-31' THEN (-1 * m.MONT)+(m.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSign,
        CASE
            WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year-01-01' AND m.FADT <= '$year-12-31' THEN m.FAQTE
            WHEN m.PICOD = 4 AND m.OP IN('D','DD', 'D 2','DO') AND m.FADT >= '$year-01-01' AND m.FADT <= '$year-12-31' THEN -1*m.FAQTE
            ELSE 0
        END AS QteSign,
		CASE -- Signature du montant
                WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year1-01-01' AND m.FADT <= '$year1-12-31' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.PICOD = 4 AND m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$year1-01-01' AND m.FADT <= '$year1-12-31' THEN (-1 * m.MONT)+(m.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN,
        CASE
            WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year1-01-01' AND m.FADT <= '$year1-12-31' THEN m.FAQTE
            WHEN m.PICOD = 4 AND m.OP IN('D','DD', 'D 2','DO') AND m.FADT >= '$year1-01-01' AND m.FADT <= '$year1-12-31' THEN -1*m.FAQTE
            ELSE 0
        END AS QteSignN,
        CASE -- Signature du montant
                WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year2-01-01' AND m.FADT <= '$year2-12-31' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.PICOD = 4 AND m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$year2-01-01' AND m.FADT <= '$year2-12-31' THEN (-1 * m.MONT)+(m.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN1,
        CASE
            WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year2-01-01' AND m.FADT <= '$year2-12-31' THEN m.FAQTE
            WHEN m.PICOD = 4 AND m.OP IN('D','DD', 'D 2','DO') AND m.FADT >= '$year2-01-01' AND m.FADT <= '$year2-12-31' THEN -1*m.FAQTE
            ELSE 0
        END AS QteSignN1,
        CASE -- Signature du montant
                WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year3-01-01' AND m.FADT <= '$year3-12-31' THEN (m.MONT)+(-1 * m.REMPIEMT_0004)
                WHEN m.PICOD = 4 AND m.OP IN('DD','D', 'D 2','DO') AND m.FADT >= '$year3-01-01' AND m.FADT <= '$year3-12-31' THEN (-1 * m.MONT)+(m.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MontantSignN2,
        CASE
            WHEN m.PICOD = 4 AND m.OP IN('C','CD', 'C 2','CO') AND m.FADT >= '$year3-01-01' AND m.FADT <= '$year3-12-31' THEN m.FAQTE
            WHEN m.PICOD = 4 AND m.OP IN('D','DD', 'D 2','DO') AND m.FADT >= '$year3-01-01' AND m.FADT <= '$year3-12-31' THEN -1*m.FAQTE
            ELSE 0
        END AS QteSignN2
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON m.REF = a.REF AND a.DOS = m.DOS
        INNER JOIN CLI c WITH (INDEX = INDEX_C_CLI) ON m.TIERS = c.TIERS AND c.DOS = m.DOS
        LEFT JOIN VRP r WITH (INDEX = INDEX_C_VRP) ON c.REPR_0001 = r.TIERS AND m.DOS = r.DOS
        WHERE m.DOS = $dossier AND m.TICOD = 'C' AND --(m.PICOD = 4 OR (m.PICOD IN(2,3) AND )) AND
		a.REF NOT IN($this->artBan)
        AND (
		(m.FADT >= '$year3-01-01' AND m.FADT <= '$year-12-31' AND m.PICOD = 4) OR
		(m.CDDT >= '$year1-09-01' AND m.CDDT <= '$year-12-31' AND m.CDCE4 = 1 ) OR
		(m.BLDT >= '$year1-09-01' AND m.BLDT <= '$year-12-31' AND m.BLCE4 = 1 )
		)
        AND c.STAT_0002 IN('EV','HP','RB') AND a.FAM_0002 IN('EV','HP','ME','MO','RB', 'D', 'RG', 'RL', 'S', 'BL')
        AND m.OP IN('C','CD','DD','D', 'C 2','CO', 'D 2','DO')) Reponse
        WHERE SecteurMouvement IN( '$metier' )
        GROUP BY Commercial, Famille_Client, Client, nom,Pays, Famille_Article, Ref,Designation,Sref1,Sref2,UV, Mois, Op
        ORDER BY Client";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
