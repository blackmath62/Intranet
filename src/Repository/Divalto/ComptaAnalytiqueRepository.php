<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @IsGranted("ROLE_COMPTA")
 */

class ComptaAnalytiqueRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   

    public function getSaleByMonth($annee,$mois):array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT MAX(VentAss) AS VentAssMax, Dos, Ref, Sref1, Sref2, Designation, Uv, CoutRevient, CoutMoyenPondere, Article, Client, CompteAchat, RegimeTva, SUM(QteSign) AS QteSigne
        FROM
        (
        SELECT RTRIM(LTRIM(MVTL.VTLNA)) AS VentAss, RTRIM(LTRIM(MOUV.DOS)) AS Dos, MOUV.FADT AS DateFacture, RTRIM(LTRIM(MOUV.FANO)) AS Numero,
        RTRIM(LTRIM(MOUV.REF)) AS Ref, RTRIM(LTRIM(MOUV.SREF1)) AS Sref1, RTRIM(LTRIM(MOUV.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation, RTRIM(LTRIM(ART.VENUN)) AS Uv, RTRIM(LTRIM(MOUV.OP)) AS Op, RTRIM(LTRIM(SART.CR)) AS CoutRevient, RTRIM(LTRIM(SART.CMP)) AS CoutMoyenPondere,
        RTRIM(LTRIM(ART.FAM_0002)) AS Article, RTRIM(LTRIM(CLI.STAT_0002)) AS Client, RTRIM(LTRIM(ART.CPTA)) AS CompteAchat, RTRIM(LTRIM(ART.TVAART)) AS RegimeTva,
        CASE
            WHEN MOUV.OP IN('C','CD') THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD') THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSign
        FROM MOUV 
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        LEFT JOIN SART ON MOUV.REF = SART.REF AND MOUV.DOS = SART.DOS AND MOUV.SREF1 = SART.SREF1 AND MOUV.SREF2 = SART.SREF2
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
        LEFT JOIN MVTL ON MOUV.DOS = MVTL.DOS AND MOUV.TIERS = MVTL.TIERS AND MOUV.FANO = MVTL.PINO AND MOUV.REF = MVTL.REF AND MOUV.SREF1 = MVTL.SREF1 AND MOUV.SREF2 = MVTL.SREF2
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.OP IN ('C','D')
        AND ART.FAM_0002 <> CLI.STAT_0002
        AND ART.FAM_0002 IN ('EV', 'HP')AND YEAR(MOUV.FADT) IN (?) AND MONTH(MOUV.FADT) IN (?)
        )reponse
        GROUP BY Dos, Ref, Sref1, Sref2, Designation, Uv, CoutRevient, CoutMoyenPondere, Article, Client, CompteAchat, RegimeTva
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois]);
        return $stmt->fetchAll();
    }

    public function getAveragePurchasePrice($ref, $ventilation)
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT 
        CASE
        WHEN MOUV.MONT <> 0 AND MOUV.FAQTE <> 0 THEN (MOUV.MONT / MOUV.FAQTE)
        ELSE 0
        END AS Pu
        FROM MOUV
        LEFT JOIN MVTL ON MOUV.DOS = MVTL.DOS AND MOUV.TIERS = MVTL.TIERS AND MOUV.FANO = MVTL.PINO
        WHERE MOUV.DOS = 1 AND MOUV.PICOD = 4 AND MOUV.TICOD = 'F' AND MOUV.REF = ? AND MVTL.VTLNO = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ref, $ventilation]);
        return $stmt->fetch();
    }

    public function getSaleList($annee, $mois)
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT MOUV.FANO AS Facture, RTRIM(LTRIM(MOUV.REF)) AS Ref,  RTRIM(LTRIM(MOUV.SREF1)) AS Sref1,  RTRIM(LTRIM(MOUV.SREF2)) AS Sref2,
        RTRIM(LTRIM(ART.DES)) AS Designation,  RTRIM(LTRIM(MOUV.VENUN)) AS Uv, RTRIM(LTRIM(MOUV.OP)) AS Op,  RTRIM(LTRIM(ART.FAM_0002)) AS Article,
        RTRIM(LTRIM(CLI.STAT_0002)) AS Client,  ART.CPTA AS CompteAchat,  RTRIM(LTRIM(MOUV.FAQTE)) AS QteSign, RTRIM(LTRIM(SART.CR)) AS CoutRevient, RTRIM(LTRIM(SART.CMP)) AS CoutMoyenPondere
        FROM MOUV
        INNER JOIN ART ON ART.REF = MOUV.REF AND ART.DOS = MOUV.DOS
        INNER JOIN CLI ON CLI.TIERS = MOUV.TIERS AND CLI.DOS = MOUV.DOS
        LEFT JOIN SART ON MOUV.REF = SART.REF AND MOUV.DOS = SART.DOS AND MOUV.SREF1 = SART.SREF1 AND MOUV.SREF2 = SART.SREF2
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4
        AND ART.FAM_0002 IN ('EV', 'HP')
        AND CLI.STAT_0002 <> ART.FAM_0002
        AND MOUV.OP IN ('C','D')
        AND YEAR(MOUV.FADT) IN (?) AND MONTH(MOUV.FADT) IN (?)
        ORDER BY MOUV.FANO
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois]);
        return $stmt->fetchAll();
    }
    public function getSaleVentilationByFactAndRef($facture, $ref)
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT RTRIM(LTRIM(MVTL.VTLNA)) AS Vtl
        FROM MVTL
        WHERE MVTL.PINO = $facture AND MVTL.REF = '$ref' AND MVTL.DOS = 1 AND MVTL.OP IN ('C','D') AND  MVTL.TICOD = 'C'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getPurchase($ref, $VentAss)
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT RTRIM(LTRIM(MVTL.PINO)) AS Facture
        FROM MVTL
        WHERE MVTL.REF = '$ref' AND MVTL.DOS = 1 AND MVTL.OP IN ('F','G') AND MVTL.TICOD = 'F' AND MVTL.VTLNO IN ($VentAss)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCma($facture, $Ref)
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT (SUM(MontantSign) / SUM(QteSign)) AS Pu
        FROM(
        SELECT MOUV.OP AS Op, MOUV.MONT, MOUV.REMPIEMT_0004,MOUV.FAQTE,
        CASE -- Signature du montant
            WHEN MOUV.OP IN('F','FD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
            WHEN MOUV.OP IN('GD','G') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
            ELSE 0
        END AS MontantSign,
        CASE
            WHEN MOUV.OP IN('F','FD') THEN MOUV.FAQTE
            WHEN MOUV.OP IN('G','GD') THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSign
        FROM MOUV
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FANO IN ($facture) AND MOUV.REF = '$Ref')reponse
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getDetailPurchase($facture)
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT MOUV.FANO AS Facture,MOUV.REF AS Ref, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2, MOUV.DES AS Designation, MOUV.VENUN AS Uv, MOUV.FAQTE AS Qte,MOUV.OP AS Op, MOUV.REMPIEMT_0004 AS Remise, MOUV.MONT AS Montant,
        CASE -- Signature du montant
            WHEN MOUV.OP IN('F','FD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
            WHEN MOUV.OP IN('GD','G') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
            ELSE 0
        END AS MontantSign
        FROM MOUV
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FANO IN ($facture)
        
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    


    
} 
