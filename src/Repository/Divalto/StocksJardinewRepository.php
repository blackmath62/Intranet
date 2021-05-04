<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class StocksJardinewRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getStocksJardinewRepository():array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation, Sum(Stock) AS Stock
        FROM(SELECT ART.TIERS AS Fournisseur, SART.REF AS Ref, SART.SREF1 AS Sref1, SART.SREF2 AS Sref2, ART.DES AS Designation, ART.VENUN AS Uv,
        CASE
            WHEN SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 THEN MVTL_STOCK_V.QTETJSENSTOCK
        END AS Stock,
        CASE
            WHEN (SART.CONF IS NOT NULL AND ART.HSDT IS NOT NULL) THEN 'CLOSE'
            ELSE ''
        END AS  ArtFerme
        FROM SART
        INNER JOIN ART ON SART.REF = ART.REF AND SART.DOS = ART.DOS
        LEFT JOIN MVTL_STOCK_V ON SART.DOS = MVTL_STOCK_V.DOSSIER AND SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL
        WHERE SART.DOS = 1 -- AND SART.REF LIKE ('BAU%')
        ) reponse
        WHERE Stock IS NOT NULL
        GROUP BY ArtFerme, Fournisseur, Ref, Sref1, Sref2, Designation, Uv
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}


