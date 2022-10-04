<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use DoctrineExtensions\Query\Mysql\Year;

class StatesFournisseursRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    // states fournisseurs basiques
    public function getStatesBasiques($dos, $dd, $df, $fous, $fams, $metier):array
    {        
        $code = "";        
        if ($fous <> '') {
            $code = 'AND MOUV.TIERS IN (' . $fous . ')' ;
        }
        $codeFams = "";        
        if ($fams <> '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')' ;
        }
        $codeMetier = "";
        if ($metier <> '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, famille, ref, sref1, sref2, designation, SUM(qte) AS qte, SUM(montant) AS montant 
        FROM (
            SELECT MOUV.TIERS AS tiers, ART.FAM_0001 AS famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, ART.DES AS designation,
            CASE
            WHEN MOUV.OP IN ('F','FD') THEN MOUV.FAQTE 
            WHEN MOUV.OP IN ('G','GD') THEN -1 * MOUV.FAQTE 
            END AS qte,
            CASE
            WHEN MOUV.OP IN ('F','FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
            WHEN MOUV.OP IN ('G','GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montant
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            GROUP BY tiers,famille, ref, sref1,sref2, designation
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // states fournisseurs sans Fournisseurs
    public function getStatesSansFournisseurs($dos, $dd, $df, $fous, $fams, $metier):array
    {        
        $code = "";        
        if ($fous <> '') {
            $code = 'AND MOUV.TIERS IN (' . $fous . ')' ;
        }
        $codeFams = "";        
        if ($fams <> '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')' ;
        }
        $codeMetier = "";
        if ($metier <> '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille, ref, sref1, sref2, designation, SUM(qte) AS qte, SUM(montant) AS montant 
        FROM (
            SELECT MOUV.TIERS AS tiers, ART.FAM_0001 AS famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, ART.DES AS designation,
            CASE
            WHEN MOUV.OP IN ('F','FD') THEN MOUV.FAQTE 
            WHEN MOUV.OP IN ('G','GD') THEN -1 * MOUV.FAQTE 
            END AS qte,
            CASE
            WHEN MOUV.OP IN ('F','FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
            WHEN MOUV.OP IN ('G','GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montant
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            GROUP BY famille, ref, sref1,sref2, designation
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // states fournisseurs détaillées
    public function getStatesDetaillees($dos, $dd, $df, $fous, $fams, $metier):array
    {        
        $code = ""; 
        if ($fous <> '') {
            $code = 'AND MOUV.TIERS IN (' . $fous . ')' ;
        }
        $codeFams = "";        
        if ($fams <> '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')' ;
        }
        $codeMetier = "";
        if ($metier <> '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, famille, ref, sref1, sref2, designation, op, dateFacture, facture, qte, montant, codeLiv, tiersLiv,
        CASE
            WHEN codeLiv = '' AND tiersLiv = '' THEN 'Ste Lhermitte Frères'
            WHEN codeLiv <> '' AND tiersLiv = '' THEN  'adresse sur le fournisseur'
            WHEN codeLiv = '' AND tiersLiv <> '' THEN  CONCAT(LTRIM(RTRIM(CLI.NOM)), ', ', LTRIM(RTRIM(CLI.RUE)), ', ', LTRIM(RTRIM(CLI.CPOSTAL)), ' ', LTRIM(RTRIM(CLI.VIL)) )
            WHEN codeLiv <> '' AND tiersLiv <> '' THEN  CONCAT(LTRIM(RTRIM(T1.NOM)), ', ', LTRIM(RTRIM(T1.RUE)), ', ', LTRIM(RTRIM(T1.CPOSTAL)), ' ', LTRIM(RTRIM(T1.VIL)) )
            END AS adresseLivraison 
        FROM(
        SELECT tiers,famille, ref, sref1, sref2, designation, op, dateFacture, facture, SUM(qte) AS qte, SUM(montant) AS montant, ENT.ADRCOD_0003 AS codeLiv, ENT.ADRTIERS_0003 AS tiersLiv
        FROM (
            SELECT MOUV.TIERS AS tiers, ART.FAM_0001 AS famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, ART.DES AS designation, MOUV.OP AS op, MOUV.FADT AS dateFacture, MOUV.FANO AS facture,
            CASE
            WHEN MOUV.OP IN ('F','FD') THEN MOUV.FAQTE 
            WHEN MOUV.OP IN ('G','GD') THEN -1 * MOUV.FAQTE 
            END AS qte,
            CASE
            WHEN MOUV.OP IN ('F','FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
            WHEN MOUV.OP IN ('G','GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montant
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            INNER JOIN ENT ON ENT.DOS = $dos AND ENT.TIERS = tiers AND ENT.PICOD = 4 AND ENT.PINO = facture AND ENT.TICOD = 'F'
            GROUP BY tiers,famille, ref, sref1,sref2, designation, op, dateFacture, facture, ENT.ADRTIERS_0003, ENT.ADRCOD_0003)reponse2
            LEFT JOIN T1 ON tiersLiv = T1.TIERS AND $dos = T1.DOS AND  codeLiv = T1.ADRCOD
            LEFT JOIN CLI ON tiersLiv = CLI.TIERS AND $dos = CLI.DOS
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Totaux states par fournisseurs
    public function getTotauxStatesParFournisseurs($dos, $dd, $df, $fous, $fams, $metier):array
    {        
        $code = ""; 
        if ($fous <> '') {
            $code = 'AND MOUV.TIERS IN (' . $fous . ')' ;
        }
        $codeFams = "";        
        if ($fams <> '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')' ;
        }
        $codeMetier = "";
        if ($metier <> '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, nom,SUM(montantDepot) AS montantDepot, SUM(montantDirect) AS montantDirect, SUM(montant) AS montant
        FROM (
            SELECT MOUV.TIERS AS tiers, FOU.NOM AS nom, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.OP AS op,
            CASE
                WHEN MOUV.OP IN ('F','FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
                WHEN MOUV.OP IN ('G','GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montant,
            CASE
                WHEN MOUV.OP IN ('F') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
                WHEN MOUV.OP IN ('G') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montantDepot,
            CASE
                WHEN MOUV.OP IN ('FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
                WHEN MOUV.OP IN ('GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montantDirect
            FROM MOUV
            INNER JOIN FOU ON FOU.DOS = MOUV.DOS AND FOU.TIERS = MOUV.TIERS
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            ) reponse
            GROUP BY tiers, nom
            ORDER BY montant DESC
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Totaux states pour tous les fournisseurs
    public function getTotauxStatesTousFournisseurs($dos, $dd, $df, $fous, $fams, $metier):array
    {        
        $code = ""; 
        if ($fous <> '') {
            $code = 'AND MOUV.TIERS IN (' . $fous . ')' ;
        }
        $codeFams = "";        
        if ($fams <> '') {
            $codeFams = 'AND ART.FAM_0001 IN (' . $fams . ')' ;
        }
        $codeMetier = "";
        if ($metier <> '') {
            $codeMetier = 'AND ART.FAM_0002 IN (' . $metier . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SUM(montantDepot) AS montantDepot, SUM(montantDirect) AS montantDirect, SUM(montant) AS montant
        FROM (
            SELECT MOUV.TIERS AS tiers, FOU.NOM AS nom, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.OP AS op,
            CASE
                WHEN MOUV.OP IN ('F','FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
                WHEN MOUV.OP IN ('G','GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montant,
            CASE
                WHEN MOUV.OP IN ('F') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
                WHEN MOUV.OP IN ('G') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montantDepot,
            CASE
                WHEN MOUV.OP IN ('FD') THEN MOUV.MONT - MOUV.REMPIEMT_0004 
                WHEN MOUV.OP IN ('GD') THEN (-1 * MOUV.MONT) + MOUV.REMPIEMT_0004 
            END AS montantDirect
            FROM MOUV
            INNER JOIN  FOU ON FOU.DOS = MOUV.DOS AND FOU.TIERS = MOUV.TIERS
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            WHERE MOUV.DOS = $dos AND MOUV.TICOD = 'F' AND MOUV.PICOD = 4 AND MOUV.FADT BETWEEN '$dd' AND '$df' $code $codeFams $codeMetier
            )reponse
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

}
