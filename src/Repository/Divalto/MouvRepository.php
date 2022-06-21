<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function getFscOrderList($listpieceOk):array
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
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Liste des piéces avec des produits FSC pour tourner à vide
    public function getFscOrderListRun():array
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
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNotreRef($piece, $typePiece, $tiers)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.PIREF AS notreRef
        FROM ENT
        WHERE ENT.DOS = 3 AND ENT.PINO = $piece AND ENT.TIERS = '$tiers' AND ENT.PICOD = $typePiece
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Mouvements sur la piéce
    public function getMouvOnOrder($num, $typePiece, $tiers):array
    {
        if ($typePiece == 2 ) {
            $code = 'MOUV.CDNO';
            $dateP = 'MOUV.CDDT';        
        }elseif ($typePiece == 3 ) {
            $code = 'MOUV.BLNO';
            $dateP = 'MOUV.BLDT';    
        }elseif ($typePiece == 4 ) {
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
        $stmt->execute();
        return $stmt->fetchAll();
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
        $stmt->execute();
        return $stmt->fetch();
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
        $stmt->execute();
        return $stmt->fetchAll();
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
        $stmt->execute();
        return $stmt->fetchAll();
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
        $stmt->execute();
        return $stmt->fetchAll();
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
            $stmt->execute();
            return $stmt->fetch();
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
        $stmt->execute();
        return $stmt->fetchAll();
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
    $stmt->execute();
    return $stmt->fetch();
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
    $stmt->execute();
    return $stmt->fetchAll();
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
    $stmt->execute();
    return $stmt->fetchAll();
}

// ramener le Détail d'une facture client FSC
public function getDetailFactureFscClient($facture):array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT MOUV.TIERS AS tiers, MOUV.REF AS ref, MOUV.DES AS designation, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MOUV.OP AS op, MOUV.FAQTE AS qte, MVTL_STOCK_V.SERIELOT AS lot
    FROM MOUV
    LEFT JOIN MVTL_STOCK_V ON MOUV.REF = MVTL_STOCK_V.REFERENCE AND MOUV.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND MOUV.SREF2 = MVTL_STOCK_V.SREFERENCE2
    WHERE MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.FANO = ? AND MOUV.DOS = 3
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$facture]);
    return $stmt->fetchAll();
}

// ramener les BLs Directs
public function getListBlDirect():array
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
    $stmt->execute();
    return $stmt->fetchAll();
}


}
