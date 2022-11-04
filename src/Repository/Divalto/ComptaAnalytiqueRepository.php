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

    public function getRapportClient($annee, $mois)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Numero AS Facture, Tiers AS Tiers, VentAss AS VentAss, Dos, Ref, Sref1, Sref2, Designation, Uv, Op AS Op,
        CoutRevient, CoutMoyenPondere, Article, Client, CompteAchat, CompteVente, RegimeTva, qteVtl AS qteVtl, regimeFou, VtlNo
       FROM
       (
       SELECT RTRIM(LTRIM(m.TIERS)) AS Tiers, RTRIM(LTRIM(v.VTLNA)) AS VentAss, RTRIM(LTRIM(v.QTE)) AS qteVtl, RTRIM(LTRIM(m.DOS)) AS Dos, m.FADT AS DateFacture, RTRIM(LTRIM(m.FANO)) AS Numero,
       RTRIM(LTRIM(m.REF)) AS Ref, RTRIM(LTRIM(m.SREF1)) AS Sref1, RTRIM(LTRIM(m.SREF2)) AS Sref2, RTRIM(LTRIM(a.DES)) AS Designation, RTRIM(LTRIM(a.VENUN)) AS Uv,
       RTRIM(LTRIM(m.OP)) AS Op,RTRIM(LTRIM(m.AXE_0001)) AS Article, RTRIM(LTRIM(m.AXE_0002)) AS Client, RTRIM(LTRIM(a.CPTA)) AS CompteAchat, CONCAT(6 , RIGHT(RTRIM(LTRIM(m.CPTV)), 7)) AS CompteVente,
       RTRIM(LTRIM(a.TVAART)) AS RegimeTva, RTRIM(LTRIM(a.TIERS)) AS fPrin, f.TVATIE AS regimeFou, v.VTLNO AS VtlNo,
       CASE
           WHEN m.OP IN('C','CD') THEN m.FAQTE
           WHEN m.OP IN('D','DD') THEN -1*m.FAQTE
           ELSE 0
       END AS QteSign,
       CASE
           WHEN m.OP IN('C','CD') THEN s.CR
           WHEN m.OP IN('D','DD') THEN -1*s.CR
           ELSE 0
       END AS CoutRevient,
       CASE
           WHEN m.OP IN('C','CD') THEN s.CMP
           WHEN m.OP IN('D','DD') THEN -1*s.CMP
           ELSE 0
       END AS CoutMoyenPondere
       FROM MOUV m
       INNER JOIN ART a ON m.REF = a.REF AND m.DOS = a.DOS
       INNER JOIN FOU f ON a.TIERS = f.TIERS AND a.DOS = f.DOS
       LEFT JOIN SART s ON m.REF = s.REF AND m.DOS = s.DOS AND m.SREF1 = s.SREF1 AND m.SREF2 = s.SREF2
       INNER JOIN CLI c ON m.TIERS = c.TIERS AND m.DOS = c.DOS
       LEFT JOIN MVTL v ON m.DOS = v.DOS AND m.TIERS = v.TIERS AND m.FANO = v.PINO AND m.REF = v.REF AND m.SREF1 = v.SREF1 AND m.SREF2 = v.SREF2
       WHERE m.DOS = 1 AND m.TICOD = 'C' AND m.PICOD = 4 AND m.OP IN ('C','D') AND NOT m.TIERS IN ('C0160500')
       AND m.AXE_0002 <> m.AXE_0001 -- a.FAM_0002 <> c.STAT_0002
       AND a.FAM_0002 IN ('EV', 'HP')AND YEAR(m.FADT) IN (?) AND MONTH(m.FADT) IN (?)
       )reponse
       GROUP BY Numero, Tiers, VentAss, Dos, Ref, Sref1, Sref2, Designation, Uv, Op, CoutRevient, CoutMoyenPondere, Article, Client, CompteAchat, CompteVente, RegimeTva, qteVtl, regimeFou, VtlNo
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee, $mois]);
        return $stmt->fetchAll();
    }

    /* 
    
    Ancienne requête getRapportClient

    SELECT Numero AS Facture, Tiers AS Tiers, VentAss AS VentAss, Dos, Ref, Sref1, Sref2, Designation, Uv, Op AS Op,
         CoutRevient, CoutMoyenPondere, Article, Client, CompteAchat, RegimeTva, SUM(QteSign) AS QteSign, qteVtl AS qteVtl, regimeFou
        FROM
        (
        SELECT RTRIM(LTRIM(MOUV.TIERS)) AS Tiers, RTRIM(LTRIM(MVTL.VTLNA)) AS VentAss, RTRIM(LTRIM(MVTL.QTE)) AS qteVtl, RTRIM(LTRIM(MOUV.DOS)) AS Dos, MOUV.FADT AS DateFacture, RTRIM(LTRIM(MOUV.FANO)) AS Numero,
        RTRIM(LTRIM(MOUV.REF)) AS Ref, RTRIM(LTRIM(MOUV.SREF1)) AS Sref1, RTRIM(LTRIM(MOUV.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation, RTRIM(LTRIM(ART.VENUN)) AS Uv,
        RTRIM(LTRIM(MOUV.OP)) AS Op,RTRIM(LTRIM(ART.FAM_0002)) AS Article, RTRIM(LTRIM(CLI.STAT_0002)) AS Client, RTRIM(LTRIM(ART.CPTA)) AS CompteAchat,
        RTRIM(LTRIM(ART.TVAART)) AS RegimeTva, RTRIM(LTRIM(ART.TIERS)) AS fouPrin, FOU.TVATIE AS regimeFou,
        CASE
            WHEN MOUV.OP IN('C','CD') THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD') THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QteSign,
        CASE
            WHEN MOUV.OP IN('C','CD') THEN SART.CR
            WHEN MOUV.OP IN('D','DD') THEN -1*SART.CR
            ELSE 0
        END AS CoutRevient,
        CASE
            WHEN MOUV.OP IN('C','CD') THEN SART.CMP
            WHEN MOUV.OP IN('D','DD') THEN -1*SART.CMP
            ELSE 0
        END AS CoutMoyenPondere
        FROM MOUV 
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        INNER JOIN FOU ON ART.TIERS = FOU.TIERS AND ART.DOS = FOU.DOS
        LEFT JOIN SART ON MOUV.REF = SART.REF AND MOUV.DOS = SART.DOS AND MOUV.SREF1 = SART.SREF1 AND MOUV.SREF2 = SART.SREF2
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
        LEFT JOIN MVTL ON MOUV.DOS = MVTL.DOS AND MOUV.TIERS = MVTL.TIERS AND MOUV.FANO = MVTL.PINO AND MOUV.REF = MVTL.REF AND MOUV.SREF1 = MVTL.SREF1 AND MOUV.SREF2 = MVTL.SREF2
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.OP IN ('C','D') AND NOT MOUV.TIERS IN ('C0160500')
        AND ART.FAM_0002 <> CLI.STAT_0002
        AND ART.FAM_0002 IN ('EV', 'HP')AND YEAR(MOUV.FADT) IN (?) AND MONTH(MOUV.FADT) IN (?)
        )reponse
        GROUP BY Numero, Tiers, VentAss, Dos, Ref, Sref1, Sref2, Designation, Uv, Op, CoutRevient, CoutMoyenPondere, Article, Client, CompteAchat, RegimeTva, qteVtl, regimeFou
    */

    public function getRapportFournisseurAvecSref($ventilation, $ref, $sref1, $sref2)
    {
        $code = '';
        $filtre = '';
        if ($sref1 && !$sref2) {
            $code = "AND '" . $sref1 . "'= MOUV.SREF1";
            $filtre = " AND LTRIM(RTRIM(MVTL.SREF1)) = '" . $sref1 . "'";
        }elseif ($sref2 && ! $sref1) {
            $code = "AND '" . $sref2 . "'= MOUV.SREF2";
            $filtre = " AND LTRIM(RTRIM(MVTL.SREF2)) = '" . $sref2 . "'";
        }elseif ($sref1 && $sref2 ) {
            $code = "AND '" . $sref1 . "'= MOUV.SREF1" . " AND '" . $sref2 . "'= MOUV.SREF2";
            $filtre = " AND LTRIM(RTRIM(MVTL.SREF1)) = ' " . $sref1 . "'" . " AND LTRIM(RTRIM(MVTL.SREF2)) = '" . $sref2 . "'";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Vent,ticod, pinoFou, fou, regimePiece,
        CASE 
        WHEN MOUV.MONT <> 0 AND MOUV.FAQTE <> 0 AND MOUV.OP IN('F','FD') THEN MOUV.MONT/MOUV.FAQTE
        WHEN MOUV.MONT <> 0 AND MOUV.FAQTE <> 0 AND MOUV.OP IN('G','GD') THEN (-1 * MOUV.MONT)/MOUV.FAQTE
        END AS pa
        FROM(
        SELECT Vent,ticod, pinoFou, fou, FOU.TVATIE AS regimePiece
        FROM(
        SELECT MVTL.VTLNO AS Vent, MVTL.TICOD AS ticod, MVTL.PINO AS pinoFou, MVTL.TIERS AS fou
        FROM MVTL
        WHERE MVTL.VTLNO = $ventilation AND LTRIM(RTRIM(MVTL.REF)) = '$ref' AND MVTL.TICOD = 'F' AND MVTL.PICOD = 4 $filtre
        )reponse3
        LEFT JOIN FOU ON fou = FOU.TIERS AND 1 = FOU.DOS) reponse4
        LEFT JOIN MOUV ON pinoFou = MOUV.FANO AND fou = MOUV.TIERS AND 1 = MOUV.DOS AND '$ref' = MOUV.REF $code
        WHERE MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.DOS = 1
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getDetailPieceFournisseur($piece)
    {
    
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT MOUV.FANO AS Facture, ART.FAM_0001 AS famille, ART.FAM_0002 AS article, MOUV.REF AS Ref, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2,
         MOUV.DES AS Designation, MOUV.OP AS Op,
         CASE
         WHEN MOUV.OP IN ('FD','F') THEN MOUV.FAQTE
         WHEN MOUV.OP IN ('GD','G') THEN -1 * MOUV.FAQTE
         END AS Qte,
         CASE
         WHEN MOUV.OP IN ('FD','F') THEN MOUV.MONT
         WHEN MOUV.OP IN ('GD','G') THEN -1 * MOUV.MONT
         END AS MontantSign,  
         CASE
         WHEN MOUV.MONT <> 0 AND MOUV.FAQTE <> 0 AND MOUV.OP IN ('FD','F') THEN MOUV.MONT/MOUV.FAQTE
         WHEN MOUV.MONT <> 0 AND MOUV.FAQTE <> 0 AND MOUV.OP IN ('GD','G') THEN (-1 * MOUV.MONT) / MOUV.FAQTE
         END AS pu
        FROM MOUV 
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        WHERE MOUV.PICOD = 4 AND MOUV.TICOD = 'F' AND MOUV.FANO = $piece AND NOT ART.REF IN ('ZRPO196', 'ZRPO196HP', 'ZRPO7', 'ZRPO7HP') AND MOUV.DOS = 1
        ORDER BY ART.FAM_0001
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getQteHorsPortFournisseur($piece)
    {
    
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT SUM(qte) AS qte
        FROM(
        SELECT MOUV.DOS AS dos,
        CASE
        WHEN MOUV.OP IN ('FD','F') THEN MOUV.FAQTE
        WHEN MOUV.OP IN ('GD','G') THEN -1 * MOUV.FAQTE
        END AS qte
        FROM MOUV 
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        WHERE MOUV.PICOD = 4 AND MOUV.TICOD = 'F' AND MOUV.FANO = $piece AND ART.FAM_0001 <> 'TRANSPOR' AND NOT ART.REF IN ('ZRPO196', 'ZRPO196HP', 'ZRPO7', 'ZRPO7HP') AND MOUV.DOS = 1)reponse
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getTransportFournisseur($piece, $metier)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT SUM(montant) AS montant
        FROM(
        SELECT MOUV.DOS AS dos,
        CASE
        WHEN MOUV.OP IN ('FD','F') THEN MOUV.MONT
        WHEN MOUV.OP IN ('GD','G') THEN -1 * MOUV.MONT
        END AS montant
        FROM MOUV 
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        WHERE MOUV.PICOD = 4 AND MOUV.TICOD = 'F' AND MOUV.FANO = $piece AND ART.FAM_0001 = 'TRANSPOR' 
        AND NOT ART.REF IN ('ZRPO196', 'ZRPO196HP', 'ZRPO7', 'ZRPO7HP') AND ART.FAM_0002 = '$metier' AND MOUV.DOS = 1)reponse
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
} 
