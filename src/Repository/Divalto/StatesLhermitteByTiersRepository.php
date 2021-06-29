<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use DoctrineExtensions\Query\Mysql\Year;

class StatesLhermitteByTiersRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   // Ancienne requête
    public function getStatesLhermitteGlobalesByMonth($dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT YEAR(MOUV.FADT) AS Annee,MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
        CASE
        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' THEN 'HP'
        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
        ELSE 'WTF !'
        END AS SecteurMouvement,
        CASE
        WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.NOM
        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
        END AS Commercial,
        CASE
        WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.TIERS
        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
        END AS CommercialId,
        CLI.STAT_0002 AS SecteurClient, MOUV.TIERS AS Tiers,CLI.NOM AS Nom,
        ART.FAM_0002 AS SecteurArticle, MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2,MOUV.VENUN AS UV, LTRIM(RTRIM(MOUV.OP)) AS OP,
        CASE
        WHEN MOUV.OP IN('C','CD') THEN MOUV.FAQTE
        WHEN MOUV.OP IN('DD','D') THEN -1 * MOUV.FAQTE
        END AS QteSign,
        CASE
        WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
        WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
        END AS MontantSign
        FROM MOUV
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
        LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP') AND ((MOUV.FADT >= ? AND MOUV.FADT <= ?) OR (MOUV.FADT >= ? AND MOUV.FADT <= ? ))";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1]);
        return $stmt->fetchAll();
    }

    // bandeau avec les states par commerciaux
    public function getStatesLhermitteTotauxParCommerciaux($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1):array
    {
        
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Periode AS Periode,SecteurMouvement AS SecteurMouvement, Commercial,COUNT(DISTINCT Bl) AS NbBl,COUNT(DISTINCT Facture) AS NbFacture,COUNT(DISTINCT Tiers) AS NbTiers,
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
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.NOM
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                END AS Commercial,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.TIERS
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
                END AS CommercialId,
                MOUV.TIERS AS Tiers,
                LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDepot,
                CASE
                WHEN MOUV.OP IN('CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDirect,
                CASE
                WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSign
                FROM MOUV
                LEFT JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')
                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN('EV','HP','ME','MO','RB')
                AND ((MOUV.FADT >= ? AND MOUV.FADT <= ? ) OR (MOUV.FADT >= ? AND MOUV.FADT <= ? )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY Commercial,SecteurMouvement, Periode
        ORDER BY Commercial,SecteurMouvement, Periode";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1]);
        return $stmt->fetchAll();
    }

    // Bandeau avec CA du secteur d'extraction
    public function getStatesLhermitteTotauxParSecteur($metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1):array
    {
        
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Periode AS Periode,SecteurMouvement AS SecteurMouvement, COUNT(DISTINCT Bl) AS NbBl,COUNT(DISTINCT Facture) AS NbFacture,COUNT(DISTINCT Tiers) AS NbTiers,
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
                ELSE 'WTF !'
                END AS SecteurMouvement,
                MOUV.TIERS AS Tiers,
                LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDepot,
                CASE
                WHEN MOUV.OP IN('CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignDirect,
                CASE
                WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSign
                FROM MOUV
                LEFT JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')
                AND CLI.STAT_0002 IN( 'EV','HP','RB' ) AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB' )
                AND ((MOUV.FADT >= ? AND MOUV.FADT <= ? ) OR (MOUV.FADT >= ?  AND MOUV.FADT <= ? )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY SecteurMouvement, Periode
        ORDER BY SecteurMouvement, Periode";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1]);
        return $stmt->fetchAll();
    }

    public function getStatesLhermitteDetailClient($metiers,$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1):array
    {
        
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SecteurMouvement AS SecteurMouvement, Commercial AS Commercial,commercialId AS commercialId, Tiers AS Tiers, Nom AS Nom,  SUM(MontantSignN1) As CATotalN1,  SUM(MontantSignN) As CATotalN
        FROM(	SELECT MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.NOM
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                END AS Commercial,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.TIERS
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 2
                END AS commercialId,
                LTRIM(RTRIM(MOUV.TIERS)) AS Tiers,CLI.NOM AS Nom,LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')
                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB' )
                AND ((MOUV.FADT >= ? AND MOUV.FADT <= ?) OR (MOUV.FADT >= ? AND MOUV.FADT <= ? )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY Commercial,commercialId,SecteurMouvement, Tiers, Nom
        ORDER BY Commercial";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1]);
        return $stmt->fetchAll();
    }

    // CA Par métiers 
    public function getStatesLhermitteMetier($dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT	SecteurMouvement AS SecteurMouvement,Color AS Color,Icon AS Icon,  SUM(MontantSignN1) As CATotalN1,  SUM(MontantSignN) As CATotalN
        FROM(	SELECT MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'success'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'danger'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'orange'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'warning'
                ELSE 'WTF !'
                END AS Color,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'fas fa-tree'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'fas fa-seedling'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'fas fa-carrot'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'fas fa-rainbow'
                ELSE 'WTF !'
                END AS Icon,
                LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
                ELSE 0
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')
                AND CLI.STAT_0002 IN('EV','HP') AND ART.FAM_0002 IN('EV','HP','ME','MO','RB')
                AND ((MOUV.FADT >= ? AND MOUV.FADT <= ?) OR (MOUV.FADT >= ? AND MOUV.FADT <= ? )))reponse
        GROUP BY SecteurMouvement, Color,Icon
        ORDER BY SecteurMouvement";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1]);
        return $stmt->fetchAll();
    }


    public function getStatesLhermitteByArticles($tiers,$metiers, $dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1):array
    {
        
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MAX(Nom) AS Nom,Ref AS Ref, MAX(Designation) AS Designation, Sref1 AS Sref1, Sref2 AS Sref2, Uv AS Uv, SUM(QteSignN1) As QteTotalN1, SUM(MontantSignN1) As CATotalN1, SUM(QteSignN) As QteTotalN,  SUM(MontantSignN) As CATotalN
        FROM(	SELECT MOUV.TIERS AS Tiers,CLI.NOM AS Nom,MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2, ART.VENUN AS Uv, MONTH(MOUV.FADT) AS Mois, LTRIM(RTRIM(MOUV.OP)) AS OP,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 NOT IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'HP'
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' AND CLI.STAT_0001 IN ('ASSO', 'MARAICHE', 'AGRICULT') THEN 'MA'
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
                ELSE 'WTF !'
                END AS SecteurMouvement,
                CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.NOM
                WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
                END AS Commercial,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.FAQTE)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.FAQTE)
				ELSE 0
                END AS QteSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN1' AND MOUV.FADT <= '$dateFinN1' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN1,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.FAQTE)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.FAQTE)
				ELSE 0
                END AS QteSignN,
                CASE
                WHEN MOUV.OP IN('C','CD') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN' THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
				ELSE 0
                END AS MontantSignN
                FROM MOUV
                LEFT JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                LEFT JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
                WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP') AND MOUV.TIERS = '$tiers'
                AND CLI.STAT_0002 IN('EV','HP','RB') AND ART.FAM_0002 IN( 'EV','HP','ME','MO','RB' )
                AND ((MOUV.FADT >= ? AND MOUV.FADT <= ?) OR (MOUV.FADT >= ? AND MOUV.FADT <= ? )))reponse
                WHERE SecteurMouvement IN( $metiers )
        GROUP BY Ref, Sref1, Sref2,Uv
        ORDER BY Ref";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateDebutN, $dateFinN, $dateDebutN1, $dateFinN1]);
        return $stmt->fetchAll();
    }
}
