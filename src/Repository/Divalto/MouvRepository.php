<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mouv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mouv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mouv[]    findAll()
 * @method Mouv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);

    }

    // Liste des piéces avec des produits FSC
    public function getFscOrderList($listpieceOk): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(ENT.PIREF)) AS notreRef, tiers AS tiers, codePiece AS codePiece, numCmd AS numCmd, dateCmd AS dateCmd, numBl AS numBl, dateBl AS dateBl, numFact AS numFact, dateFact AS dateFact, utilisateur AS utilisateur
        FROM(
        SELECT DISTINCT MOUV.DOS AS dos, RTRIM(LTRIM(MOUV.TIERS)) AS tiers, RTRIM(LTRIM(MOUV.PICOD)) AS codePiece,
        RTRIM(LTRIM(MOUV.CDNO)) AS numCmd,MOUV.CDDT AS dateCmd,RTRIM(LTRIM(MOUV.BLNO)) AS numBl, MOUV.BLDT AS dateBl,
        RTRIM(LTRIM(MOUV.FANO)) AS numFact, MOUV.FADT AS dateFact,
        CASE
        WHEN MOUV.PICOD = 2 THEN  MOUV.CDNO
        WHEN MOUV.PICOD = 3 THEN  MOUV.BLNO
        WHEN MOUV.PICOD = 4 THEN  MOUV.FANO
        END AS numPiece,
        CASE
        WHEN MOUV.TIERS IS NULL THEN 'MARINA'
        ELSE 'MARINA'
        END AS utilisateur
        FROM MOUV
        WHERE MOUV.DOS = 3 AND ( MOUV.REF LIKE 'FSC%' OR MOUV.FANO IN ('19021142','19021427') ) AND MOUV.TICOD IN ('F') AND MOUV.CDNO NOT IN($listpieceOk) AND MOUV.PICOD IN (2,3,4)
        --AND (MOUV.CDDT >= '2021/01/01' OR MOUV.BLDT >= '2021/01/01' OR MOUV.FADT >= '2021/01/01')
        )reponse
        INNER JOIN ENT ON dos = ENT.DOS AND tiers = ENT.TIERS AND ENT.PINO = numPiece
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Liste des piéces avec des produits FSC pour tourner à vide
    public function getFscOrderListRun(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(ENT.PIREF)) AS notreRef, tiers AS tiers, codePiece AS codePiece, numCmd AS numCmd, dateCmd AS dateCmd, numBl AS numBl, dateBl AS dateBl, numFact AS numFact, dateFact AS dateFact, utilisateur AS utilisateur
        FROM(
        SELECT DISTINCT MOUV.DOS AS dos, RTRIM(LTRIM(MOUV.TIERS)) AS tiers, RTRIM(LTRIM(MOUV.PICOD)) AS codePiece,
        RTRIM(LTRIM(MOUV.CDNO)) AS numCmd,MOUV.CDDT AS dateCmd,RTRIM(LTRIM(MOUV.BLNO)) AS numBl, MOUV.BLDT AS dateBl,
        RTRIM(LTRIM(MOUV.FANO)) AS numFact, MOUV.FADT AS dateFact,
        CASE
        WHEN MOUV.PICOD = 2 THEN  MOUV.CDNO
        WHEN MOUV.PICOD = 3 THEN  MOUV.BLNO
        WHEN MOUV.PICOD = 4 THEN  MOUV.FANO
        END AS numPiece,
        CASE
        WHEN MOUV.TIERS IS NULL THEN 'MARINA'
        ELSE 'MARINA'
        END AS utilisateur
        FROM MOUV
        WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.TICOD IN ('F') AND MOUV.PICOD IN (2,3,4)
        --AND (MOUV.CDDT >= '2021/01/01' OR MOUV.BLDT >= '2021/01/01' OR MOUV.FADT >= '2021/01/01')
        )reponse
        INNER JOIN ENT ON dos = ENT.DOS AND tiers = ENT.TIERS AND ENT.PINO = numPiece
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getNotreRef($piece, $typePiece, $tiers)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.PIREF AS notreRef
        FROM ENT
        WHERE ENT.DOS = 3 AND ENT.PINO = $piece AND ENT.TIERS = '$tiers' AND ENT.PICOD = $typePiece
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // Mouvements sur la piéce
    public function getMouvOnOrder($num, $typePiece, $tiers): array
    {
        if ($typePiece == 2) {
            $code = 'MOUV.CDNO';
            $dateP = 'MOUV.CDDT';
        } elseif ($typePiece == 3) {
            $code = 'MOUV.BLNO';
            $dateP = 'MOUV.BLDT';
        } elseif ($typePiece == 4) {
            $code = 'MOUV.FANO';
            $dateP = 'MOUV.FADT';
        }
        $where = $code . ' = ' . $num;
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT $dateP AS datePiece, $code AS num, MOUV.PICOD AS codePiece, MOUV.TIERS AS tiers, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MAX(MOUV.DES) AS designation, MVTL_STOCK_V.SERIELOT AS lot
        FROM MOUV
        LEFT JOIN MVTL_STOCK_V ON MOUV.REF = MVTL_STOCK_V.REFERENCE AND MOUV.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND MOUV.SREF2 = MVTL_STOCK_V.SREFERENCE2
        WHERE MOUV.DOS = 3 AND MOUV.PICOD = $typePiece AND MOUV.TIERS = '$tiers' AND $where
        GROUP BY $dateP, $code, MOUV.PICOD, MOUV.TIERS, MOUV.REF, MOUV.SREF1, MOUV.SREF2, MVTL_STOCK_V.SERIELOT
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Mouvements sur la piéce
    public function getMouvByOrder($num, $tiers)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT RTRIM(LTRIM(MOUV.CDNO)) AS numCmd, RTRIM(LTRIM(MOUV.PICOD)) AS codePiece, MOUV.TIERS AS tiers,RTRIM(LTRIM(MOUV.BLNO)) AS numBl, MOUV.BLDT AS dateBl,RTRIM(LTRIM(MOUV.FANO)) AS numFact, MOUV.FADT AS dateFact
        FROM MOUV
        WHERE MOUV.DOS = 3 AND MOUV.TIERS = '$tiers' AND MOUV.REF LIKE 'FSC%' AND MOUV.CDNO = $num
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

    // Mouvements sur la piéce
    public function getLastMouvCli($dos)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT commercial, tiers AS tiers, nom AS nom, MAX(MOUV.CDDT) AS dernCmd, MAX(MOUV.BLDT) AS dernBl, MAX(MOUV.FADT) AS dernFact
        FROM(
        SELECT CLI.DOS AS dos,VRP.NOM AS commercial, RTRIM(LTRIM(CLI.TIERS)) AS tiers, RTRIM(LTRIM(CLI.NOM)) AS nom
        FROM CLI
        LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND CLI.DOS = VRP.DOS
        WHERE CLI.DOS IN ($dos) AND CLI.HSDT IS NULL)reponse
        LEFT JOIN MOUV ON tiers = MOUV.TIERS AND dos = MOUV.DOS
        GROUP BY commercial,tiers, nom
        ORDER BY tiers
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Vente des contrats commissionnaires
    public function getVenteContratCommissionnaire($articles, $mois, $annee)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT reference, designation, uv,SUM(quantite) AS quantite
        FROM(
        SELECT RTRIM(LTRIM(MOUV.REF)) AS reference, RTRIM(LTRIM(MOUV.DES)) AS designation, RTRIM(LTRIM(MOUV.VENUN)) AS uv, MOUV.OP AS op, MOUV.FAQTE AS qte,
        CASE
            WHEN MOUV.OP IN('C','CD') THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD') THEN -1*MOUV.FAQTE
            ELSE 0
        END AS quantite
        FROM MOUV
        WHERE MOUV.DOS = 1 AND MOUV.REF IN ($articles) AND YEAR(MOUV.FADT) IN ($annee) AND MOUV.PICOD = 4 AND MOUV.TICOD = 'C' AND MONTH(MOUV.FADT) IN ($mois)
        GROUP BY MOUV.REF, MOUV.DES, MOUV.VENUN, MOUV.OP, MOUV.FAQTE)reponse
        GROUP BY reference, designation, uv
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Mouvements FSC
    public function getMovFsc()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT REPLACE(CONCAT(Ref,Sref1,Sref2,Nature),'/','-') AS Lien, Ref,Sref1,Sref2,Designation AS Design,Nature
        ,
        SUM(Fournisseur) AS Fourn, SUM(Client) AS Cli, SUM(Interne) AS Inter,SUM(Fournisseur)+SUM(Client)+SUM(Interne) AS Diff
        FROM(
        SELECT MOUV.DOS AS Dos, MOUV.TIERS AS Tiers, MOUV.TICOD AS TypePiece, RTRIM(LTRIM(MOUV.REF)) AS Ref, RTRIM(LTRIM(MOUV.SREF1)) AS Sref1, RTRIM(LTRIM(MOUV.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation,
        CASE
        WHEN MOUV.TICOD = 'F' AND MOUV.OP IN ('F','FD') THEN MOUV.FAQTE
        WHEN MOUV.TICOD = 'F' AND MOUV.OP IN ('G', 'GD') THEN -1 * MOUV.FAQTE
        ELSE 0
        END AS Fournisseur,
        CASE
        WHEN MOUV.TICOD = 'C' AND MOUV.OP IN ('C','CD') THEN -1 * MOUV.FAQTE
        WHEN MOUV.TICOD = 'C' AND MOUV.OP IN ('D','DD') THEN MOUV.FAQTE
        ELSE 0
        END AS Client,
        CASE
        WHEN MOUV.TICOD = 'I' AND MOUV.OP IN('JI','JID') THEN MOUV.BLQTE
        WHEN MOUV.TICOD = 'I' AND MOUV.OP IN('II', 'IID') THEN -1 * MOUV.BLQTE
        ELSE 0
        END AS Interne,
        CASE
        WHEN MOUV.OP IN('JID', 'IID', 'CD','FD','DD','GD') THEN 'Direct'
        WHEN MOUV.OP IN('JI', 'II', 'C','F','D','G') THEN 'Dépôt'
        END AS Nature,
        CASE
        WHEN MOUV.TICOD IN('F','C') THEN MOUV.FANO
        WHEN MOUV.TICOD IN('I') THEN MOUV.BLNO
        END AS NumPiece,
        ENT.PIREF AS NotreRef
        FROM MOUV
        INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
        LEFT JOIN ENT ON ENT.DOS = MOUV.DOS AND ENT.TIERS = MOUV.TIERS AND ENT.PICOD = MOUV.PICOD AND ENT.PINO = MOUV.BLNO
        WHERE MOUV.DOS = 3 AND LEFT(MOUV.REF,3) = 'FSC' AND MOUV.PICOD IN (3,4))REPONSE
        GROUP BY Ref,Sref1,Sref2, Designation, Nature
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Mouvements FSC pour un Article
    public function getMovFscOneArt($lien)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT REPLACE(CONCAT(Ref,Sref1,Sref2,Nature),'/','-') AS Lien, Ref,Sref1,Sref2,Designation AS Design,Nature
            ,
            SUM(Fournisseur) AS Fourn, SUM(Client) AS Cli, SUM(Interne) AS Inter,SUM(Fournisseur)+SUM(Client)+SUM(Interne) AS Diff
            FROM(
                SELECT MOUV.DOS AS Dos, MOUV.TIERS AS Tiers, MOUV.TICOD AS TypePiece, RTRIM(LTRIM(MOUV.REF)) AS Ref, RTRIM(LTRIM(MOUV.SREF1)) AS Sref1, RTRIM(LTRIM(MOUV.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation,
                CASE
                WHEN MOUV.TICOD = 'F' AND MOUV.OP IN ('F','FD') THEN MOUV.FAQTE
                WHEN MOUV.TICOD = 'F' AND MOUV.OP IN ('G', 'GD') THEN -1 * MOUV.FAQTE
                ELSE 0
                END AS Fournisseur,
                CASE
                WHEN MOUV.TICOD = 'C' AND MOUV.OP IN ('C','CD') THEN -1 * MOUV.FAQTE
                WHEN MOUV.TICOD = 'C' AND MOUV.OP IN ('D','DD') THEN MOUV.FAQTE
                ELSE 0
                END AS Client,
                CASE
                WHEN MOUV.TICOD = 'I' AND MOUV.OP IN('JI','JID') THEN MOUV.BLQTE
                WHEN MOUV.TICOD = 'I' AND MOUV.OP IN('II', 'IID') THEN -1 * MOUV.BLQTE
                ELSE 0
                END AS Interne,
                CASE
                WHEN MOUV.OP IN('JID', 'IID', 'CD','FD','DD','GD') THEN 'Direct'
                WHEN MOUV.OP IN('JI', 'II', 'C','F','D','G') THEN 'Dépôt'
                END AS Nature,
                CASE
                WHEN MOUV.TICOD IN('F','C') THEN MOUV.FANO
                WHEN MOUV.TICOD IN('I') THEN MOUV.BLNO
                END AS NumPiece,
                ENT.PIREF AS NotreRef
                FROM MOUV
                INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
                LEFT JOIN ENT ON ENT.DOS = MOUV.DOS AND ENT.TIERS = MOUV.TIERS AND ENT.PICOD = MOUV.PICOD AND ENT.PINO = MOUV.BLNO
            WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.PICOD IN (3,4))REPONSE
            WHERE REPLACE(CONCAT(Ref,Sref1,Sref2,Nature),'/','-') = '$lien'
            GROUP BY Ref,Sref1,Sref2, Designation, Nature
            ORDER BY Ref
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

    // Détails Mouvements FSC par article
    public function getDetailArtMovFsc($lien)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM (
        SELECT CONCAT(Lien,Nature) AS Lien, Dos AS Dos, Tiers AS Tiers,TypePiece AS TypePiece,
        Ref AS Ref,Sref1 AS Sref1, Sref2 AS Sref2, Designation AS Designation, DatePiece AS DatePiece, NumPiece AS NumPiece,
        Nature AS Nature, Fournisseur AS Fournisseur, Client AS Client, Interne AS Interne, NotreRef AS NotreRef FROM(
            SELECT REPLACE(CONCAT(RTRIM(LTRIM(MOUV.REF)),RTRIM(LTRIM(MOUV.SREF1)),RTRIM(LTRIM(MOUV.SREF2))),'/','-') AS Lien,
             MOUV.DOS AS Dos, MOUV.TIERS AS Tiers, MOUV.TICOD AS TypePiece, RTRIM(LTRIM(MOUV.REF)) AS Ref, RTRIM(LTRIM(MOUV.SREF1)) AS Sref1,
              RTRIM(LTRIM(MOUV.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation,
            CASE
            WHEN MOUV.TICOD = 'F' AND MOUV.OP IN ('F','FD') THEN MOUV.FAQTE
            WHEN MOUV.TICOD = 'F' AND MOUV.OP IN ('G', 'GD') THEN -1 * MOUV.FAQTE
            ELSE 0
            END AS Fournisseur,
            CASE
            WHEN MOUV.TICOD = 'C' AND MOUV.OP IN ('C','CD') THEN -1 * MOUV.FAQTE
            WHEN MOUV.TICOD = 'C' AND MOUV.OP IN ('D','DD') THEN MOUV.FAQTE
            ELSE 0
            END AS Client,
            CASE
            WHEN MOUV.TICOD = 'I' AND MOUV.OP IN('JI','JID') THEN MOUV.BLQTE
            WHEN MOUV.TICOD = 'I' AND MOUV.OP IN('II', 'IID') THEN -1 * MOUV.BLQTE
            ELSE 0
            END AS Interne,
            CASE
            WHEN MOUV.OP IN('JID', 'IID', 'CD','FD','DD','GD') THEN 'Direct'
            WHEN MOUV.OP IN('JI', 'II', 'C','F','D','G') THEN 'Dépôt'
            END AS Nature,
            CASE
            WHEN MOUV.TICOD IN('F','C') THEN MOUV.FANO
            WHEN MOUV.TICOD IN('I') THEN MOUV.BLNO
            END AS NumPiece,
            CASE
            WHEN MOUV.TICOD IN('F','C') THEN MOUV.FADT
            WHEN MOUV.TICOD IN('I') THEN MOUV.BLDT
            END AS DatePiece,
            ENT.PIREF AS NotreRef
            FROM MOUV
            INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
            LEFT JOIN ENT ON ENT.DOS = MOUV.DOS AND ENT.TIERS = MOUV.TIERS AND ENT.PICOD = MOUV.PICOD AND (ENT.PINO = MOUV.BLNO OR ENT.PINO = MOUV.FANO)
            WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.PICOD IN (3,4))reponse)rep
        WHERE Lien = '$lien'
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// ramener le dernier mouvement avec Type Piéce, numéro piéce et date piéce sur un article FSC
    public function getMaxPiece($lien)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT TOP 1 * FROM(
        SELECT CONCAT(Lien,Nature) AS Lien, TypePiece AS TypePiece, MAX(DatePiece) AS MaxDate, MAX(NumPiece) AS NumPiece, Nature AS Nature FROM(
        SELECT REPLACE(CONCAT(RTRIM(LTRIM(MOUV.REF)),RTRIM(LTRIM(MOUV.SREF1)),RTRIM(LTRIM(MOUV.SREF2))),'/','-') AS Lien,
        MOUV.TICOD AS TypePiece,
        CASE
        WHEN MOUV.TICOD IN('F','C') THEN MOUV.FANO
        WHEN MOUV.TICOD IN('I') THEN MOUV.BLNO
        END AS NumPiece,
        CASE
        WHEN MOUV.OP IN('JID', 'IID', 'CD','FD','DD','GD') THEN 'Direct'
        WHEN MOUV.OP IN('JI', 'II', 'C','F','D','G') THEN 'Dépôt'
        END AS Nature,
        CASE
        WHEN MOUV.TICOD IN('F','C') THEN MOUV.FADT
        WHEN MOUV.TICOD IN('I') THEN MOUV.BLDT
        END AS DatePiece
        FROM MOUV
        INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
        WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.PICOD IN (3,4))reponse
        GROUP BY Lien,TypePiece, Nature
        )repo
    WHERE Lien = '$lien'
    ORDER BY MaxDate,NumPiece DESC
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

// ramener les produits en liens avec des factures FSC
    public function getListeProduits($listFactures)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CONCAT(Lien,Nature) AS Lien,TypePiece, Factures, Nature FROM(
        SELECT REPLACE(CONCAT(RTRIM(LTRIM(MOUV.REF)),RTRIM(LTRIM(MOUV.SREF1)),RTRIM(LTRIM(MOUV.SREF2))),'/','-') AS Lien, MOUV.PICOD AS PieceCode,
        MOUV.TICOD AS TypePiece, MOUV.FANO AS Factures,
        CASE
        WHEN MOUV.OP IN('JID', 'IID', 'CD','FD','DD','GD') THEN 'Direct'
        WHEN MOUV.OP IN('JI', 'II', 'C','F','D','G') THEN 'Dépôt'
        END AS Nature
        FROM MOUV
        INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
        WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.PICOD IN (3,4))reponse
    WHERE Factures IN($listFactures) AND TypePiece = 'F' AND PieceCode = 4
    GROUP BY Lien, TypePiece, Factures, Nature
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// Vérifier que les bons codes articles sont utilisés
    public function getCheckCodeAndDesArticles()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT REPLACE(CONCAT(TypePiece,TypeTiers, NumPiece, Ref, Sref1, Sref2),'/','-') AS Identification, TypePiece as TypePiece, TypeTiers as TypeTiers,
    Ref AS Ref, Sref1 AS Sref1, Sref2 AS Sref2, DesignationPiece AS DesignationPiece, DesignationArticle AS DesignationArticle,
        Probleme AS Probleme, NumPiece AS NumPiece, Utilisateur AS Utilisateur, MUSER.EMAIL AS Email FROM(
        SELECT MOUV.TICOD AS TypeTiers, MOUV.PICOD AS TypePiece, MOUV.REF AS Ref,MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2, MOUV.DES AS DesignationPiece, ART.DES AS DesignationArticle,
        CASE
        WHEN LEFT(MOUV.REF,7) = 'FSC100%' AND (NOT MOUV.DES LIKE '%FSC%' OR NOT MOUV.DES LIKE '%100%') THEN 'La référence produit contient FSC100% et pas la désignation sur la piéce, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,7) <> 'FSC100%' AND MOUV.DES LIKE '%FSC%' AND MOUV.DES LIKE '%100%' THEN 'La référence produit ne contient pas FSC100% mais la désignation sur la piéce oui, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,3) <> 'FSC' AND MOUV.DES LIKE '%FSC%' THEN 'La référence produit ne contient pas FSC mais la désignation sur la piéce oui, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,12) = 'FSCMIXCREDIT' AND (NOT MOUV.DES LIKE '%FSC%' OR NOT MOUV.DES LIKE '%MIX%' OR NOT MOUV.DES LIKE '%CREDIT%') THEN 'La référence produit contient FSCMIXCREDIT et pas la désignation sur la piéce, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,12) <> 'FSCMIXCREDIT' AND MOUV.DES LIKE '%FSC MIX CREDIT%' THEN 'La référence produit ne contient pas FSCMIXCREDIT mais la désignation sur la piéce oui, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,12) <> 'FSCMIXCREDIT' AND MOUV.DES LIKE '%MIX CREDIT%' THEN 'La référence produit ne contient pas FSCMIXCREDIT mais la désignation sur la piéce oui, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,6) = 'FSCMIX' AND NOT MOUV.DES LIKE '%FSC MIX%' THEN 'La référence produit contient FSCMIX et pas la désignation sur la piéce, merci d utiliser le bon produit'
        WHEN LEFT(MOUV.REF,6) <> 'FSCMIX' AND MOUV.DES LIKE '%FSC MIX%' THEN 'La référence produit ne contient pas FSCMIX mais la désignation sur la piéce oui, merci d utiliser le bon produit'
        END AS Probleme,
        CASE
        WHEN MOUV.PICOD = 2 THEN MOUV.CDNO
        WHEN MOUV.PICOD = 3 THEN MOUV.BLNO
        END AS NumPiece,
        CASE
        WHEN MOUV.USERMO = '' THEN MOUV.USERCR
        ELSE MOUV.USERCR
        END AS Utilisateur
        FROM MOUV
        INNER JOIN ART ON ART.DOS = MOUV.DOS AND ART.REF = MOUV.REF
        WHERE MOUV.DOS = 3 AND MOUV.DES <> ART.DES AND (MOUV.CDCE4 = '1' OR MOUV.BLCE4 = '1') AND MOUV.TICOD IN ('F','C')
        AND (YEAR(MOUV.CDDT) >= 2021 OR YEAR(MOUV.BLDT) >= 2021))Reponse
        INNER JOIN MUSER ON Utilisateur = MUSER.USERX
        WHERE NOT Probleme IS NULL
        ORDER BY Probleme DESC
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// ramener le Détail d'une facture client FSC
    public function getDetailFactureFscClient($facture): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.TIERS AS tiers, m.REF AS ref, m.SREF1 AS sref1, m.SREF2 AS sref2, a.DES AS designation, m.OP AS op, m.QTE AS qte, m.SERIE AS lot
    FROM MVTL m
    INNER JOIN ART a ON a.REF = m.REF AND a.DOS = m.DOS
    WHERE m.DOS = 3 AND m.PINO = $facture AND m.TICOD = 'C'
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// ramener les BLs Directs
    public function getListBlDirect(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CONCAT(tiers,'BL',numeroBl) AS Identification, tiers, numeroBl, dateBl, Utilisateur, MUSER.EMAIL AS Email FROM(
        SELECT LTRIM(RTRIM(MOUV.TIERS)) AS tiers, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.DES AS designation, LTRIM(RTRIM(MOUV.BLNO)) AS numeroBl, MOUV.BLDT AS dateBl,
        CASE
        WHEN MOUV.USERMO = '' THEN MOUV.USERCR
        ELSE MOUV.USERCR
        END AS Utilisateur
        FROM MOUV
        WHERE MOUV.BLCE4 = '1' AND MOUV.OP IN ('CD', 'DD', 'FD', 'GD') AND MOUV.BLDT > '2022-05-05')reponse
        INNER JOIN MUSER ON Utilisateur = MUSER.USERX
        GROUP BY tiers, numeroBl,dateBl,Utilisateur,MUSER.EMAIL
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// Update Mouvements/entête conduite de travaux
    public function getUpdateMouvConduiteTravaux($cmd, $bl, $facture, $termine): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MAX(dos) AS dos, MAX(tiers) AS tiers, MAX(nom) AS nom, MAX(typePiece) AS typePiece,
    MAX(dateCmd) AS dateCmd, MAX(numCmd) AS numCmd, MAX(dateBl) AS dateBl, MAX(numBl) AS numBl, MAX(dateFacture) AS dateFacture, MAX(numFacture) AS numFacture,
    MAX(ENT.DELDEMDT) AS delaiDemande, MAX(ENT.DELACCDT) AS delaiAccepte, MAX(ENT.DELREPDT) AS delaiReporte,
    MAX(ENT.BLMOD) AS transport, MAX(ENT.PROJET) AS affaire, MAX(ENT.OP) AS op, ENT.ENT_ID AS id, MAX(utilisateur) AS utilisateur,
    CASE
    WHEN MAX(ENT.ADRCOD_0003) = '' THEN MAX(adressePrincipale)
    ELSE MAX(CONCAT(LTRIM(RTRIM(T1.NOM)), ', ', LTRIM(RTRIM(T1.RUE)), ', ', LTRIM(RTRIM(T1.CPOSTAL)), ' ', LTRIM(RTRIM(T1.VIL)) ) )
    END AS adresseLivraison
    FROM(
    SELECT MOUV.DOS AS dos, MOUV.TIERS AS tiers, CLI.NOM AS nom, MOUV.PICOD as typePiece,
    MOUV.CDDT AS dateCmd, MOUV.CDNO AS numCmd, MOUV.BLDT AS dateBl, MOUV.BLNO AS numBl, MOUV.FADT AS dateFacture, MOUV.FANO AS numFacture,
    CONCAT(LTRIM(RTRIM(CLI.RUE)), ', ', LTRIM(RTRIM(CLI.CPOSTAL)), ' ', LTRIM(RTRIM(CLI.VIL)) ) AS adressePrincipale, MAX(LTRIM(RTRIM(MUSER.NOM))) AS utilisateur,
    CASE
    WHEN MOUV.PICOD = 4 THEN MOUV.FANO
    WHEN MOUV.PICOD = 3 THEN MOUV.BLNO
    WHEN MOUV.PICOD = 2 THEN MOUV.CDNO
    END AS numPiece,
    CASE
    WHEN MOUV.PICOD = 4 THEN MOUV.FADT
    WHEN MOUV.PICOD = 3 THEN MOUV.BLDT
    WHEN MOUV.PICOD = 2 THEN MOUV.CDDT
    END AS datePiece
    FROM MOUV
    INNER JOIN CLI ON MOUV.DOS = CLI.DOS AND MOUV.TIERS = CLI.TIERS
    INNER JOIN ART ON MOUV.DOS = ART.DOS AND MOUV.REF = ART.REF
	INNER JOIN MUSER ON MOUV.DOS = MUSER.DOS AND MOUV.USERCR = MUSER.USERX
    WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'C' AND ART.FAM_0002 IN ('ME','MO') AND MOUV.PICOD IN (2,3,4) AND MOUV.TIERS <> ('C0160500')
    GROUP BY MOUV.DOS, MOUV.TIERS, CLI.NOM, MOUV.PICOD, MOUV.CDDT, MOUV.CDNO, MOUV.BLDT, MOUV.BLNO, MOUV.FADT, MOUV.FANO, CLI.RUE, CLI.CPOSTAL, CLI.VIL)reponse
    INNER JOIN ENT ON ENT.DOS = dos AND ENT.TIERS = tiers AND ENT.PICOD = typePiece AND ENT.PINO = numPiece AND ENT.TICOD = 'C'
    LEFT JOIN T1 ON tiers = T1.TIERS AND dos = T1.DOS AND  ENT.ADRCOD_0003 = T1.ADRCOD
    WHERE (datePiece >= '2022-06-01' AND ENT.PROJET <> '' AND NOT numFacture IN ($termine)) OR ( numCmd IN($cmd) OR numBl IN ($bl) OR numFacture IN ($facture) )
    GROUP BY ENT.ENT_ID
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// détail des articles de la piéce conduite de travaux
    public function getDetailArticleConduiteTravaux($id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT dos, tiers, ref,sref1,sref2,designation,qte,num,type
    FROM(
    SELECT MOUV.DOS AS dos,MOUV.TIERS AS tiers, MOUV.PICOD AS type, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.DES AS designation,
    CASE
    WHEN MOUV.PICOD = 2 THEN MOUV.CDQTE
    WHEN MOUV.PICOD = 3 THEN MOUV.BLQTE
    WHEN MOUV.PICOD = 4 THEN MOUV.FAQTE
    END AS qte
    ,
    CASE
    WHEN MOUV.PICOD = 2 THEN MOUV.CDNO
    WHEN MOUV.PICOD = 3 THEN MOUV.BLNO
    WHEN MOUV.PICOD = 4 THEN MOUV.FANO
    END AS num
    FROM MOUV
    WHERE MOUV.DOS = 1)reponse
    INNER JOIN ENT ON dos = ENT.DOS AND tiers = ENT.TIERS AND num = ENT.PINO AND ENT.TICOD = 'C' AND ENT.PICOD = type AND ENT.ENT_ID = $id
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// achat en cours liés à l'affaire ->  conduite de travaux
    public function getAchatLieAffaireConduitetravaux($affaire): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MOUV.TIERS AS tiers, FOU.NOM AS nom, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.DES AS designation,
    CASE
    WHEN MOUV.PICOD = 2 THEN MOUV.CDQTE
    WHEN MOUV.PICOD = 3 THEN MOUV.BLQTE
    WHEN MOUV.PICOD = 4 THEN MOUV.FAQTE
    END AS qte,
    CASE
    WHEN MOUV.PICOD = 2 THEN 'Commande'
    WHEN MOUV.PICOD = 3 THEN 'Bl'
    WHEN MOUV.PICOD = 4 THEN 'Facture'
    END AS piece,
    CASE
    WHEN MOUV.PICOD = 2 THEN MOUV.CDNO
    WHEN MOUV.PICOD = 3 THEN MOUV.BLNO
    WHEN MOUV.PICOD = 4 THEN MOUV.FANO
    END AS num,
    CASE
    WHEN MOUV.PICOD = 2 THEN MOUV.CDDT
    WHEN MOUV.PICOD = 3 THEN MOUV.BLDT
    WHEN MOUV.PICOD = 4 THEN MOUV.FADT
    END AS datePiece
    FROM MOUV
    INNER JOIN FOU ON FOU.DOS = MOUV.DOS AND FOU.TIERS = MOUV.TIERS
    WHERE MOUV.DOS = 1 AND MOUV.TICOD = 'F' AND MOUV.PROJET = '$affaire'
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

// Commande et BL de la veille pour les clients feu rouge et orange
    public function getCmdBlClientFeuRougeOrange(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
    FROM(
        SELECT ENT.TIERS AS tiers, CLI.NOM AS nom, ENT.OP AS op,
        CASE
        WHEN CLI.FEU = 1 THEN 'vert'
        WHEN CLI.FEU = 2 THEN 'orange'
        WHEN CLI.FEU = 3 THEN 'rouge'
        END AS feu,
        CASE
        WHEN ENT.PICOD = 2 THEN 'cmd'
        WHEN ENT.PICOD = 3 THEN 'bl'
        END AS typePiece,
        CASE
        WHEN ENT.PICOD IN(2,3) THEN ENT.PINO
        END AS numPiece,
        CASE
        WHEN ENT.PICOD IN(2,3) THEN ENT.PIDT
        END AS datePiece,
        CASE
        WHEN ENT.OP IN ('CD','C') THEN ENT.HTPDTMT
        WHEN ENT.OP IN ('DD','D') THEN -1*ENT.HTPDTMT
        END AS montant,
        ENT.USERCR AS userCr, VRP.NOM AS commercial
        FROM ENT
        INNER JOIN CLI ON CLI.DOS = ENT.DOS AND CLI.TIERS = ENT.TIERS
        INNER JOIN VRP ON VRP.TIERS = CLI.REPR_0001 AND VRP.DOS = ENT.DOS
        WHERE ENT.PIDT = DATEADD(day,-1,CAST(GETDATE() as date)) AND ENT.CE4 = 1 )reponse
        WHERE feu <> 'vert'
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getNbeBlEtStockParProduit($dos, $dd, $df, $fermeOuvert, $famille, $stockOuBl): array
    {
        $code = '';
        $codeFamille = "";
        if ($fermeOuvert == 'ouvert') {
            $code = " AND a.HSDT IS NULL";
        }
        // s'il y a des familles de produits, il faut filtrer dessus, sinon afficher toutes les familles produits
        if ($famille) {
            $codeFamille = " AND a.FAM_0001 IN($famille)";
        }
        $conn = $this->getEntityManager()->getConnection();
        if ($stockOuBl == 'bl') {
            $sql = "SELECT dos, famille, ref,sref1,sref2, designation, uv,fermeture, nbeBl, s.DEPOT AS depot, s.EMPLACEMENT AS emplacement, SUM(s.QTETJSENSTOCK) AS stock
        FROM(
        SELECT MOUV.DOS AS dos,a.FAM_0001 as famille, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, a.DES AS designation, MOUV.VENUN AS uv, a.HSDT as fermeture, COUNT(MOUV.BLNO) AS nbeBl
        FROM MOUV
        INNER JOIN ART a ON a.REF = MOUV.REF AND a.DOS = MOUV.DOS
        WHERE MOUV.BLDT BETWEEN '$dd' AND '$df' AND MOUV.TICOD = 'C' AND MOUV.OP IN('C','D') AND MOUV.BLNO <> 0 AND MOUV.DOS = $dos $codeFamille
        AND NOT a.FAM_0001 IN ('TRANSPOR', 'TAXE', 'DIVERS') AND NOT a.REF LIKE ('DIVERS20%') $code
        GROUP BY MOUV.DOS, a.FAM_0001, MOUV.REF, MOUV.SREF1, MOUV.SREF2, a.DES, MOUV.VENUN, a.HSDT)reponse
        LEFT JOIN MVTL_STOCK_V s ON ref = s.REFERENCE AND sref1 = s.SREFERENCE1 AND sref2 = s.SREFERENCE2 AND dos = s.DOSSIER
        GROUP BY dos, famille, ref,sref1,sref2, designation, uv, fermeture, nbeBl, s.DEPOT, s.EMPLACEMENT
        ORDER BY nbeBl DESC
        ";
        } else {
            $sql = "SELECT dos,famille,ref,sref1,sref2,designation,uv,fermeture,depot, emplacement, stock, COUNT(m.BLNO) AS nbeBl
            FROM(
            SELECT s.DOSSIER AS dos,a.FAM_0001 AS famille,s.REFERENCE AS ref, s.SREFERENCE1 AS sref1, s.SREFERENCE2 AS sref2, a.DES AS designation,
            a.VENUN AS uv, a.HSDT AS fermeture, s.DEPOT AS depot, s.EMPLACEMENT AS emplacement, SUM(s.QTETJSENSTOCK) AS stock
            FROM MVTL_STOCK_V s
            INNER JOIN ART a ON a.DOS = s.DOSSIER AND s.REFERENCE = a.REF
            WHERE s.DOSSIER = $dos AND NOT a.FAM_0001 IN ('TRANSPOR', 'TAXE', 'DIVERS') AND NOT a.REF LIKE ('DIVERS20%') $code $codeFamille
            GROUP BY s.DOSSIER, a.FAM_0001, s.REFERENCE, s.SREFERENCE1, s.SREFERENCE2, a.DES, a.VENUN, a.HSDT, s.DEPOT, s.EMPLACEMENT)reponse
            LEFT JOIN MOUV m ON ref = m.REF AND sref1 = m.SREF1 AND sref2 = m.SREF2 AND dos = m.DOS
            AND m.BLDT BETWEEN '$dd' AND '$df' AND m.TICOD = 'C' AND m.OP IN('C','D') AND m.BLNO <> 0
            GROUP BY dos,famille,ref,sref1,sref2,designation,uv,fermeture, depot, emplacement, stock
            ORDER BY nbeBl DESC
        ";
        }
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getNbeBlEtStockParFamille($dos, $dd, $df, $fermeOuvert, $famille, $stockOuBl): array
    {
        $code = '';
        $codeFamille = "";
        if ($fermeOuvert == 'ouvert') {
            $code = " AND a.HSDT IS NULL";
        }
        // s'il y a des familles de produits, il faut filtrer dessus, sinon afficher toutes les familles produits
        if ($famille) {
            $codeFamille = " AND a.FAM_0001 IN($famille)";
        }
        $conn = $this->getEntityManager()->getConnection();
        if ($stockOuBl == 'bl') {
            $sql = "SELECT dos, LTRIM(RTRIM(famille)) AS famille, SUM(nbeBl) AS nbeBl
            FROM(
            SELECT MOUV.DOS AS dos,a.FAM_0001 as famille, COUNT(MOUV.BLNO) AS nbeBl
            FROM MOUV
            INNER JOIN ART a ON a.REF = MOUV.REF AND a.DOS = MOUV.DOS
            WHERE MOUV.BLDT BETWEEN '$dd' AND '$df' AND MOUV.TICOD = 'C' AND MOUV.OP IN('C','D') AND MOUV.BLNO <> 0 AND MOUV.DOS = $dos $code $codeFamille
            AND NOT a.FAM_0001 IN ('TRANSPOR', 'TAXE', 'DIVERS') AND NOT a.REF LIKE ('DIVERS20%')
            GROUP BY MOUV.DOS, a.FAM_0001, MOUV.REF, MOUV.SREF1, MOUV.SREF2, a.DES, MOUV.VENUN, a.HSDT)reponse
            GROUP BY dos, famille
            ORDER BY nbeBl DESC
        ";
        } else {
            $sql = "SELECT dos,LTRIM(RTRIM(famille)) AS famille, COUNT(m.BLNO) AS nbeBl
            FROM(
            SELECT s.DOSSIER AS dos,a.FAM_0001 AS famille,s.REFERENCE AS ref, s.SREFERENCE1 AS sref1, s.SREFERENCE2 AS sref2, a.DES AS designation,
            a.VENUN AS uv, a.HSDT AS fermeture, s.DEPOT AS depot, s.EMPLACEMENT AS emplacement, SUM(s.QTETJSENSTOCK) AS stock
            FROM MVTL_STOCK_V s
            INNER JOIN ART a ON a.DOS = s.DOSSIER AND s.REFERENCE = a.REF
            WHERE s.DOSSIER = $dos AND NOT a.FAM_0001 IN ('TRANSPOR', 'TAXE', 'DIVERS') AND NOT a.REF LIKE ('DIVERS20%') $code $codeFamille
            GROUP BY s.DOSSIER, a.FAM_0001, s.REFERENCE, s.SREFERENCE1, s.SREFERENCE2, a.DES, a.VENUN, a.HSDT, s.DEPOT, s.EMPLACEMENT)reponse
            LEFT JOIN MOUV m ON ref = m.REF AND sref1 = m.SREF1 AND sref2 = m.SREF2 AND dos = m.DOS
            AND m.BLDT BETWEEN '$dd' AND '$df' AND m.TICOD = 'C' AND m.OP IN('C','D') AND m.BLNO <> 0
            GROUP BY dos,famille
            ORDER BY nbeBl DESC
        ";
        }
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
    // activité métier par produits
    public function getActivitesMetier($dd, $df, $metier): array
    {
        $code = '';
        if ($metier == 'EV') {
            $code = "AND c.STAT_0002 IN ('EV') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'HP') {
            $code = "AND c.STAT_0002 IN ('HP') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'ME') {
            $code = "AND a.FAM_0002 IN ('ME', 'MO')";
        } elseif ($metier == 'Tous') {
            $code = "AND a.FAM_0002 IN ('EV', 'HP', 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille, ref,des, SUM(qte) AS qteSign, SUM(montant) AS montantSign
            FROM(
            SELECT a.FAM_0001 AS famille, m.REF AS ref, m.DES AS des,
                CASE
                    WHEN m.OP IN ('C','CD') THEN m.MONT - m.REMPIEMT_0004
                    WHEN m.OP IN ('D','DD') THEN (-1 * m.MONT) + m.REMPIEMT_0004
                END AS montant,
                CASE
                    WHEN m.OP IN ('C','CD') THEN m.FAQTE
                    WHEN m.OP IN ('D','DD') THEN (-1 * m.FAQTE)
                END AS qte
            FROM MOUV m
            INNER JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
            INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
            WHERE m.DOS = 1 AND m.FADT BETWEEN '$dd' AND '$df'
            $code
            AND m.TICOD = 'C' AND m.PICOD = 4
            AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
            GROUP BY famille, ref, des
            ORDER BY montantSign DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // activité métier par clients
    public function getActivitesMetierClient($dd, $df, $metier): array
    {
        $code = '';
        if ($metier == 'EV') {
            $code = "AND c.STAT_0002 IN ('EV') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'HP') {
            $code = "AND c.STAT_0002 IN ('HP') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'ME') {
            $code = "AND a.FAM_0002 IN ('ME', 'MO')";
        } elseif ($metier == 'Tous') {
            $code = "AND a.FAM_0002 IN ('EV', 'HP', 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille,tiers, nom, SUM(montant) AS montantSign
        FROM(
        SELECT RTRIM(LTRIM(c.STAT_0001)) AS famille, c.TIERS AS tiers, c.NOM AS nom,
        CASE
        WHEN m.OP IN ('C','CD') THEN m.MONT - m.REMPIEMT_0004
        WHEN m.OP IN ('D','DD') THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montant,
        CASE
        WHEN m.OP IN ('C','CD') THEN m.FAQTE
        WHEN m.OP IN ('D','DD') THEN (-1 * m.FAQTE)
        END AS qte
        FROM MOUV m
        INNER JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = 1 AND m.FADT BETWEEN '$dd' AND '$df'
        $code
        AND m.TICOD = 'C' AND m.PICOD = 4
        AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
        GROUP BY famille, tiers, nom
        ORDER BY montantSign DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getTotalActivitesMetier($dd, $df, $metier)
    {
        $code = '';
        if ($metier == 'EV') {
            $code = "AND c.STAT_0002 IN ('EV') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'HP') {
            $code = "AND c.STAT_0002 IN ('HP') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'ME') {
            $code = "AND a.FAM_0002 IN ('ME', 'MO')";
        } elseif ($metier == 'Tous') {
            $code = "AND a.FAM_0002 IN ('EV', 'HP', 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SUM(montantSign) AS total FROM(
            SELECT famille, ref,des, SUM(qte) AS qteSign, SUM(montant) AS montantSign
            FROM(
            SELECT a.FAM_0001 AS famille, m.REF AS ref, m.DES AS des,
                CASE
                    WHEN m.OP IN ('C','CD') THEN m.MONT - m.REMPIEMT_0004
                    WHEN m.OP IN ('D','DD') THEN (-1 * m.MONT) + m.REMPIEMT_0004
                END AS montant,
                CASE
                    WHEN m.OP IN ('C','CD') THEN m.FAQTE
                    WHEN m.OP IN ('D','DD') THEN (-1 * m.FAQTE)
                END AS qte
            FROM MOUV m
            INNER JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
            INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
            WHERE m.DOS = 1 AND m.FADT BETWEEN '$dd' AND '$df'
            $code
            AND m.TICOD = 'C' AND m.PICOD = 4
            AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
            GROUP BY famille, ref, des)rep
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    public function getActivitesFamilleProduit($dd, $df, $metier): array
    {
        $code = '';
        if ($metier == 'EV') {
            $code = "AND c.STAT_0002 IN ('EV') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'HP') {
            $code = "AND c.STAT_0002 IN ('HP') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'ME') {
            $code = "AND a.FAM_0002 IN ('ME', 'MO')";
        } elseif ($metier == 'Tous') {
            $code = "AND a.FAM_0002 IN ('EV', 'HP', 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille, SUM(montant) AS montantSign
            FROM(
            SELECT RTRIM(LTRIM(a.FAM_0001)) AS famille, m.REF AS ref, m.DES AS des,
            CASE
            WHEN m.OP IN ('C','CD') THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('D','DD') THEN (-1 * m.MONT) + m.REMPIEMT_0004
            END AS montant,
            CASE
            WHEN m.OP IN ('C','CD') THEN m.FAQTE
            WHEN m.OP IN ('D','DD') THEN (-1 * m.FAQTE)
            END AS qte
            FROM MOUV m
            INNER JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
            INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
            WHERE m.DOS = 1 AND m.FADT BETWEEN '$dd' AND '$df'
            $code
            AND m.TICOD = 'C' AND m.PICOD = 4
            AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
            GROUP BY famille
            ORDER BY montantSign DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getActivitesFamilleClient($dd, $df, $metier): array
    {
        $code = '';
        if ($metier == 'EV') {
            $code = "AND c.STAT_0002 IN ('EV') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'HP') {
            $code = "AND c.STAT_0002 IN ('HP') AND a.FAM_0002 IN ('EV', 'HP')";
        } elseif ($metier == 'ME') {
            $code = "AND a.FAM_0002 IN ('ME', 'MO')";
        } elseif ($metier == 'Tous') {
            $code = "AND a.FAM_0002 IN ('EV', 'HP', 'ME', 'MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille, SUM(montant) AS montantSign
            FROM(
            SELECT RTRIM(LTRIM(c.STAT_0001)) AS famille, m.REF AS ref, m.DES AS des,
            CASE
            WHEN m.OP IN ('C','CD') THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('D','DD') THEN (-1 * m.MONT) + m.REMPIEMT_0004
            END AS montant,
            CASE
            WHEN m.OP IN ('C','CD') THEN m.FAQTE
            WHEN m.OP IN ('D','DD') THEN (-1 * m.FAQTE)
            END AS qte
            FROM MOUV m
            INNER JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
            INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
            WHERE m.DOS = 1 AND m.FADT BETWEEN '$dd' AND '$df'
            $code
            AND m.TICOD = 'C' AND m.PICOD = 4
            AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
            GROUP BY famille
            ORDER BY montantSign DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
