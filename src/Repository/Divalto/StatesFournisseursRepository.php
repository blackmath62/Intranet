<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StatesFournisseursRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    // states fournisseurs basiques
    public function getStatesBasiques($dos, $dd, $df, $fous, $fams, $metier, $tiers): array
    {
        $codeFams = "";
        if ($fams != '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')';
        }
        $codeMetier = "";
        if ($metier != '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }
        $code = "";
        if ($tiers == 'C') {
            if ($fous != '') {
                $code = 'AND ART.TIERS IN (' . $fous . ')';
            }
            $opS = "'C','CD'";
            $opA = "'D','DD'";
        } elseif ($tiers == 'F') {
            if ($fous != '') {
                $code = 'AND MOUV.TIERS IN (' . $fous . ')';
            }
            $opS = "'F','FD'";
            $opA = "'G','GD'";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, famille, ref, sref1, sref2, designation,uv, SUM(qte) AS qte, SUM(montant) AS montant
        FROM (
            SELECT MOUV.TIERS AS tiers, ART.FAM_0001 AS famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, ART.DES AS designation, MOUV.VENUN AS uv,
            CASE
            WHEN MOUV.OP IN ($opS) THEN MOUV.FAQTE
            WHEN MOUV.OP IN ($opA) THEN -1 * MOUV.FAQTE
            END AS qte,
            CASE
            WHEN MOUV.OP IN ($opS) THEN MOUV.MONT - MOUV.REMPIEMT_0004
            WHEN MOUV.OP IN ($opA) THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montant
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = '$tiers' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            GROUP BY tiers,famille, ref, sref1,sref2, designation, uv
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // states fournisseurs sans Fournisseurs
    public function getStatesSansFournisseurs($dos, $dd, $df, $fous, $fams, $metier, $tiers): array
    {
        $code = "";
        if ($tiers == 'C') {
            if ($fous != '') {
                $code = 'AND ART.TIERS IN (' . $fous . ')';
            }
            $opS = "'C','CD'";
            $opA = "'D','DD'";
        } elseif ($tiers == 'F') {
            if ($fous != '') {
                $code = 'AND MOUV.TIERS IN (' . $fous . ')';
            }
            $opS = "'F','FD'";
            $opA = "'G','GD'";
        }
        $codeFams = "";
        if ($fams != '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')';
        }
        $codeMetier = "";
        if ($metier != '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille, ref, sref1, sref2, designation,uv, SUM(qte) AS qte, SUM(montant) AS montant
        FROM (
            SELECT MOUV.TIERS AS tiers, ART.FAM_0001 AS famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, ART.DES AS designation,MOUV.VENUN AS uv,
            CASE
            WHEN MOUV.OP IN ($opS) THEN MOUV.FAQTE
            WHEN MOUV.OP IN ($opA) THEN -1 * MOUV.FAQTE
            END AS qte,
            CASE
            WHEN MOUV.OP IN ($opS) THEN MOUV.MONT - MOUV.REMPIEMT_0004
            WHEN MOUV.OP IN ($opA) THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montant
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = '$tiers' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            GROUP BY famille, ref, sref1,sref2, designation,uv
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // states fournisseurs détaillées
    public function getStatesDetaillees($dos, $dd, $df, $fous, $fams, $metier, $tiers): array
    {
        $code = "";
        if ($tiers == 'C') {
            if ($fous != '') {
                $code = 'AND ART.TIERS IN (' . $fous . ')';
            }
            $opS = "'C','CD'";
            $opA = "'D','DD'";
        } elseif ($tiers == 'F') {
            if ($fous != '') {
                $code = 'AND MOUV.TIERS IN (' . $fous . ')';
            }
            $opS = "'F','FD'";
            $opA = "'G','GD'";
        }
        $codeFams = "";
        if ($fams != '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')';
        }
        $codeMetier = "";
        if ($metier != '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, famille, ref, sref1, sref2, designation,uv, op, dateFacture, facture, qte, montant, codeLiv, tiersLiv,
        CASE
            WHEN codeLiv = '' AND tiersLiv = '' THEN 'Ste Lhermitte Frères'
            WHEN codeLiv <> '' AND tiersLiv = '' THEN  'adresse sur le fournisseur'
            WHEN codeLiv = '' AND tiersLiv <> '' THEN  CONCAT(LTRIM(RTRIM(CLI.NOM)), ', ', LTRIM(RTRIM(CLI.RUE)), ', ', LTRIM(RTRIM(CLI.CPOSTAL)), ' ', LTRIM(RTRIM(CLI.VIL)) )
            WHEN codeLiv <> '' AND tiersLiv <> '' THEN  CONCAT(LTRIM(RTRIM(T1.NOM)), ', ', LTRIM(RTRIM(T1.RUE)), ', ', LTRIM(RTRIM(T1.CPOSTAL)), ' ', LTRIM(RTRIM(T1.VIL)) )
            END AS adresseLivraison
        FROM(
        SELECT tiers,famille, ref, sref1, sref2, designation,uv, op, dateFacture, facture, SUM(qte) AS qte, SUM(montant) AS montant, ENT.ADRCOD_0003 AS codeLiv, ENT.ADRTIERS_0003 AS tiersLiv
        FROM (
            SELECT MOUV.TIERS AS tiers, ART.FAM_0001 AS famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, ART.DES AS designation, MOUV.OP AS op, MOUV.FADT AS dateFacture, MOUV.FANO AS facture,MOUV.VENUN AS uv,
            CASE
            WHEN MOUV.OP IN ($opS) THEN MOUV.FAQTE
            WHEN MOUV.OP IN ($opA) THEN -1 * MOUV.FAQTE
            END AS qte,
            CASE
            WHEN MOUV.OP IN ($opS) THEN MOUV.MONT - MOUV.REMPIEMT_0004
            WHEN MOUV.OP IN ($opA) THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montant
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = '$tiers' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            INNER JOIN ENT ON ENT.DOS = $dos AND ENT.TIERS = tiers AND ENT.PICOD = 4 AND ENT.PINO = facture AND ENT.TICOD = '$tiers'
            GROUP BY tiers,famille, ref, sref1,sref2, designation,uv, op, dateFacture, facture, ENT.ADRTIERS_0003, ENT.ADRCOD_0003)reponse2
            LEFT JOIN T1 ON tiersLiv = T1.TIERS AND $dos = T1.DOS AND  codeLiv = T1.ADRCOD
            LEFT JOIN CLI ON tiersLiv = CLI.TIERS AND $dos = CLI.DOS
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Totaux states par fournisseurs
    public function getTotauxStatesParFournisseurs($dos, $dd, $df, $fous, $fams, $metier, $tiers): array
    {
        $code = "";
        if ($tiers == 'C') {
            if ($fous != '') {
                $code = 'AND ART.TIERS IN (' . $fous . ')';
            }
            $opS = "'C','CD'";
            $opA = "'D','DD'";
            $a = 'D';
            $d = 'CD';
            $ad = 'DD';
            $innerJoinTiers = "INNER JOIN CLI t ON t.DOS = MOUV.DOS AND t.TIERS = MOUV.TIERS";
        } elseif ($tiers == 'F') {
            if ($fous != '') {
                $code = 'AND MOUV.TIERS IN (' . $fous . ')';
            }
            $opS = "'F','FD'";
            $opA = "'G','GD'";
            $a = 'G';
            $d = 'FD';
            $ad = 'GD';
            $innerJoinTiers = "INNER JOIN FOU t ON t.DOS = MOUV.DOS AND t.TIERS = MOUV.TIERS";
        }
        $codeFams = "";
        if ($fams != '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')';
        }
        $codeMetier = "";
        if ($metier != '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, nom,SUM(montantDepot) AS montantDepot, SUM(montantDirect) AS montantDirect, SUM(montant) AS montant
        FROM (
            SELECT MOUV.TIERS AS tiers, t.NOM AS nom, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.OP AS op,
            CASE
                WHEN MOUV.OP IN ($opS) THEN MOUV.MONT - MOUV.REMPIEMT_0004
                WHEN MOUV.OP IN ($opA) THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montant,
            CASE
                WHEN MOUV.OP IN ('$tiers') THEN MOUV.MONT - MOUV.REMPIEMT_0004
                WHEN MOUV.OP IN ('$a') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montantDepot,
            CASE
                WHEN MOUV.OP IN ('$d') THEN MOUV.MONT - MOUV.REMPIEMT_0004
                WHEN MOUV.OP IN ('$ad') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montantDirect
            FROM MOUV
            $innerJoinTiers
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = '$tiers' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            GROUP BY tiers, nom
            ORDER BY montant DESC
            ";
        //dd($sql);
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Totaux states pour tous les fournisseurs
    public function getTotauxStatesTousFournisseurs($dos, $dd, $df, $fous, $fams, $metier, $tiers): array
    {
        $code = "";
        if ($tiers == 'C') {
            if ($fous != '') {
                $code = 'AND ART.TIERS IN (' . $fous . ')';
            }
            $opS = "'C','CD'";
            $opA = "'D','DD'";
            $a = 'D';
            $d = 'CD';
            $ad = 'DD';
            $innerJoinTiers = "INNER JOIN CLI t ON t.DOS = MOUV.DOS AND t.TIERS = MOUV.TIERS";
        } elseif ($tiers == 'F') {
            if ($fous != '') {
                $code = 'AND MOUV.TIERS IN (' . $fous . ')';
            }
            $opS = "'F','FD'";
            $opA = "'G','GD'";
            $a = 'G';
            $d = 'FD';
            $ad = 'GD';
            $innerJoinTiers = "INNER JOIN FOU t ON t.DOS = MOUV.DOS AND t.TIERS = MOUV.TIERS";
        }
        $codeFams = "";
        if ($fams != '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')';
        }
        $codeMetier = "";
        if ($metier != '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SUM(montantDepot) AS montantDepot, SUM(montantDirect) AS montantDirect, SUM(montant) AS montant
        FROM (
            SELECT MOUV.TIERS AS tiers, t.NOM AS nom, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.OP AS op,
            CASE
                WHEN MOUV.OP IN ($opS) THEN MOUV.MONT - MOUV.REMPIEMT_0004
                WHEN MOUV.OP IN ($opA) THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montant,
            CASE
                WHEN MOUV.OP IN ('$tiers') THEN MOUV.MONT - MOUV.REMPIEMT_0004
                WHEN MOUV.OP IN ('$a') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montantDepot,
            CASE
                WHEN MOUV.OP IN ('$d') THEN MOUV.MONT - MOUV.REMPIEMT_0004
                WHEN MOUV.OP IN ('$ad') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004
            END AS montantDirect
            FROM MOUV
            $innerJoinTiers
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = '$tiers' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            )reponse
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

}
