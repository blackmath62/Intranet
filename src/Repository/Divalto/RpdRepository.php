<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class RpdRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getRpd($annee):array
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
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.FAM_0001 IN('PHYTO','BIOCONTR') AND YEAR(MOUV.FADT) IN (?) AND CLI.STAT_0003 <> 'DISTRI'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee]);
        return $stmt->fetchAll();
    }


}
