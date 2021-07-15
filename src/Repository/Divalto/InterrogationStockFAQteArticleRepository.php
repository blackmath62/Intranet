<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class InterrogationStockFAQteArticleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getInterrogationStockFAQteArticle($dateDebutN, $dateFinN):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Ref, Designation, Sref1, Sref2, Uv,SUM(Stock) AS Stock, SUM(QteSign) AS QteSign
        FROM(
        SELECT SART.REF AS Ref, ART.DES AS Designation, SART.SREF1 AS Sref1, SART.SREF2 AS Sref2, ART.VENUN AS Uv, MVTL_STOCK_V.QTETJSENSTOCK AS Stock,
        CASE 
        WHEN MOUV.OP IN ('C','CD') THEN MOUV.FAQTE
        WHEN MOUV.OP IN ('D','DD') THEN -1*MOUV.FAQTE
        END AS QteSign
        FROM SART
        LEFT JOIN MOUV ON MOUV.REF = SART.REF AND MOUV.SREF1 = SART.SREF1 AND MOUV.SREF2 = SART.SREF2 AND MOUV.DOS = SART.DOS AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.FADT >= '$dateDebutN' AND MOUV.FADT <= '$dateFinN'
        INNER JOIN ART ON SART.REF = ART.REF AND SART.DOS = ART.DOS AND ART.HSDT IS NULL
        LEFT JOIN MVTL_STOCK_V ON SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND SART.DOS = MVTL_STOCK_V.DOSSIER
        WHERE SART.DOS = 1
        AND SART.REF IN ('CO2230','CO2940','CO3048','CO1000','CO1512','CO1800', 'AF00BD131610A')
        )Reponse
        WHERE NOT Stock IS NULL OR NOT QteSign IS NULL  
        GROUP BY Ref, Designation, Sref1, Sref2, Uv";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }  
    
}