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

}
