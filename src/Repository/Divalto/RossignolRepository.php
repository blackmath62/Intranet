<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RossignolRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    public function getRossignolStockList(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "WITH StockCTE AS (
                SELECT
                    REFERENCE, SREFERENCE1, SREFERENCE2, DEPOT, NATURESTOCK, SUM(QTETJSENSTOCK) AS Stock
                FROM
                    MVTL_STOCK_V
                WHERE
                    DOSSIER = 1 AND NATURESTOCK = 'O' --AND REFERENCE LIKE 'CO4527OC'
                GROUP BY
                    REFERENCE, SREFERENCE1, SREFERENCE2, DEPOT, NATURESTOCK
                )
                SELECT
                    s.REFERENCE AS Ref, s.SREFERENCE1 AS Sref1, s.SREFERENCE2 AS Sref2, SUM(m.CDQTE) AS CmdQte, s.ARTICLE_DESIGNATION AS Designation, a.VENUN AS Uv,
                    s.DEPOT AS Depot, s.NATURESTOCK AS Nature, st.Stock AS Stock, t.TACOD AS CodeTarif, t.PUB AS Tarif, MAX(t.TADT) AS DateTarif,
                    CASE
                        WHEN t.PPAR <> 0 AND t.PPAR IS NOT NULL THEN t.PPAR
                    END AS PrixPar
                FROM StockCTE st
                INNER JOIN
                    MVTL_STOCK_V s ON st.REFERENCE = s.REFERENCE AND st.SREFERENCE1 = s.SREFERENCE1 AND st.SREFERENCE2 = s.SREFERENCE2
                    AND st.DEPOT = s.DEPOT AND st.NATURESTOCK = s.NATURESTOCK
                INNER JOIN
                    ART a ON a.REF = s.REFERENCE AND a.DOS = s.DOSSIER
                LEFT JOIN
                    MOUV m ON s.REFERENCE = m.REF AND s.SREFERENCE1 = m.SREF1 AND s.SREFERENCE2 = m.SREF2
                    AND m.DOS = s.DOSSIER AND m.CDCE4 IN (1) AND m.TICOD = 'C' AND m.OP IN('C 2', 'CO')
                LEFT JOIN
                    TAR t ON t.DOS = a.DOS AND t.REF = a.REF AND t.SREF1 = s.SREFERENCE1
                    AND t.SREF2 = s.SREFERENCE2 AND t.TACOD = 'TO'
                WHERE
                    s.DOSSIER = 1 AND s.NATURESTOCK = 'O' --AND s.REFERENCE LIKE 'CO4527OC'
                GROUP BY
                    s.REFERENCE, s.SREFERENCE1, s.SREFERENCE2, s.ARTICLE_DESIGNATION, a.VENUN, s.DEPOT, s.NATURESTOCK, t.TACOD, t.PUB, t.PPAR, st.Stock
                ORDER BY
                    s.REFERENCE;
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getRossignolVenteList($annee): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Tiers, Nom, Commercial, Ref, Sref1, Sref2, Designation, Uv, TAR.TACOD AS CodeTarif, TAR.TADT AS dateTarif, TAR.PUB AS Prix, TAR.PPAR AS PrixParTO,
        SUM(QteSign) AS Qte, (SUM(MontantSign) / SUM(QteSign)) AS Pu, PrixPar,  SUM(MontantSign) AS Montant
        FROM(
        SELECT RTRIM(LTRIM(MOUV.DOS)) AS Dos, RTRIM(LTRIM(CLI.TIERS)) AS Tiers, RTRIM(LTRIM(CLI.NOM)) AS Nom, RTRIM(LTRIM(VRP.SELCOD)) AS Commercial,
        RTRIM(LTRIM(MOUV.REF)) AS Ref,  RTRIM(LTRIM(MOUV.SREF1)) AS Sref1,RTRIM(LTRIM(MOUV.SREF2)) AS Sref2,
        RTRIM(LTRIM(MOUV.DES)) AS Designation, RTRIM(LTRIM(ART.VENUN)) AS Uv,RTRIM(LTRIM(MOUV.OP)) AS Op,
        RTRIM(LTRIM(MOUV.MONT)) AS Montant, RTRIM(LTRIM(MOUV.REMPIEMT_0004)) AS Remise, RTRIM(LTRIM(MOUV.FADT)) AS DateFacture,
        RTRIM(LTRIM(MOUV.FAQTE)) AS QuantiteFacture,
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
        WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.OP IN('C 2', 'CO') AND CLI.STAT_0002 = 'HP' AND YEAR(MOUV.FADT) IN($annee)
        GROUP BY MOUV.DOS, CLI.TIERS, CLI.NOM, VRP.SELCOD, MOUV.REF,  MOUV.SREF1, MOUV.SREF2,MOUV.DES,ART.VENUN,MOUV.OP, MOUV.PPAR, MOUV.FADT, MOUV.FAQTE, MOUV.MONT, MOUV.REMPIEMT_0004)reponse
        LEFT JOIN TAR ON Dos = TAR.DOS AND Ref = TAR.REF AND Sref1 = TAR.SREF1 AND Sref2 = TAR.SREF2 AND TAR.TACOD = 'TO'
        GROUP BY Tiers, Nom, Commercial, Ref, Sref1, Sref2, Designation, Uv, TAR.TACOD, TAR.TADT, TAR.PUB, TAR.PPAR, PrixPar
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
