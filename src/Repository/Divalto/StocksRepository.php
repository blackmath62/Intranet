<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StocksRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    public function getStocks(): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation,cmdCli, cmdFou, Sum(Stock) AS Stock, (-1 * cmdCli + cmdFou + Sum(Stock)) AS total
        FROM(SELECT ART.TIERS AS Fournisseur, SART.REF AS Ref, SART.SREF1 AS Sref1, SART.SREF2 AS Sref2, ART.DES AS Designation, ART.VENUN AS Uv,ART.CDECLQTE AS cmdCli, ART.CDEFOQTE AS cmdFou,
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
        GROUP BY ArtFerme, Fournisseur, Ref, Sref1, Sref2, Designation, Uv,cmdCli, cmdFou
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
