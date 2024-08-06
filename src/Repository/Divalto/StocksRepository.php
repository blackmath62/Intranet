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
            Uv, Designation, Stock, SUM(cmdCli) AS cmdCli, SUM(cmdFou) AS cmdFou, (-1 * SUM(cmdCli) + SUM(cmdFou) + Stock) AS total,
            ABS(SUM(cmdCli)) + ABS(SUM(cmdFou)) + ABS(Stock) AS vtl
            FROM( -- last
            SELECT dos, ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation, Stock,
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
            SELECT dos, ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation, Sum(Stock) AS Stock
            FROM( --rep
            SELECT LTRIM(RTRIM(sr.DOS)) AS dos, LTRIM(RTRIM(a.TIERS)) AS Fournisseur, sr.REF AS Ref, sr.SREF1 AS Sref1, sr.SREF2 AS Sref2, LTRIM(RTRIM(a.DES)) AS Designation, a.VENUN AS Uv,
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
            GROUP BY dos, ArtFerme, Fournisseur, Ref, Sref1, Sref2, Designation, Uv )rep
            LEFT JOIN MOUV m ON Ref = m.REF AND dos = m.DOS AND Sref1 = m.SREF1 AND Sref2 = m.SREF2 AND 1 = m.CDCE4 AND m.PICOD = 2 AND m.CDDT >= '2015-01-01' $referenceLast $designationLast)ad
            GROUP BY dos, ArtFerme,Fournisseur, Ref, Sref1, Sref2,Uv, Designation, Stock)last
            WHERE ($commande)
            ORDER BY ArtFerme DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getStockjN($ref)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT
        m.REF AS ref,
        m.SREF1 AS sref1,
        m.SREF2 AS sref2,
        a.VENUN AS uv,
        m.OP AS op,
        CASE
            WHEN SUM(stock.QTETJSENSTOCK) > 0 AND m.OP IN ('F') THEN MAX(m.PUB) + (-1 * m.REMPIEMT_0004)
            ELSE (SELECT MAX(t.PA) FROM TFO t WHERE t.DOS = m.DOS AND t.REF = m.REF AND t.TADT = (
                SELECT MAX(t2.TADT) FROM TFO t2 WHERE t2.DOS = m.DOS AND t2.REF = m.REF))
        END AS prix,
        CASE
            WHEN SUM(stock.QTETJSENSTOCK) > 0 AND m.OP IN ('F') THEN MAX(m.FADT)
            ELSE (SELECT MAX(t.TADT) FROM TFO t WHERE t.DOS = m.DOS AND t.REF = m.REF AND t.TADT = (
                SELECT MAX(t2.TADT) FROM TFO t2 WHERE t2.DOS = m.DOS AND t2.REF = m.REF))
        END AS dateTarif,
        CASE
            WHEN SUM(stock.QTETJSENSTOCK) > 0 AND m.OP IN ('F') THEN (SELECT m2.FANO FROM MOUV m2 WHERE m2.DOS = m.DOS AND m2.REF = m.REF AND m2.SREF1 = m.SREF1 AND m2.SREF2 = m.SREF2 AND m2.PICOD = 4 AND m2.TICOD = 'F' AND m2.FADT = (
                SELECT MAX(m3.FADT) FROM MOUV m3 WHERE m3.DOS = m2.DOS AND m3.REF = m2.REF AND m3.SREF1 = m2.SREF1 AND m3.SREF2 = m2.SREF2 AND m3.PICOD = 4 AND m3.TICOD = 'F' AND m3.OP IN ('F')))
            ELSE 0
        END AS numero_piece,
        SUM(stock.QTETJSENSTOCK) AS stock,
        MAX(
            CASE
                WHEN a.SREFCOD = 1 AND a.HSDT > '1984-10-19' THEN 'Fermé'
                ELSE ''
            END
        ) AS ferme,
        CASE
            WHEN SUM(stock.QTETJSENSTOCK) > 0 AND m.OP IN ('F') THEN m.PPAR
            ELSE (SELECT t.PPAR FROM TFO t WHERE t.DOS = m.DOS AND t.REF = m.REF AND t.TADT = (
                SELECT MAX(t2.TADT) FROM TFO t2 WHERE t2.DOS = m.DOS AND t2.REF = m.REF))
        END AS ppar
    FROM
        MOUV m
    LEFT JOIN SART s ON m.DOS = s.DOS AND m.REF = s.REF AND m.SREF1 = s.SREF1 AND m.SREF2 = s.SREF2
    LEFT JOIN MVTL_STOCK_V stock ON m.DOS = stock.DOSSIER AND m.REF = stock.REFERENCE AND s.SREF1 = stock.SREFERENCE1 AND s.SREF2 = stock.SREFERENCE2
    INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
    WHERE
        m.DOS = 1
        AND m.REF = '$ref'
        AND m.PICOD = 4
        AND m.TICOD = 'F'
        AND m.FADT = (
            SELECT MAX(m2.FADT)
            FROM MOUV m2
            WHERE
                m2.DOS = m.DOS
                AND m2.REF = m.REF
                AND m2.SREF1 = m.SREF1
                AND m2.SREF2 = m.SREF2
                AND m2.PICOD = 4
                AND m2.TICOD = 'F'
                AND m2.OP IN ('F')
        )
    GROUP BY m.DOS, m.REF, m.SREF1, m.SREF2,a.VENUN, m.FADT, m.REMPIEMT_0004, m.PPAR, m.OP
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

}
