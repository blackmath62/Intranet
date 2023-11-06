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

}
