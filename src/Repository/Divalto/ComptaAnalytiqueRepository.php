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
   

    public function getComptaAnalytiqueByMonth($annee,$mois):array
    {

        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT LTRIM(RTRIM(MOUV.FADT)) AS DateFacture, LTRIM(RTRIM(MOUV.TICOD)) AS TypeTiers, LTRIM(RTRIM(MOUV.PICOD)) AS TypePiece,LTRIM(RTRIM(MOUV.FANO)) AS Facture,
        MOUV.CE1, MOUV.CE2, MOUV.CE3, MOUV.CE4, MOUV.CE5, MOUV.CE6, MOUV.CE7, MOUV.CE8, 
        LTRIM(RTRIM(MOUV.REF)) AS Ref,LTRIM(RTRIM(MOUV.SREF1)) AS Sref1, LTRIM(RTRIM(MOUV.SREF2)) AS Sref2, LTRIM(RTRIM(MOUV.DES)) AS Designation, LTRIM(RTRIM(MOUV.FAQTE)) AS Qte,
        LTRIM(RTRIM(MOUV.CRTOTMT)) AS CrTotal, LTRIM(RTRIM(MOUV.SENS)) AS Sens, LTRIM(RTRIM(MOUV.OP)) AS Op,
        LTRIM(RTRIM(ART.FAM_0002)) AS Article, LTRIM(RTRIM(CLI.STAT_0002)) AS Client, LTRIM(RTRIM(ART.CPTA)) AS CompteAchat, LTRIM(RTRIM(ART.TVAARTA)) AS TvaArticle,
        CASE
        WHEN MOUV.CRTOTMT <> 0 AND MOUV.FAQTE <> 0 THEN MOUV.CRTOTMT/MOUV.FAQTE
        ELSE 0
        END AS CoutRevient
        FROM MOUV
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
        WHERE YEAR(MOUV.FADT) IN (?) AND MONTH(MOUV.FADT) IN (?) AND MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND ART.FAM_0002 IN ('EV', 'HP')
        ORDER BY Facture";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois]);
        return $stmt->fetchAll();
    }
} 
