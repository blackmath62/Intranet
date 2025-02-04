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

    public function getStocks($ref, $des, $cmd, $direct): array
    {

        $referenceReponse = "";
        $designationReponse = "";
        $referenceLast = "";
        $designationLast = "";
        $commande = "Stock > 0";
        if ($ref) {
            $referenceReponse = "AND a.REF LIKE ('$ref%')";
            $referenceLast = "AND Ref LIKE ('$ref%')";
        }
        if ($des) {
            $designationReponse = "AND a.DES LIKE ('%$des%')";
            $designationLast = "AND Designation LIKE ('%$des%')";
        }
        if ($cmd == false) {
            $commande = "vtl > 0";
        }
        if ($direct == true) {
            $clientV = "'C','CD'";
            $clientA = "'D','DD'";
            $fournisseurV = "'F','FD'";
            $fournisseurA = "'G','GD'";
            $op = "AND m.OP IN ('C', 'CD', 'D', 'DD', 'F', 'FD', 'G', 'GD')";
            $natureS = '';
        } else {
            $clientV = "'C'";
            $clientA = "'D'";
            $fournisseurV = "'F'";
            $fournisseurA = "'G'";
            $op = "AND m.OP IN ('C', 'D', 'F', 'G')";
            $natureS = "AND s.NATURESTOCK IN ('N', 'O')";
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM(
            SELECT dos, ArtFerme,Fournisseur, LTRIM(RTRIM(Ref)) AS Ref, LTRIM(RTRIM(Sref1)) AS Sref1, LTRIM(RTRIM(Sref2)) AS Sref2,
            Uv, Designation,nature, Stock, SUM(cmdCli) AS cmdCli, SUM(cmdFou) AS cmdFou, (-1 * SUM(cmdCli) + SUM(cmdFou) + Stock) AS total,
            ABS(SUM(cmdCli)) + ABS(SUM(cmdFou)) + ABS(Stock) AS vtl
            FROM( -- last
            SELECT dos, ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation,nature, Stock,
            CASE
            WHEN m.OP IN ($clientV) THEN m.CDQTE
            WHEN m.OP IN ($clientA) THEN -1 * m.CDQTE
            ELSE 0
            END AS cmdCli,
            CASE
            WHEN m.OP IN ($fournisseurV) THEN m.CDQTE
            WHEN m.OP IN ($fournisseurA) THEN -1 * m.CDQTE
            ELSE 0
            END AS cmdFou
            FROM( --ad
            SELECT dos, ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation,nature, Sum(Stock) AS Stock
            FROM( --rep
            SELECT LTRIM(RTRIM(sr.DOS)) AS dos, LTRIM(RTRIM(a.TIERS)) AS Fournisseur, sr.REF AS Ref, sr.SREF1 AS Sref1, sr.SREF2 AS Sref2, LTRIM(RTRIM(a.DES)) AS Designation, a.VENUN AS Uv,
            s.NATURESTOCK AS nature,
            CASE
                WHEN sr.REF = s.REFERENCE AND sr.SREF1 = s.SREFERENCE1 AND sr.SREF2 = s.SREFERENCE2 THEN s.QTETJSENSTOCK
                ELSE 0
            END AS Stock,
            CASE
                WHEN (sr.CONF IS NOT NULL AND a.HSDT IS NOT NULL) THEN 'CLOSE'
                ELSE ''
            END AS  ArtFerme
            FROM SART sr --reponse
            INNER JOIN ART a ON sr.REF = a.REF AND sr.DOS = a.DOS
            LEFT JOIN MVTL_STOCK_V s ON sr.DOS = s.DOSSIER AND sr.REF = s.REFERENCE AND sr.SREF1 = s.SREFERENCE1 AND sr.SREF2 = s.SREFERENCE2
			AND s.QTETJSENSTOCK IS NOT NULL $natureS
            WHERE sr.DOS = 1  $referenceReponse $designationReponse
            ) reponse
            GROUP BY dos, ArtFerme, Fournisseur, Ref, Sref1, Sref2, Designation, Uv, nature )rep
            LEFT JOIN MOUV m ON Ref = m.REF AND dos = m.DOS AND Sref1 = m.SREF1 AND Sref2 = m.SREF2 AND 1 = m.CDCE4 AND m.PICOD = 2 AND m.CDDT >= '2015-01-01' $referenceLast $designationLast)ad
            GROUP BY dos, ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation,nature, Stock)last
            WHERE ($commande)
            ORDER BY ArtFerme DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getStockjN($ref, $sref1 = null, $sref2 = null)
    {

        $s1 = "";
        $s2 = "";
        $sr1 = "";
        $sr2 = "";

        if ($sref1) {
            $s1 = "AND m.SREF1 IN ('$sref1')";
            $sr1 = "AND x.SREF1 IN ('$sref1')";
        }
        if ($sref2) {
            $s2 = "AND m.SREF2 IN ('$sref2')";
            $sr2 = "AND x.SREF2 IN ('$sref2')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "WITH LatestTarifs AS (
    -- Obtenir les tarifs les plus récents pour chaque combinaison unique de REF, SREF1, SREF2
    SELECT
        m.DOS,
		m.REF,
        m.SREF1,
        m.SREF2,
        m.FADT,
		m.TACOD,
		m.FAQTE,
        m.MONT,
        m.REMPIEMT_0004,
        m.FANO,
        m.PPAR,
        ROW_NUMBER() OVER(PARTITION BY m.REF, m.SREF1, m.SREF2 ORDER BY m.FADT DESC) AS sort
    FROM MOUV m
    WHERE m.DOS = 1
      AND m.PICOD = 4
      AND m.TICOD = 'F'
      AND m.OP IN ('F')
      AND m.REF = '$ref' $s1 $s2
),
TransporTotals AS (
    -- Total des montants des articles TRANSPOR pour chaque FANO
    SELECT
        m.FANO,
        SUM(m.MONT) AS total_mont_transpor
    FROM MOUV m
    INNER JOIN ART a ON m.REF = a.REF AND m.DOS = a.DOS
    WHERE m.DOS = 1
      AND a.FAM_0001 = 'TRANSPOR'
      AND m.PICOD = 4
      AND m.TICOD = 'F'
    GROUP BY m.FANO
),
NonTransporTotals AS (
    -- Somme des FAQTE pour les articles non TRANSPOR pour chaque FANO
    SELECT
        m.FANO,
        SUM(m.FAQTE) AS total_faqte_non_transpor
    FROM MOUV m
    INNER JOIN ART a ON m.REF = a.REF AND m.DOS = a.DOS
    WHERE m.DOS = 1
      AND a.FAM_0001 <> 'TRANSPOR'
      AND m.PICOD = 4
      AND m.TICOD = 'F'
    GROUP BY m.FANO
)
SELECT
    LTRIM(RTRIM(x.REF)) AS ref,
    LTRIM(RTRIM(x.SREF1)) AS sref1,
    LTRIM(RTRIM(x.SREF2)) AS sref2,
    LTRIM(RTRIM(a.DES)) AS designation,
    LTRIM(RTRIM(a.VENUN)) AS uv,
    SUM(stock.QTETJSENSTOCK) AS stock,
    CASE
        WHEN s.CONF = 'Usrd' AND a.SREFCOD = 2 THEN 'Usrd'
        ELSE ''
    END AS conf,
    LTRIM(RTRIM(x.FANO)) AS facture,
    LTRIM(RTRIM(x.TACOD)) AS code,
    x.FADT AS datePu,
    CASE
        WHEN x.MONT > 0 THEN (x.MONT - x.REMPIEMT_0004) / x.FAQTE
    END AS pu,
    CASE
        WHEN x.PPAR > 0 THEN x.PPAR
        ELSE NULL
    END AS ppar,
    a.TVAART AS tva,
    -- Calcul du ratio de port si applicable
    CASE
        WHEN nt.total_faqte_non_transpor > 0 THEN
            ISNULL(tt.total_mont_transpor, 0) / nt.total_faqte_non_transpor
        ELSE 0
    END AS ratio_port
FROM LatestTarifs x
INNER JOIN ART a ON x.REF = a.REF AND a.DOS = x.DOS
LEFT JOIN SART s ON x.REF = s.REF AND s.DOS = x.DOS AND s.SREF1 = x.SREF1 AND s.SREF2 = x.SREF2
LEFT JOIN MVTL_STOCK_V stock ON x.DOS = stock.DOSSIER AND x.REF = stock.REFERENCE AND x.SREF1 = stock.SREFERENCE1 AND x.SREF2 = stock.SREFERENCE2
LEFT JOIN TransporTotals tt ON x.FANO = tt.FANO
LEFT JOIN NonTransporTotals nt ON x.FANO = nt.FANO
WHERE x.sort = 1
  AND a.HSDT IS NULL
  AND a.FAM_0001 NOT IN ('', 'PRESTA', 'TRANSPOR', 'NC', 'LOCATION')
  AND x.REF = '$ref' $sr1 $sr2
  --AND YEAR(x.FADT) >= YEAR(GETDATE()) - 1
  --AND SUM(stock.QTETJSENSTOCK) > 0
GROUP BY
    x.REF, x.SREF1, x.SREF2, a.DES, a.VENUN, a.SREFCOD, s.CONF, x.TACOD, x.FANO, x.FAQTE,
    x.FADT, x.MONT, x.REMPIEMT_0004, a.TVAART, x.PPAR, tt.total_mont_transpor, nt.total_faqte_non_transpor
HAVING SUM(stock.QTETJSENSTOCK) > 0
ORDER BY ref ASC;
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

    public function getNoStockjN($ref)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "WITH LatestTarifs AS (
-- On commence par obtenir les tarifs les plus récents pour chaque code tarif, référence et configuration
    SELECT *,
        ROW_NUMBER() OVER(PARTITION BY t.TACOD, t.REF, t.SREF1, t.SREF2 ORDER BY t.TADT DESC) AS sort
    FROM TFO t
    WHERE t.DOS = 1 AND t.TACOD IN ('99','95T1' )
)
    SELECT * FROM(
    -- On filtre pour ne garder que les tarifs les plus récents
    SELECT
        LTRIM(RTRIM(x.REF)) AS ref,
        LTRIM(RTRIM(x.SREF1)) AS sref1,
        LTRIM(RTRIM(x.SREF2)) AS sref2,
        LTRIM(RTRIM(a.DES)) AS designation,
        LTRIM(RTRIM(a.VENUN)) AS uv,
		SUM(stock.QTETJSENSTOCK) AS stock ,
        CASE
            WHEN s.CONF = 'Usrd' AND a.SREFCOD = 2 THEN 'Usrd'
            ELSE ''
        END AS conf,
        CASE
            WHEN x.REF <> '' THEN 0
        END AS facture,
        LTRIM(RTRIM(x.TACOD)) AS code,
        x.TADT AS datePu,
        x.PA AS pu,
		CASE
		WHEN x.PPAR > 0 THEN x.PPAR
		ELSE NULL
		END AS ppar,
        a.TVAART AS tva
    FROM LatestTarifs x
    INNER JOIN ART a ON x.REF = a.REF AND a.DOS = x.DOS
    LEFT JOIN SART s ON x.REF = s.REF AND s.DOS = x.DOS AND s.SREF1 = x.SREF1 AND s.SREF2 = x.SREF2
	LEFT JOIN MVTL_STOCK_V stock ON x.DOS = stock.DOSSIER AND x.REF = stock.REFERENCE AND s.SREF1 = stock.SREFERENCE1 AND s.SREF2 = stock.SREFERENCE2
    WHERE x.sort = 1 AND a.REF = '$ref'
    AND a.HSDT IS NULL
    AND a.FAM_0001 NOT IN ('', 'PRESTA', 'TRANSPOR', 'NC', 'LOCATION') AND YEAR(x.TADT) >= YEAR(GETDATE()) - 1
	GROUP BY x.REF, x.SREF1, x.SREF2, a.DES, a.VENUN, a.SREFCOD, s.CONF, x.TACOD, x.TADT, x.PA, a.TVAART,x.PPAR
    ) rep
    WHERE conf <> 'Usrd'
    ORDER BY ref ASC;
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

}
