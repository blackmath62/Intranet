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
        MVTL_STOCK_V.DEPOT AS Depot,MVTL_STOCK_V.NATURESTOCK AS Nature, SUM(MVTL_STOCK_V.QTETJSENSTOCK) AS Stock,
        TAR.TACOD AS CodeTarif, TAR.PUB AS Tarif, MAX(TAR.TADT) AS DateTarif,
        CASE
        WHEN TAR.PPAR <> 0 AND TAR.PPAR IS NOT NULL THEN TAR.PPAR
        END AS PrixPar
        FROM MVTL_STOCK_V
        INNER JOIN ART ON ART.REF = MVTL_STOCK_V.REFERENCE AND ART.DOS = MVTL_STOCK_V.DOSSIER
        LEFT JOIN MOUV ON MVTL_STOCK_V.REFERENCE = MOUV.REF AND MVTL_STOCK_V.SREFERENCE1 = MOUV.SREF1 AND MVTL_STOCK_V.SREFERENCE2 = MOUV.SREF2 AND MOUV.DOS = MVTL_STOCK_V.DOSSIER AND MOUV.CDCE4 IN (1) AND MOUV.TICOD = 'C' AND MOUV.OP IN('C 2','CO')
        LEFT JOIN TAR ON TAR.DOS = ART.DOS AND TAR.REF = ART.REF AND TAR.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND TAR.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND TAR.TACOD = 'TO'
        WHERE MVTL_STOCK_V.DOSSIER = 1 AND MVTL_STOCK_V.NATURESTOCK = 'O' --MVTL_STOCK_V.REFERENCE LIKE 'CO%' AND
        GROUP BY MVTL_STOCK_V.REFERENCE,  MVTL_STOCK_V.SREFERENCE1,MVTL_STOCK_V.SREFERENCE2,MVTL_STOCK_V.ARTICLE_DESIGNATION,ART.VENUN,MVTL_STOCK_V.QTERESERVE,
        MVTL_STOCK_V.DEPOT,MVTL_STOCK_V.NATURESTOCK, TAR.TACOD, TAR.PUB, TAR.PPAR, TAR.PPAR
        ORDER BY MVTL_STOCK_V.REFERENCE
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRossignolVenteList():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Tiers, Nom, Commercial, Ref, Sref1, Sref2, Designation, Uv, TAR.TACOD AS CodeTarif, TAR.PUB AS Prix, TAR.PPAR AS PrixParTO, 
        SUM(QteSign) AS Qte, (SUM(MontantSign) / SUM(QteSign)) AS Pu, PrixPar,  SUM(MontantSign) AS Montant 
        FROM(
        SELECT MOUV.DOS AS Dos, CLI.TIERS AS Tiers, CLI.NOM AS Nom, VRP.SELCOD AS Commercial,  MOUV.REF AS Ref,  MOUV.SREF1 AS Sref1,MOUV.SREF2 AS Sref2, 
        MOUV.DES AS Designation, ART.VENUN AS Uv,MOUV.OP AS Op, MOUV.MONT AS Montant, MOUV.REMPIEMT_0004 AS Remise, MOUV.FADT AS DateFacture, MOUV.FAQTE AS QuantiteFacture,
        CASE -- Signature du montant
            WHEN MOUV.OP IN('C 2','CO') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
            WHEN MOUV.OP IN('D 2','DO') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
            ELSE 0
        END AS MontantSign,
        CASE -- Signature du montant
            WHEN MOUV.OP IN('C 2','CO') THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D 2','DO') THEN (-1 * MOUV.FAQTE)
            ELSE 0
        END AS QteSign,
        CASE
        WHEN MOUV.PPAR <> 0 AND MOUV.PPAR IS NOT NULL THEN MOUV.PPAR
        ELSE NULL
        END AS PrixPar
        FROM MOUV
        INNER JOIN ART ON ART.REF = MOUV.REF AND ART.DOS = MOUV.DOS
        INNER JOIN CLI ON MOUV.DOS = CLI.DOS AND MOUV.TIERS = CLI.TIERS
        INNER JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.OP IN('C 2', 'CO') AND CLI.STAT_0002 = 'HP' AND YEAR(MOUV.FADT) IN(2021)
        GROUP BY MOUV.DOS, CLI.TIERS, CLI.NOM, VRP.SELCOD, MOUV.REF,  MOUV.SREF1, MOUV.SREF2,MOUV.DES,ART.VENUN,MOUV.OP, MOUV.PPAR, MOUV.FADT, MOUV.FAQTE, MOUV.MONT, MOUV.REMPIEMT_0004)reponse
        LEFT JOIN TAR ON Dos = TAR.DOS AND Ref = TAR.REF AND Sref1 = TAR.SREF1 AND Sref2 = TAR.SREF2 AND TAR.TACOD = 'TO'
        GROUP BY Tiers, Nom, Commercial, Ref, Sref1, Sref2, Designation, Uv, TAR.TACOD, TAR.PUB, TAR.PPAR, PrixPar
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}


