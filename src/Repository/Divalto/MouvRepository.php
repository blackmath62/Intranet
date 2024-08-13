<?php

namespace App\Repository\Divalto;

use App\Controller\StatsAchatController;
use App\Entity\Divalto\Mouv;
use DateTime;
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
    private $statsAchatController;

    public function __construct(ManagerRegistry $registry, StatsAchatController $statsAchatController)
    {
        parent::__construct($registry, Mouv::class);
        $this->statsAchatController = $statsAchatController;

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

    public function getPiecesByCommercialByPeriods($piece, $commerciaux, $start, $end)
    {
        if ($piece == 1) {
            $p = 'm.DVDT';
            $pino = 'm.DVNO';
        } elseif ($piece == 2) {
            $p = 'm.CDDT';
            $pino = 'm.CDNO';
        } elseif ($piece == 3) {
            $p = 'm.BLDT';
            $pino = 'm.BLNO';
        } elseif ($piece == 4) {
            $p = 'm.FADT';
            $pino = 'm.FANO';
        }
        // Création d'objets DateTime pour $start et $end
        $start_date = new DateTime($start);
        $end_date = new DateTime($end);

        // Calcul de la différence entre les deux dates
        $diff = $start_date->diff($end_date);

        // Récupération de la différence en années
        $diff_years = $diff->y;
        $start1 = clone $start_date;
        $start1->modify('-' . $diff_years + 1 . ' year');
        $start1 = $start1->format('Y-m-d');
        $start_date1 = new DateTime($start1);

        $start2 = clone $start_date1;
        $start2->modify('-' . $diff_years + 1 . ' year');
        $start2 = $start2->format('Y-m-d');
        $start_date2 = new DateTime($start2);

        $start3 = clone $start_date2;
        $start3->modify('-' . $diff_years + 1 . ' year');
        $start3 = $start3->format('Y-m-d');

        // Création d'une nouvelle date avec l'année de $start moins un an, et le mois et le jour de $end
        $end1 = clone $start_date;
        $end1->modify('-1 year');
        $end1->setDate($end1->format('Y'), $end_date->format('m'), $end_date->format('d'));
        $end1 = $end1->format('Y-m-d');

        $end2 = clone $start_date1;
        $end2->modify('-1 year');
        $end2->setDate($end2->format('Y'), $end_date->format('m'), $end_date->format('d'));
        $end2 = $end2->format('Y-m-d');

        $end3 = clone $start_date2;
        $end3->modify('-1 year');
        $end3->setDate($end3->format('Y'), $end_date->format('m'), $end_date->format('d'));
        $end3 = $end3->format('Y-m-d');

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT v.NOM AS nom,
	SUM(CASE WHEN $p >= '$start' AND $p <= '$end' AND $pino <> 0 THEN 1 ELSE 0 END) AS qte,
	SUM(
	CASE
		WHEN m.FADT >= '$start' AND m.FADT <= '$end' AND m.PICOD = 4 AND m.OP IN ('C','CD') AND m.FANO <> 0 THEN  m.MONT - m.REMPIEMT_0004
		WHEN m.FADT >= '$start' AND m.FADT <= '$end' AND m.PICOD = 4 AND m.OP IN ('D','DD') AND m.FANO <> 0 THEN  (-1 * m.MONT) + m.REMPIEMT_0004
		END
		) AS mont,
	SUM(CASE WHEN $p >= '$start1' AND $p <= '$end1' AND $pino <> 0 THEN 1 ELSE 0 END) AS qte1,
	SUM(
	CASE
		WHEN m.FADT >= '$start1' AND m.FADT <= '$end1' AND m.PICOD = 4 AND m.OP IN ('C','CD') AND m.FANO <> 0 THEN  m.MONT - m.REMPIEMT_0004
		WHEN m.FADT >= '$start1' AND m.FADT <= '$end1' AND m.PICOD = 4 AND m.OP IN ('D','DD') AND m.FANO <> 0 THEN  (-1 * m.MONT) + m.REMPIEMT_0004
		END
		) AS mont1,
	SUM(CASE WHEN $p >= '$start2' AND $p <= '$end2' AND $pino <> 0  THEN 1 ELSE 0 END) AS qte2,
	SUM(
	CASE
		WHEN m.FADT >= '$start2' AND m.FADT <= '$end2' AND m.PICOD = 4 AND m.OP IN ('C','CD') AND m.FANO <> 0 THEN  m.MONT - m.REMPIEMT_0004
		WHEN m.FADT >= '$start2' AND m.FADT <= '$end2' AND m.PICOD = 4 AND m.OP IN ('D','DD') AND m.FANO <> 0 THEN  (-1 * m.MONT) + m.REMPIEMT_0004
		END
		) AS mont2,
	SUM(CASE WHEN $p >= '$start3' AND $p <= '$end3' AND $pino <> 0 THEN 1 ELSE 0 END) AS qte3,
	SUM(
	CASE
		WHEN m.FADT >= '$start3' AND m.FADT <= '$end3' AND m.PICOD = 4 AND m.OP IN ('C','CD') AND m.FANO <> 0 THEN  m.MONT - m.REMPIEMT_0004
		WHEN m.FADT >= '$start3' AND m.FADT <= '$end3' AND m.PICOD = 4 AND m.OP IN ('D','DD') AND m.FANO <> 0 THEN  (-1 * m.MONT) + m.REMPIEMT_0004
		END
		) AS mont3
    FROM MOUV m
    INNER JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
    INNER JOIN VRP v ON v.DOS = m.DOS AND m.REPR_0001 = v.TIERS
    INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
    WHERE m.DOS = 1 AND $p >= '$start3' AND $p <= '$end' AND m.TICOD = 'C'
    AND a.FAM_0002 IN ('HP','EV') AND m.REPR_0001 IN ($commerciaux)
    GROUP BY v.NOM
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function searchCodeAffairePiece($tiers, $piece)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.PROJET AS affaire, m.TIERS AS tiers, c.NOM AS nom, m.FADT AS dateFact,  m.REF AS ref, m.DES AS designation, m.SREF1 AS sref1, m.SREF2 AS sref2, m.VENUN AS uv,m.OP AS op, m.FAQTE AS qte
        FROM MOUV m
        LEFT JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = 1 AND m.PICOD = 4 AND m.TICOD = '$tiers' AND m.FANO = '$piece'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function changeCodeAffairePiece($tiers, $piece, $affaire)
    {
        if ($affaire) {
            $ce3 = "m.CE3 = 1";
        } else {
            $ce3 = "m.CE3 = ''";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "UPDATE m
        SET m.PROJET = '$affaire', $ce3
        FROM MOUV m
        LEFT JOIN CLI c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = 1 AND m.PICOD = 4 AND m.TICOD = '$tiers' AND m.FANO = '$piece'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
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

    // Mouvements sur la piéce client
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

    // Liste des produits phytos ouverts et leurs stocks dans Divalto
    public function getListePhytos()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ref, designation, uv, amm, SUM(stock) AS stock FROM(
            SELECT a.REF AS ref, a.DES AS designation, a.VENUN AS uv, a.UP_CODEAMM AS amm, s.QTETJSENSTOCK AS stock
            FROM ART a
            LEFT JOIN MVTL_STOCK_V s ON a.REF = s.REFERENCE AND a.DOS = s.DOSSIER
            WHERE a.REF LIKE 'PPP%' AND a.DOS = 1 AND a.HSDT IS NULL)reponse
            GROUP BY ref, designation, uv, amm
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Mouvements sur la piéce Fournisseur
    public function getLastMouvFou($dos)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers AS tiers, nom AS nom, MAX(MOUV.CDDT) AS dernCmd, MAX(MOUV.BLDT) AS dernBl, MAX(MOUV.FADT) AS dernFact
        FROM(
        SELECT FOU.DOS AS dos, RTRIM(LTRIM(FOU.TIERS)) AS tiers, RTRIM(LTRIM(FOU.NOM)) AS nom
        FROM FOU
        WHERE FOU.DOS IN ($dos) AND FOU.HSDT IS NULL)reponse
        LEFT JOIN MOUV ON tiers = MOUV.TIERS AND dos = MOUV.DOS
        GROUP BY tiers, nom
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

// Extraction des factures de ventes ou d'achat sur une période résumé par type FSC
    public function getExtractPeriodByTypeFsc($start, $end, $tiers, $type): array
    {
        if ($tiers == 'C') {
            $opPositive = "'C','CD'";
            $opNegative = "'D','DD'";
        } elseif ($tiers == 'F') {
            $opPositive = "'F','FD'";
            $opNegative = "'G','GD'";
        }

        if ($type == 'resume') {
            $selectType = " typeFsc,SUM(qte) AS qte";
            $groupType = " typeFsc";
        } elseif ($type == 'detail') {
            $selectType = " typeFsc, serie, ref,sref1,sref2,designation,uv, SUM(qte) AS qte";
            $groupType = " typeFsc, serie, ref,sref1,sref2,designation,uv";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT $selectType
                    FROM(
                        SELECT m.REF AS ref, m.SREF1 AS sref1, m.SREF2 AS sref2, a.DES AS designation, m.VENUN AS uv, m.BLNO AS bl, m.BLDT AS dateBl, BLQTE AS qteBl, m.FANO AS facture, m.FADT AS dateFact, v.SERIE AS serie,
                        CASE
                        WHEN m.OP IN ($opPositive) THEN m.FAQTE
                        WHEN m.OP IN ($opNegative) THEN -1 * m.FAQTE
                        END AS qte,
                        CASE
                        WHEN m.REF LIKE '%FSC100%' THEN 'fsc 100 %'
                        WHEN m.REF LIKE '%FSCMIXCREDIT%' THEN 'fsc mix crédit'
                        WHEN m.REF LIKE '%FSCMIX%' AND m.REF NOT LIKE '%CREDIT%' THEN 'fsc mix'
                        END AS typeFsc
                        FROM MOUV m
                        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
                        INNER JOIN MVTL v ON m.DOS = v.DOS AND m.ENRNO = v.ENRNO
                        WHERE m.DOS = 3 AND m.TICOD = '$tiers'
                        AND m.BLDT BETWEEN '$start' AND '$end' AND m.REF LIKE '%FSC%'
                    )reponse
                GROUP BY $groupType
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

    // Liste des Affaires NOUVELLE VERSION !!!
    public function getAffaires(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(p.AFFAIRE)) AS affaire, LTRIM(RTRIM(r.RBQVAL)) AS duration, LTRIM(RTRIM(p.LIB80)) AS libelle, LTRIM(RTRIM(p.TIERS)) AS tiers, LTRIM(RTRIM(c.NOM)) AS nom, LTRIM(RTRIM(p.USERCRDH)) AS dateCreation
       FROM PRJAP p
       INNER JOIN CLI c ON p.DOS = c.DOS AND p.TIERS = c.TIERS
       LEFT JOIN MRBQVAL r ON p.DOS = r.DOS AND p.AFFAIRE = r.ENTITEINDEX AND 'JOURS' = r.RUBRIQUE
       WHERE p.DOS = 1
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Liste des Pieces liées aux Affaires
    public function getPiecesAffaires($affaire): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(m.PROJET)) AS affaire, LTRIM(RTRIM(m.CDNO)) AS cdno, LTRIM(RTRIM(m.BLNO)) AS blno
        FROM MOUV m
        WHERE m.DOS = 1 AND m.PROJET = '$affaire' AND m.TICOD = 'C' AND (m.CDNO > 0 OR m.BLNO > 0) --AND m.PICOD IN (2, 3)
        GROUP BY m.PROJET, m.DVNO, m.CDNO,m.BLNO
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Liste des entêtes de Pieces liées aux Affaires
    public function getEntetePiecesAffaires($num, $type)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(e.PROJET)) AS affaire, LTRIM(RTRIM(e.ENT_ID)) AS id,
        LTRIM(RTRIM(e.PICOD)) AS typeP, LTRIM(RTRIM(e.PINO)) AS piece,
        LTRIM(RTRIM(e.OP)) AS op, LTRIM(RTRIM(e.BLMOD)) AS transport,
        CASE
            WHEN e.ADRCOD_0003 = '' THEN CONCAT(LTRIM(RTRIM(c.RUE)), ', ', LTRIM(RTRIM(c.CPOSTAL)), ' ', LTRIM(RTRIM(c.VIL)) )
            ELSE CONCAT(LTRIM(RTRIM(T1.NOM)), ', ', LTRIM(RTRIM(T1.RUE)), ', ', LTRIM(RTRIM(T1.CPOSTAL)), ' ', LTRIM(RTRIM(T1.VIL)) )
        END AS adresse
        FROM ENT e
        INNER JOIN CLI c ON c.DOS = e.DOS AND c.TIERS = e.TIERS
        LEFT JOIN T1 ON e.TIERS = T1.TIERS AND e.DOS = T1.DOS AND  e.ADRCOD_0003 = T1.ADRCOD
        WHERE e.DOS = 1 AND e.PICOD = $type AND e.TICOD = 'C' AND e.PINO = $num
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $result = $resultSet->fetchAssociative();
        return $result;
    }

    // Detail des Pieces liées aux Affaires
    public function getDetailPiecesAffaires($pino, $picod): array
    {

        if ($picod == 2) {
            $pino = 'AND m.CDNO IN (' . $pino . ')';
        } elseif ($picod == 3) {
            $pino = 'AND m.BLNO IN (' . $pino . ')';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(m.DOS)) as dos, LTRIM(RTRIM(m.TIERS)) AS tiers, LTRIM(RTRIM(m.REF)) AS ref, LTRIM(RTRIM(m.SREF1)) AS sref1, LTRIM(RTRIM(m.SREF2)) AS sref2, LTRIM(RTRIM(m.DES)) AS designation, LTRIM(RTRIM(m.VENUN)) AS uv,
        LTRIM(RTRIM(m.OP)) AS op, LTRIM(RTRIM(a.CDEFOQTE)) AS cmdFou,SUM(s.QTETJSENSTOCK) AS stock, LTRIM(RTRIM(a.SREFCOD)) AS codeSref, LTRIM(RTRIM(sean.EAN)) as ean, LTRIM(RTRIM(a.HSDT)) AS ferme, LTRIM(RTRIM(sart.CONF)) AS fermeSart,
        LTRIM(RTRIM(m.TICOD)) AS ticod, SUM(m.CDQTE) AS cmdQte, SUM(m.BLQTE) AS blQte, noteLig.NOTEBLOB as note
        FROM MOUV m
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        LEFT JOIN MVTL_STOCK_V s ON a.DOS = s.DOSSIER AND m.REF = s.REFERENCE AND m.SREF1 = s.SREFERENCE1 AND m.SREF2 = s.SREFERENCE2
        LEFT JOIN SARTEAN sean ON a.DOS = sean.DOS AND m.REF = sean.REF AND m.SREF1 = sean.SREF1 AND m.SREF2 = sean.SREF2
        LEFT JOIN SART sart ON a.DOS = sart.DOS AND m.REF = sart.REF AND m.SREF1 = sart.SREF1 AND m.SREF2 = sart.SREF2
        LEFT JOIN MNOTE noteLig WITH (INDEX = INDEX_A) ON m.TXTNOTE = noteLig.NOTE
        WHERE m.DOS = 1 AND m.TICOD = 'C' $pino
        GROUP BY m.TIERS, m.PICOD, m.DOS, m.REF, m.SREF1, m.SREF2, m.DES, m.VENUN, m.OP,m.CDQTE,m.BLQTE,
        m.FAQTE,m.CDNO,m.BLNO, a.CDEFOQTE, a.SREFCOD, a.EAN, a.HSDT, m.TICOD, sean.EAN, sart.CONF, noteLig.NOTEBLOB
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
    MAX(LTRIM(RTRIM(ENT.BLMOD))) AS transport, MAX(LTRIM(RTRIM(ENT.PROJET))) AS affaire, MAX(ENT.OP) AS op, ENT.ENT_ID AS id, MAX(utilisateur) AS utilisateur,
    CASE
    WHEN MAX(ENT.ADRCOD_0003) = '' THEN MAX(adressePrincipale)
    ELSE MAX(CONCAT(LTRIM(RTRIM(T1.NOM)), ', ', LTRIM(RTRIM(T1.RUE)), ', ', LTRIM(RTRIM(T1.CPOSTAL)), ' ', LTRIM(RTRIM(T1.VIL)) ) )
    END AS adresseLivraison
    FROM(
    SELECT LTRIM(RTRIM(MOUV.DOS)) AS dos, LTRIM(RTRIM(MOUV.TIERS)) AS tiers, LTRIM(RTRIM(CLI.NOM)) AS nom, MOUV.PICOD as typePiece,
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
    AND MUSER.MUSER_ID IN (4, 13, 16, 61)
    GROUP BY MOUV.DOS, MOUV.TIERS, CLI.NOM, MOUV.PICOD, MOUV.CDDT, MOUV.CDNO, MOUV.BLDT, MOUV.BLNO, MOUV.FADT, MOUV.FANO, CLI.RUE, CLI.CPOSTAL, CLI.VIL)reponse
    INNER JOIN ENT ON ENT.DOS = dos AND ENT.TIERS = tiers AND ENT.PICOD = typePiece AND ENT.PINO = numPiece AND ENT.TICOD = 'C'
    LEFT JOIN T1 ON tiers = T1.TIERS AND dos = T1.DOS AND  ENT.ADRCOD_0003 = T1.ADRCOD
    WHERE (datePiece >= '2022-06-01' AND NOT numFacture IN ($termine)) OR ( numCmd IN($cmd) OR numBl IN ($bl) OR numFacture IN ($facture) )
    GROUP BY ENT.ENT_ID
    ORDER BY numCmd ASC
    ";
        // AND ENT.PROJET <> ''
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

// Commande et BL de la veille pour les clients feu rouge
    public function getCmdBlClientFeuRouge(): array
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
        WHERE ENT.PIDT = DATEADD(day,-1,CAST(GETDATE() as date)) AND ENT.CE4 = 1 AND ENT.DOS = 1 )reponse
        WHERE feu = 'rouge'
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
    public function getActivitesMetier($dos, $dd, $df, $metier): array
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
        } elseif ($metier == 'RB') {
            $code = "";
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
            WHERE m.DOS = $dos AND m.FADT BETWEEN '$dd' AND '$df'
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
    public function getActivitesMetierClient($dos, $dd, $df, $metier, $type): array
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
        } elseif ($metier == 'RB') {
            $code = "";
        }

        if ($type == 'CLI') {
            $op = "'C' , 'CD'";
            $nop = "'D' , 'DD'";
        } elseif ($type == 'FOU') {
            $op = "'F' , 'FD'";
            $nop = "'G' , 'GD'";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT famille,tiers, nom, SUM(montant) AS montantSign
        FROM(
        SELECT RTRIM(LTRIM(c.STAT_0001)) AS famille, c.TIERS AS tiers, c.NOM AS nom,
        CASE
        WHEN m.OP IN ($op) THEN m.MONT - m.REMPIEMT_0004
        WHEN m.OP IN ($nop) THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montant,
        CASE
        WHEN m.OP IN ($op) THEN m.FAQTE
        WHEN m.OP IN ($nop) THEN (-1 * m.FAQTE)
        END AS qte
        FROM MOUV m
        INNER JOIN $type c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = $dos AND m.FADT BETWEEN '$dd' AND '$df'
        $code
        AND m.TICOD = '$type[0]' AND m.PICOD = 4
        AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
        GROUP BY famille, tiers, nom
        ORDER BY montantSign DESC
        ";
        //dd($sql);
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getTotalActivitesMetier($dos, $dd, $df, $metier, $type)
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
        } elseif ($metier == 'RB') {
            $code = "";
        }
        if ($type == 'CLI') {
            $op = "'C' , 'CD'";
            $nop = "'D' , 'DD'";
        } elseif ($type == 'FOU') {
            $op = "'F' , 'FD'";
            $nop = "'G' , 'GD'";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SUM(montantSign) AS total FROM(
            SELECT famille, ref,des, SUM(qte) AS qteSign, SUM(montant) AS montantSign
            FROM(
            SELECT a.FAM_0001 AS famille, m.REF AS ref, m.DES AS des,
                CASE
                    WHEN m.OP IN ($op) THEN m.MONT - m.REMPIEMT_0004
                    WHEN m.OP IN ($nop) THEN (-1 * m.MONT) + m.REMPIEMT_0004
                END AS montant,
                CASE
                    WHEN m.OP IN ($op) THEN m.FAQTE
                    WHEN m.OP IN ($nop) THEN (-1 * m.FAQTE)
                END AS qte
            FROM MOUV m
            INNER JOIN $type c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
            INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
            WHERE m.DOS = $dos AND m.FADT BETWEEN '$dd' AND '$df'
            $code
            AND m.TICOD = '$type[0]' AND m.PICOD = 4
            AND a.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP'))reponse
            GROUP BY famille, ref, des)rep
        ";
        //dd($sql);
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    public function getActivitesFamilleProduit($dos, $dd, $df, $metier): array
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
        } elseif ($metier == 'RB') {
            $code = "";
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
            WHERE m.DOS = $dos AND m.FADT BETWEEN '$dd' AND '$df'
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

    public function getActivitesFamilleClient($dos, $dd, $df, $metier): array
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
        } elseif ($metier == 'RB') {
            $code = "";
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
            WHERE m.DOS = $dos AND m.FADT BETWEEN '$dd' AND '$df'
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

    public function getCmdBlDepot1($typePiece, $user): array
    {

        if ($typePiece == 2) {
            $pino = "m.CDNO";
            $pice4 = "m.CDCE4";
            $pidt = 'm.CDDT';
        } elseif ($typePiece == 3) {
            $pino = "m.BLNO";
            $pice4 = "m.BLCE4";
            $pidt = 'm.BLDT';
        }

        if ($user == null) {
            $code = 'DISTINCT RTRIM(LTRIM(u.EMAIL)) AS mail';
            $mailUser = '';
        } else {
            $code = 'dos, piece, tiers, op, ref, sref1, sref2, designation, uv, RTRIM(LTRIM(u.EMAIL)) AS mail';
            $mailUser = "WHERE u.EMAIL = '$user'";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT $code
        FROM(
        SELECT RTRIM(LTRIM(m.DOS)) AS dos, $pino AS piece, RTRIM(LTRIM(m.TIERS)) AS tiers, RTRIM(LTRIM(m.OP)) AS op, RTRIM(LTRIM(m.REF)) AS ref, RTRIM(LTRIM(m.SREF1)) AS sref1, RTRIM(LTRIM(m.SREF2)) AS sref2, RTRIM(LTRIM(m.DES)) AS designation, RTRIM(LTRIM(m.VENUN)) AS uv,
        CASE
        WHEN m.USERMO = '' THEN m.USERCR
        WHEN m.USERMO = '' AND m.USERCR = '' THEN 'JEROME'
        ELSE m.USERMO
        END AS utilisateur
        FROM MOUV m
        WHERE $pidt > '2023-01-01' AND $pice4 = '1' AND m.DEPO = '1' AND m.TICOD IN ('F', 'C'))reponse
        INNER JOIN MUSER u ON u.USERX = utilisateur
        $mailUser
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getMouvRbue($dd, $df): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(m.TIERS)) AS tiers, LTRIM(RTRIM(m.CDDT)) AS dateCmd, LTRIM(RTRIM(e.PIREF)) AS notreRef,LTRIM(RTRIM(m.CDNO)) AS cmd, LTRIM(RTRIM(m.BLNO)) AS bl, LTRIM(RTRIM(m.FANO)) AS facture, LTRIM(RTRIM(m.REF)) AS ref, LTRIM(RTRIM(m.SREF1)) AS sref1, LTRIM(RTRIM(m.SREF2)) AS sref2,
        LTRIM(RTRIM(a.DES)) AS designation, LTRIM(RTRIM(a.FAM_0001)) AS famille, LTRIM(RTRIM(m.FAQTE)) AS qte
        FROM MOUV m
        INNER JOIN FOU f ON f.DOS = m.DOS AND m.TIERS = f.TIERS
        INNER JOIN T013 p ON f.PAY = p.PAY
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN ENT e ON e.DOS = m.DOS AND e.PINO = m.CDNO
        WHERE m.DOS = 3 AND m.TICOD = 'F' AND m.PICOD IN (2,3,4) --AND f.TIERS = 'FJUANCHE'
        AND p.TVAPAYTYP = 1 AND a.FAM_0001 <> 'TRANSPOR' AND m.REF <> 'DIVERS20%' AND m.CDDT BETWEEN '$dd' AND '$df'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getVenteClientSur3Ans($dd, $df, $type, $metier): array
    {

        $ddN1 = new DateTime($dd);
        $ddN1 = date_modify($ddN1, '-1 Year');
        $ddN1 = $ddN1->format('Y-m-d');
        $ddN2 = new DateTime($dd);
        $ddN2 = date_modify($ddN2, '-2 Year');
        $ddN2 = $ddN2->format('Y-m-d');
        $dfN1 = new DateTime($df);
        $dfN1 = date_modify($dfN1, '-1 Year');
        $dfN1 = $dfN1->format('Y-m-d');
        $dfN2 = new DateTime($df);
        $dfN2 = date_modify($dfN2, '-2 Year');
        $dfN2 = $dfN2->format('Y-m-d');

        if ($type == 'CLIENT') {
            $select = 'tiers as tiers, nom AS nom, cp AS cp, tel AS tel, famille AS famille, siret AS siret, intra AS intra, blob AS blob';
            $group = 'tiers, nom,cp,tel, famille, siret, intra, blob';
        } elseif ($type == 'FAMILLE') {
            $select = 'famille AS famille';
            $group = 'famille';
        }

        if ($metier == 'Tous') {
            $secteur = '';
        } elseif ($metier == 'EV') {
            $secteur = "AND c.STAT_0002 = 'EV' AND a.FAM_0002 IN ('EV','HP')";
        } elseif ($metier == 'HP') {
            $secteur = "AND c.STAT_0002 = 'HP' AND a.FAM_0002 IN ('EV','HP')";
        } elseif ($metier == 'ME') {
            $secteur = "AND a.FAM_0002 IN ('ME','MO')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT $select ,SUM(montantN) AS montantN, SUM(montantN1) AS montantN1, SUM(montantN2) AS montantN2
        FROM(
            SELECT m.TIERS AS tiers, c.NOM AS nom, c.CPOSTAL AS cp, c.TEL AS tel, c.STAT_0001 AS famille ,c.SIRET AS siret, c.TVANO AS intra, n.NOTEBLOB AS blob,
            CASE
                WHEN m.OP IN ('C', 'CD') AND m.FADT BETWEEN '$dd' AND '$df' THEN m.MONT - m.REMPIEMT_0004
                WHEN m.OP IN ('D', 'DD') AND m.FADT BETWEEN '$dd' AND '$df' THEN (-1 * m.MONT) + m.REMPIEMT_0004
            END AS montantN,
            CASE
                WHEN m.OP IN ('C', 'CD') AND m.FADT BETWEEN '$ddN1' AND '$dfN1' THEN m.MONT - m.REMPIEMT_0004
                WHEN m.OP IN ('D', 'DD') AND m.FADT BETWEEN '$ddN1' AND '$dfN1' THEN (-1 * m.MONT) + m.REMPIEMT_0004
            END AS montantN1,
            CASE
                WHEN m.OP IN ('C', 'CD') AND m.FADT BETWEEN '$ddN2' AND '$dfN2' THEN m.MONT - m.REMPIEMT_0004
                WHEN m.OP IN ('D', 'DD') AND m.FADT BETWEEN '$ddN2' AND '$dfN2' THEN (-1 * m.MONT) + m.REMPIEMT_0004
            END AS montantN2
            FROM MOUV m
            LEFT JOIN CLI c ON m.DOS = c.DOS AND m.TIERS = c.TIERS
            INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
            LEFT JOIN T041 t ON c.TEXCOD_0004 = t.TEXCOD AND m.DOS = t.DOS
			LEFT JOIN MNOTE n ON t.NOTE = n.NOTE
            WHERE m.DOS = 1 AND m.PICOD = 4 AND m.TICOD = 'C' AND m.OP IN ('C', 'D', 'CD','DD') AND m.MONT > 0
            AND m.FADT BETWEEN '$ddN2' AND '$df'
            $secteur
        )reponse
        GROUP BY $group
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getRseCartographieAchatVente($dd, $df, $type, $tiers, $dos): array
    {

        $ddN1 = new DateTime($dd);
        $ddN1 = date_modify($ddN1, '-1 Year');
        $ddN1 = $ddN1->format('Y-m-d');
        $ddN2 = new DateTime($dd);
        $ddN2 = date_modify($ddN2, '-2 Year');
        $ddN2 = $ddN2->format('Y-m-d');
        $ddN3 = new DateTime($dd);
        $ddN3 = date_modify($ddN3, '-3 Year');
        $ddN3 = $ddN3->format('Y-m-d');
        $dfN1 = new DateTime($df);
        $dfN1 = date_modify($dfN1, '-1 Year');
        $dfN1 = $dfN1->format('Y-m-d');
        $dfN2 = new DateTime($df);
        $dfN2 = date_modify($dfN2, '-2 Year');
        $dfN2 = $dfN2->format('Y-m-d');
        $dfN3 = new DateTime($df);
        $dfN3 = date_modify($dfN3, '-3 Year');
        $dfN3 = $dfN3->format('Y-m-d');

        if ($type == 'DEPARTEMENT') {
            $select = 'pays, departement,';
            $group = 'GROUP BY pays, departement';
        } elseif ($type == 'GLOBAL') {
            $select = '';
            $group = '';
        }
        if ($tiers == 'CLI') {
            $avoir = 'D';
        } elseif ($tiers == 'FOU') {
            $avoir = 'G';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT $select SUM(montantN) AS montantN, SUM(montantN1) AS montantN1, SUM(montantN2) AS montantN2, SUM(montantN3) AS montantN3
        FROM(
        SELECT LEFT(RTRIM(LTRIM(c.CPOSTAL)),2) AS departement, RTRIM(LTRIM(c.PAY)) AS pays,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$dd' AND '$df' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$dd' AND '$df' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$ddN1' AND '$dfN1' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$ddN1' AND '$dfN1' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN1,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$ddN2' AND '$dfN2' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$ddN2' AND '$dfN2' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN2,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$ddN3' AND '$dfN3' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$ddN3' AND '$dfN3' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN3
        FROM MOUV m
        INNER JOIN $tiers c ON c.DOS = m.DOS AND c.TIERS = m.TIERS
        WHERE m.DOS = $dos AND m.TICOD = '$tiers[0]' AND m.PICOD = 4 AND m.FADT BETWEEN '$ddN3' AND '$df')REPONSE
        $group
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Paillage / Engrais / 5.5%, 10 % et 20 %
    public function getRseFamilleArticleTvaAchatVente($dd, $df, $type, $tiers, $famille, $dos): array
    {

        $ddN1 = new DateTime($dd);
        $ddN1 = date_modify($ddN1, '-1 Year');
        $ddN1 = $ddN1->format('Y-m-d');
        $ddN2 = new DateTime($dd);
        $ddN2 = date_modify($ddN2, '-2 Year');
        $ddN2 = $ddN2->format('Y-m-d');
        $ddN3 = new DateTime($dd);
        $ddN3 = date_modify($ddN3, '-3 Year');
        $ddN3 = $ddN3->format('Y-m-d');
        $dfN1 = new DateTime($df);
        $dfN1 = date_modify($dfN1, '-1 Year');
        $dfN1 = $dfN1->format('Y-m-d');
        $dfN2 = new DateTime($df);
        $dfN2 = date_modify($dfN2, '-2 Year');
        $dfN2 = $dfN2->format('Y-m-d');
        $dfN3 = new DateTime($df);
        $dfN3 = date_modify($dfN3, '-3 Year');
        $dfN3 = $dfN3->format('Y-m-d');

        if ($type == 'FAMILLE') {
            $select = 'famille, tva,';
            $group = 'GROUP BY famille, tva';
        } elseif ($type == 'GLOBAL') {
            $select = '';
            $group = '';
        }

        if ($tiers == 'CLI') {
            $avoir = 'D';
        } elseif ($tiers == 'FOU') {
            $avoir = 'G';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT $select SUM(montantN) AS montantN, SUM(montantN1) AS montantN1, SUM(montantN2) AS montantN2, SUM(montantN3) AS montantN3
        FROM(
        SELECT RTRIM(LTRIM(a.FAM_0001)) AS famille, RTRIM(LTRIM(a.TVAART)) AS tva,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$dd' AND '$df' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$dd' AND '$df' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$ddN1' AND '$dfN1' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$ddN1' AND '$dfN1' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN1,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$ddN2' AND '$dfN2' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$ddN2' AND '$dfN2' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN2,
        CASE
            WHEN m.OP IN ('$tiers[0]', '$tiers[0]D') AND m.FADT BETWEEN '$ddN3' AND '$dfN3' THEN m.MONT - m.REMPIEMT_0004
            WHEN m.OP IN ('$avoir', '$avoir . D') AND m.FADT BETWEEN '$ddN3' AND '$dfN3' THEN (-1 * m.MONT) + m.REMPIEMT_0004
        END AS montantN3
        FROM MOUV m
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = $dos AND m.TICOD = '$tiers[0]' AND m.PICOD = 4 AND m.FADT BETWEEN '$ddN3' AND '$df'AND a.FAM_0001 IN($famille))REPONSE
        $group
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Export des tarifs de vente de Divalto
    public function tarifsVentesDivalto($prefixe = null, $fous = null, $familles = null, $codes = null, $year = null): array
    {
        $pref = "";
        $fou = "";
        $famille = '';
        $cases = "";
        $max = "";
        $y = "";
        if ($prefixe) {
            $pref = "AND t.REF LIKE '$prefixe%'";
        }
        if ($fous) {
            $fou = "AND a.TIERS IN ($fous)";
        }
        if ($familles) {
            $famille = "AND a.FAM_0001 IN ($familles)";
        }
        if ($year) {
            $y = "AND YEAR(x.TADT) >= ($year)";
        }
        if ($codes) {
            $lesCodes = $this->statsAchatController->miseEnForme($codes);
            $code = "AND t.TACOD IN ($lesCodes)";
            foreach ($codes as $value) {
                $cases = $cases . ",CASE
                WHEN x.TACOD = '$value' THEN x.TADT
                END AS date" . $value . "
                ,CASE
                WHEN x.TACOD = '$value' THEN x.PUB
                END AS prix$value
                ,CASE
                WHEN x.TACOD = '$value' THEN x.PPAR
                END AS ppar" . $value;
                $max = $max . ",MAX(date$value) AS date$value,MAX(prix$value) AS prix$value,MAX(ppar$value) AS ppar$value ";
            }
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT tiers, ref, sref1, sref2, designation, uv, dos, conf, famille $max FROM(
        SELECT LTRIM(RTRIM(a.TIERS)) AS tiers, LTRIM(RTRIM(x.REF)) AS ref, LTRIM(RTRIM(x.SREF1)) AS sref1,
        LTRIM(RTRIM(x.SREF2)) AS sref2, LTRIM(RTRIM(a.DES)) AS designation, LTRIM(RTRIM(x.VENUN)) AS uv,
        LTRIM(RTRIM(x.DOS)) AS dos, LTRIM(RTRIM(a.FAM_0001)) AS famille,
        CASE
        WHEN s.CONF = 'Usrd' AND a.SREFCOD = 2 THEN 'Usrd'
        ELSE ''
        END AS conf
        $cases
        FROM
        ( SELECT *, ROW_NUMBER() OVER(PARTITION BY t.TACOD, t.REF, t.SREF1, t.SREF2 ORDER BY t.TADT DESC) AS sort
        FROM TAR t
        WHERE t.DOS = 1 $pref $code ) x
        INNER JOIN ART a ON x.REF = a.REF AND a.DOS = x.DOS
        INNER JOIN SART s ON x.REF = s.REF AND s.DOS = x.DOS AND s.SREF1 = x.SREF1 AND s.SREF2 = x.SREF2
        WHERE sort = 1 AND a.HSDT IS NULL $fou $famille $y
        --GROUP BY a.TIERS, x.REF, x.SREF1, x.SREF2, a.DES, x.VENUN, x.DOS, s.CONF, a.FAM_0001
        )reponse
        WHERE conf <> 'Usrd'
        GROUP BY tiers, ref, sref1, sref2, designation, uv, dos, conf, famille
        ORDER BY famille
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Export des tarifs de vente de Divalto un code tarif par ligne
    public function tarifsVentesDivaltoUneColonneTarif($prefixe = null, $fous = null, $familles = null, $codes = null, $year = null): array
    {
        $pref = "";
        $fou = "";
        $famille = "";
        $y = "";
        $lesCodes = "";

        //dd($fous);

        if ($prefixe) {
            $prefixe = strtoupper($prefixe);
            $pref = "AND t.REF LIKE '$prefixe%'";
        }
        if ($fous) {
            $fou = "AND a.TIERS IN ($fous)";
        }
        if ($familles) {
            $famille = "AND a.FAM_0001 IN ($familles)";
        }
        if ($year) {
            $y = "AND YEAR(x.TADT) >= '$year'";
        }
        if ($codes) {
            $lesCodes = $this->statsAchatController->miseEnForme($codes);
        }

        $conn = $this->getEntityManager()->getConnection();

        // SQL Query
        $sql = "WITH LatestTarifs AS (
        -- On commence par obtenir les tarifs les plus récents pour chaque code tarif, référence et configuration
                    SELECT *,
                        ROW_NUMBER() OVER(PARTITION BY t.TACOD, t.REF, t.SREF1, t.SREF2 ORDER BY t.TADT DESC) AS sort
                    FROM TAR t
                    WHERE t.DOS = 1 AND t.TACOD IN ($lesCodes) $pref
                )

                SELECT * FROM(
                -- On filtre pour ne garder que les tarifs les plus récents
                SELECT
                    LTRIM(RTRIM(a.TIERS)) AS tiers,
                    LTRIM(RTRIM(x.REF)) AS ref,
                    LTRIM(RTRIM(s1.LIB)) AS sref1,
                    LTRIM(RTRIM(s2.LIB)) AS sref2,
                    LTRIM(RTRIM(a.DES)) AS designation,
                    LTRIM(RTRIM(u.LIB)) AS uv,
                    CASE
                        WHEN s.CONF = 'Usrd' AND a.SREFCOD = 2 THEN 'Usrd'
                        ELSE ''
                    END AS conf,
                    LTRIM(RTRIM(a.FAM_0001)) AS famille,
                    LTRIM(RTRIM(f.LIB)) AS libelle,
                    LTRIM(RTRIM(x.TACOD)) AS code,
                    x.TADT AS datePu,
                    x.PUB AS pu,
                    a.TVAART AS tva,
                    x.PPAR AS ppar
                FROM LatestTarifs x
                INNER JOIN ART a ON x.REF = a.REF AND a.DOS = x.DOS
                LEFT JOIN SART s ON x.REF = s.REF AND s.DOS = x.DOS AND s.SREF1 = x.SREF1 AND s.SREF2 = x.SREF2
                LEFT JOIN T012 f ON a.DOS = f.DOS AND a.FAM_0001 = f.FAM
                LEFT JOIN T019 s1 ON a.DOS = f.DOS AND x.SREF1 = s1.SREF1
                LEFT JOIN T019 s2 ON a.DOS = f.DOS AND x.SREF2 = s2.SREF1
                INNER JOIN T023 u ON a.DOS = u.DOS AND a.VENUN = u.REFUN
                WHERE x.sort = 1
                AND a.HSDT IS NULL
                AND a.FAM_0001 NOT IN ('', 'PRESTA', 'TRANSPOR', 'NC', 'LOCATION')
                $fou $famille $y
                ) rep
                WHERE conf <> 'Usrd'
                ORDER BY famille DESC;
                ";
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare($sql);
        // Bind parameters
        /*if ($prefixe) {
        $stmt->bindValue(':prefixe', $prefixe . '%');
        }

        if ($fous) {
        $stmt->bindValue(':fous', implode(',', $fous));
        }
        // Assuming $fous is an array
        if ($familles) {
        $stmt->bindValue(':familles', implode(',', $familles));
        }
        Assuming $familles is an array
        if ($year) {
        $stmt->bindValue(':year', $year, \PDO::PARAM_INT);
        }*/

        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
