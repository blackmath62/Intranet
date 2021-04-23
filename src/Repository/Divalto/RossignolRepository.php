<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class RossignolRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getRossignolStockList():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MVTL_STOCK_V.REFERENCE AS Ref,  MVTL_STOCK_V.SREFERENCE1 AS Sref1,MVTL_STOCK_V.SREFERENCE2 AS Sref2, SUM(MOUV.CDQTE) AS CmdQte
        ,MVTL_STOCK_V.ARTICLE_DESIGNATION AS Designation, ART.VENUN AS Uv,
        MVTL_STOCK_V.DEPOT AS Depot,MVTL_STOCK_V.NATURESTOCK AS Nature, SUM(MVTL_STOCK_V.QTETJSENSTOCK) AS Stock
        FROM MVTL_STOCK_V
        INNER JOIN ART ON ART.REF = MVTL_STOCK_V.REFERENCE AND ART.DOS = MVTL_STOCK_V.DOSSIER
        LEFT JOIN MOUV ON MVTL_STOCK_V.REFERENCE = MOUV.REF AND MVTL_STOCK_V.SREFERENCE1 = MOUV.SREF1 AND MVTL_STOCK_V.SREFERENCE2 = MOUV.SREF2 AND MOUV.DOS = MVTL_STOCK_V.DOSSIER AND MOUV.CDCE4 IN (1) AND MOUV.TICOD = 'C' AND MOUV.OP = 'C 2'
        WHERE MVTL_STOCK_V.DOSSIER = 1 AND MVTL_STOCK_V.NATURESTOCK = 'O' --MVTL_STOCK_V.REFERENCE LIKE 'CO%' AND
        GROUP BY MVTL_STOCK_V.REFERENCE,  MVTL_STOCK_V.SREFERENCE1,MVTL_STOCK_V.SREFERENCE2,MVTL_STOCK_V.ARTICLE_DESIGNATION,ART.VENUN,MVTL_STOCK_V.QTERESERVE,
        MVTL_STOCK_V.DEPOT,MVTL_STOCK_V.NATURESTOCK
        ORDER BY MVTL_STOCK_V.REFERENCE";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}


