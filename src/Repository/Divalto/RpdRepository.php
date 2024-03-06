<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RpdRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    public function getRpd($annee): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CLI.CPOSTAL AS Cp,ART.UP_CODEAMM AS Amm, ART.UP_CERTIPHYTO AS TypeArt, MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.VENUN AS Uv, MOUV.OP AS Op, ART.UP_TAXEPOLLUTION AS Rpd,
        CASE
        WHEN MOUV.OP IN ('DD','D') THEN -1 * MOUV.FAQTE
        WHEN MOUV.OP IN ('CD','C') THEN MOUV.FAQTE
        END AS QteSign
        FROM MOUV
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.FAM_0001 IN('PHYTO','BIOCONTR') AND YEAR(MOUV.FADT) IN ($annee) AND CLI.STAT_0003 <> 'DISTRI'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getRpdXML($annee): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Cp AS cp, Amm AS amm, SUM(QteSign) AS quantite
        FROM(
        SELECT CLI.CPOSTAL AS Cp,ART.UP_CODEAMM AS Amm, ART.UP_CERTIPHYTO AS TypeArt, MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.VENUN AS Uv, MOUV.OP AS Op, ART.UP_TAXEPOLLUTION AS Rpd,
                CASE
                WHEN MOUV.OP IN ('DD','D') THEN -1 * MOUV.FAQTE
                WHEN MOUV.OP IN ('CD','C') THEN MOUV.FAQTE
                END AS QteSign
                FROM MOUV
                INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
                INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
                WHERE MOUV.DOS = 1 AND ART.UP_CODEAMM <> '' AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.FAM_0001 IN('PHYTO','BIOCONTR') AND YEAR(MOUV.FADT) IN ($annee) AND CLI.STAT_0003 <> 'DISTRI') r
                GROUP BY Cp, Amm
                HAVING SUM(QteSign) > 0
                ORDER BY quantite
                ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getDeclarationCepp($annee): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.TYPEARTCOD AS typeArticle, m.REF AS ref, m.SREF1 AS sref1, m.SREF2 AS sref2, a.DES AS designation, m.VENUN AS uv,
        SUM(CASE
        WHEN m.OP IN ('C','CD') THEN m.FAQTE
        WHEN m.OP IN ('D','DD') THEN -1 * m.FAQTE
        END) AS qte
        FROM MOUV m
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = 1 AND a.TYPEARTCOD <> '' AND a.TYPEARTCOD NOT IN ('T-ENTRET')
        AND YEAR(m.FADT) = $annee AND m.TICOD = 'C' AND m.PICOD = 4
        GROUP BY a.TYPEARTCOD, m.REF, m.SREF1, m.SREF2, a.DES, m.VENUN
        HAVING SUM(CASE
           WHEN m.OP IN ('C','CD') THEN m.FAQTE
           WHEN m.OP IN ('D','DD') THEN -1 * m.FAQTE
           ELSE 0
       END) > 0
        ORDER BY a.TYPEARTCOD DESC
                ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
