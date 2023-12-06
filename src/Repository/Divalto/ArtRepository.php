<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Art;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Art|null find($id, $lockMode = null, $lockVersion = null)
 * @method Art|null findOneBy(array $criteria, array $orderBy = null)
 * @method Art[]    findAll()
 * @method Art[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Art::class);
    }
    public function getControleArt(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Identification, Ref, Designation, REFUM, ACHUN, VENUN, STUN, FAM_0001, FAM_0002, FAM_0003,Utilisateur, MUSER.EMAIL AS Email
                    FROM(
                    SELECT RTRIM(LTRIM(ART.ART_ID)) AS Identification, RTRIM(LTRIM(ART.REF)) AS Ref, RTRIM(LTRIM(ART.DES)) AS Designation, RTRIM(LTRIM(ART.REFUN)) AS REFUM, RTRIM(LTRIM(ART.ACHUN)) AS ACHUN
                    , RTRIM(LTRIM(ART.VENUN)) AS VENUN, RTRIM(LTRIM(ART.STUN)) AS STUN , RTRIM(LTRIM(ART.FAM_0001)) AS FAM_0001, RTRIM(LTRIM(ART.FAM_0002)) AS FAM_0002, RTRIM(LTRIM(ART.FAM_0003)) AS FAM_0003,
                    RTRIM(LTRIM(ART.DOS)) AS Dos,
                    CASE
                    WHEN USERMO IS NOT NULL THEN USERMO
                    ELSE USERCR
                    END AS Utilisateur
                    FROM ART
                    WHERE ART.HSDT IS NULL AND
                    (
                    ART.REFUN <> ART.ACHUN
                    OR ART.REFUN <> ART.VENUN
                    OR ART.REFUN <> ART.STUN
                    OR ART.FAM_0001 IS NULL
                    OR ART.FAM_0002 IS NULL
                    OR ART.FAM_0003 IS NULL
                    OR ART.FAM_0001 = '0'
                    OR ART.FAM_0002 = '0'
                    OR ART.FAM_0003 = '0'
                    ))REPONSE
                    INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getControleStockDirect(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(ART.FAM_0002)) AS Metier, RTRIM(LTRIM(MVTL_STOCK_V.REFERENCE)) AS Ref,RTRIM(LTRIM(MVTL_STOCK_V.DOSSIER)) AS Dos, RTRIM(LTRIM(MVTL_STOCK_V.SREFERENCE1))  AS Sref1
        ,RTRIM(LTRIM(MVTL_STOCK_V.SREFERENCE2)) AS Sref2,RTRIM(LTRIM(MVTL_STOCK_V.ARTICLE_DESIGNATION)) AS Designation,SUM(MVTL_STOCK_V.QTETJSENSTOCK) AS StockDirect
        , MIN(MVTL_STOCK_V.UTILISATEURCREATION) AS Utilisateur,
        CASE
        WHEN ART.FAM_0002 IN ('ME','MO') THEN 'crichard@lhermitte.fr'
        WHEN ART.FAM_0002 IN ('HP', 'EV') THEN 'dlouchart@lhermitte.fr'
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'ndegorre@roby-fr.com'
        END AS Email,
        CASE
        WHEN ART.FAM_0002 IN ('ME','MO') THEN 'adeschodt@lhermitte.fr'
        WHEN ART.FAM_0002 IN ('HP', 'EV') THEN 'clerat@lhermitte.fr'
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'kkupczak@roby-fr.com'
        END AS Email2,
        CASE
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'marina@roby-fr.com'
        END AS Email3
        FROM MVTL_STOCK_V
        INNER JOIN ART ON ART.DOS = MVTL_STOCK_V.DOSSIER AND ART.REF = MVTL_STOCK_V.REFERENCE
        WHERE MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL AND MVTL_STOCK_V.NATURESTOCK NOT IN ('N', 'O')
        GROUP BY ART.FAM_0002, MVTL_STOCK_V.REFERENCE,MVTL_STOCK_V.DOSSIER, MVTL_STOCK_V.SREFERENCE1 ,MVTL_STOCK_V.SREFERENCE2,MVTL_STOCK_V.ARTICLE_DESIGNATION
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getControleStockDirectFiltre($metier, $dos): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM(SELECT RTRIM(LTRIM(ART.FAM_0002)) AS Metier, RTRIM(LTRIM(MVTL_STOCK_V.REFERENCE)) AS Ref,RTRIM(LTRIM(MVTL_STOCK_V.DOSSIER)) AS Dos, RTRIM(LTRIM(MVTL_STOCK_V.SREFERENCE1))  AS Sref1
        , RTRIM(LTRIM(MVTL_STOCK_V.SREFERENCE2)) AS Sref2, RTRIM(LTRIM(MVTL_STOCK_V.ARTICLE_DESIGNATION)) AS Designation, SUM(MVTL_STOCK_V.QTETJSENSTOCK) AS StockDirect, MIN(MVTL_STOCK_V.UTILISATEURCREATION) AS Utilisateur,
        CASE
        WHEN ART.FAM_0002 IN ('ME','MO') THEN 'crichard@lhermitte.fr'
        WHEN ART.FAM_0002 IN ('HP', 'EV') THEN 'dlouchart@lhermitte.fr'
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'ndegorre@roby-fr.com'
        END AS Email,
        CASE
        WHEN ART.FAM_0002 IN ('ME','MO') THEN 'adeschodt@lhermitte.fr'
        WHEN ART.FAM_0002 IN ('HP', 'EV') THEN 'clerat@lhermitte.fr'
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'kkupczak@roby-fr.com'
        END AS Email2,
        CASE
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'marina@roby-fr.com'
        END AS Email3
        FROM MVTL_STOCK_V
        INNER JOIN ART ON ART.DOS = MVTL_STOCK_V.DOSSIER AND ART.REF = MVTL_STOCK_V.REFERENCE
        WHERE MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL AND MVTL_STOCK_V.NATURESTOCK NOT IN ('N', 'O')
        GROUP BY ART.FAM_0002, MVTL_STOCK_V.REFERENCE,MVTL_STOCK_V.DOSSIER, MVTL_STOCK_V.SREFERENCE1 ,MVTL_STOCK_V.SREFERENCE2,MVTL_STOCK_V.ARTICLE_DESIGNATION)reponse
        WHERE Metier IN( $metier ) AND Dos = $dos
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Controle des articles à fermés tenant compte des réappro
    public function getControleArticleAFermer(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Dos,Metier, Ref, Sref1, Sref2, Designation,  SUM(Stock) AS Stock, SUM(Alerte) AS Alerte, SUM(Cmd) AS Cmd, SUM(Bl) AS Bl, Op, Hsdt, Blob, Identification, Utilisateur, Email
        FROM(
        SELECT LTRIM(RTRIM(LART.DOS)) AS Dos, LTRIM(RTRIM(LART.REF)) AS Ref, LTRIM(RTRIM(LART.SREF1)) AS Sref1, LTRIM(RTRIM(LART.SREF2)) AS Sref2,
        LTRIM(RTRIM(ART.DES)) AS Designation, MVTL_STOCK_V.QTETJSENSTOCK AS Stock, RSO.STALERTQTE AS Alerte,
        MOUV.CDQTE AS Cmd, MOUV.BLQTE AS Bl, LTRIM(RTRIM(MVTL.OP)) AS Op, LTRIM(RTRIM(ART.HSDT)) AS Hsdt,MAX(convert(varchar(max),MNOTE.NOTEBLOB)) AS Blob, LTRIM(RTRIM(ART.FAM_0002)) AS Metier,
        CASE
        WHEN LART.DOS <> '' THEN '999999999997'
        END AS Identification,
        CASE
        WHEN LART.DOS <> '' THEN 'Jérôme'
        END AS Utilisateur,
        CASE
        WHEN LART.DOS <> '' THEN 'jpochet@lhermitte.fr'
        END AS Email
        FROM LART
        LEFT JOIN ART ON LART.DOS = ART.DOS AND ART.REF = LART.REF -- Ramener les dates de fermetures
        LEFT JOIN RSO ON RSO.DOS = LART.DOS AND RSO.REF = LART.REF AND RSO.SREF1 = LART.SREF1 AND RSO.SREF2 = LART.SREF2 -- Ramener les réappros produits
        LEFT JOIN MNOTE ON MNOTE.NOTE = LART.NOTE_0010 -- Ramener les textes
        LEFT JOIN MVTL_STOCK_V ON LART.DOS = MVTL_STOCK_V.DOSSIER AND LART.REF = MVTL_STOCK_V.REFERENCE AND LART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND LART.SREF2 = MVTL_STOCK_V.SREFERENCE2 -- Ramener les Stocks
        LEFT JOIN MVTL ON MVTL.REF = LART.REF AND MVTL.DOS = LART.DOS AND MVTL.OP IN ('999') AND MVTL.CE2 = 1 AND MVTL.SREF1 = LART.SREF1 AND MVTL.SREF2 =  LART.SREF2 -- Ramener les Efs
        LEFT JOIN MOUV ON MOUV.DOS = LART.DOS AND MOUV.REF = LART.REF AND MOUV.SREF1 = LART.SREF1 AND MOUV.SREF2 = LART.SREF2 AND (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1) ) AND MOUV.TICOD IN ('C','F') AND (MOUV.CDNO > 0 OR MOUV.BLNO > 0) -- Ramener les Cmd et BL Clients et fournisseurs
        WHERE LART.NOTE_0010 IS NOT NULL AND ART.HSDT IS NULL AND ART.SREFCOD NOT IN (2)
        GROUP BY LART.DOS,ART.FAM_0002, LART.REF, LART.SREF1, LART.SREF2,ART.DES ,MVTL_STOCK_V.QTETJSENSTOCK, RSO.STALERTQTE,MOUV.CDQTE, MOUV.BLQTE,MVTL.OP, ART.HSDT)reponse
        WHERE (Stock IN (0) OR Stock IS NULL)
        AND Blob LIKE '%FERMETU%'
        AND (Cmd = 0 OR Cmd IS NULL)
        AND (Bl = 0 OR Bl IS NULL)
        AND Op IS NULL
        GROUP BY Dos, Metier, Ref, Sref1, Sref2, Designation, Op, Hsdt, Blob, Identification, Utilisateur, Email
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Controle des Sous références articles à fermées tenant compte des réappro
    public function getControleSousRefArticleAFermer(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Dos,Metier, Ref, Sref1, Sref2, Designation, SUM(Stock) AS Stock, SUM(Alerte) AS Alerte, SUM(Cmd) AS Cmd, SUM(Bl) AS Bl, Op, Conf, Hsdt, Blob, Identification, Utilisateur, Email
        FROM(
        SELECT LTRIM(RTRIM(LART.DOS)) AS Dos, LTRIM(RTRIM(LART.REF)) AS Ref, LTRIM(RTRIM(LART.SREF1)) AS Sref1, LTRIM(RTRIM(LART.SREF2)) AS Sref2,
		LTRIM(RTRIM(ART.DES)) AS Designation, MVTL_STOCK_V.QTETJSENSTOCK AS Stock, RSO.STALERTQTE AS Alerte,
		MOUV.CDQTE AS Cmd, MOUV.BLQTE AS Bl, LTRIM(RTRIM(MVTL.OP)) AS Op, LTRIM(RTRIM(SART.CONF)) AS Conf ,
		LTRIM(RTRIM(ART.HSDT)) AS Hsdt,MAX(convert(varchar(max),MNOTE.NOTEBLOB)) AS Blob, LTRIM(RTRIM(ART.FAM_0002)) AS Metier,
        CASE
        WHEN LART.DOS <> '' THEN '999999999996'
        END AS Identification,
        CASE
        WHEN LART.DOS <> '' THEN 'Jérôme'
        END AS Utilisateur,
        CASE
        WHEN LART.DOS <> '' THEN 'jpochet@lhermitte.fr'
        END AS Email
        FROM LART
        LEFT JOIN ART ON LART.DOS = ART.DOS AND ART.REF = LART.REF -- Ramener les dates de fermetures
        LEFT JOIN MNOTE ON MNOTE.NOTE = LART.NOTE_0010 -- Ramener les textes
        LEFT JOIN RSO ON RSO.DOS = LART.DOS AND RSO.REF = LART.REF AND RSO.SREF1 = LART.SREF1 AND RSO.SREF2 = LART.SREF2 -- Ramener les réappros produits
        LEFT JOIN SART ON SART.REF = LART.REF AND SART.DOS = LART.DOS AND SART.SREF1 = LART.SREF1 AND SART.SREF2 = LART.SREF2 -- Ramener les Conf
        LEFT JOIN MVTL_STOCK_V ON LART.DOS = MVTL_STOCK_V.DOSSIER AND LART.REF = MVTL_STOCK_V.REFERENCE AND LART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND LART.SREF2 = MVTL_STOCK_V.SREFERENCE2 -- Ramener les Stocks
        LEFT JOIN MVTL ON MVTL.REF = LART.REF AND MVTL.DOS = LART.DOS AND MVTL.OP IN ('999') AND MVTL.CE2 = 1 AND MVTL.SREF1 = LART.SREF1 AND MVTL.SREF2 =  LART.SREF2 -- Ramener les Efs
        LEFT JOIN MOUV ON MOUV.DOS = LART.DOS AND MOUV.REF = LART.REF AND MOUV.SREF1 = LART.SREF1 AND MOUV.SREF2 = LART.SREF2 AND (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1) ) AND MOUV.TICOD IN ('C','F') AND (MOUV.CDNO > 0 OR MOUV.BLNO > 0) -- Ramener les Cmd et BL Clients et fournisseurs
        WHERE LART.NOTE_0010 IS NOT NULL AND ART.HSDT IS NULL AND ART.SREFCOD = 2
        GROUP BY LART.DOS, LART.REF, LART.SREF1, LART.SREF2, ART.DES, MVTL_STOCK_V.QTETJSENSTOCK, RSO.STALERTQTE,MOUV.CDQTE, MOUV.BLQTE,MVTL.OP, SART.CONF, ART.HSDT, ART.FAM_0002)reponse
        WHERE (Stock IN (0) OR Stock IS NULL)
        AND Blob LIKE '%FERMETU%'
        AND (Cmd = 0 OR Cmd IS NULL)
        AND (Bl = 0 OR Bl IS NULL)
        AND Op IS NULL
        AND Conf NOT IN ('Usrd')
        GROUP BY Dos, Metier,Ref, Sref1, Sref2, Designation, Op, Conf, Hsdt, Blob, Identification, Utilisateur, Email
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function ControleToutesSrefFermeesArticle(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM(
            SELECT Identification, Utilisateur, Email, Dos, Ref, Hsdt, SUM(Nombre_Sref) AS NbSref, SUM(Nombre_Conf) AS NbConf
            FROM(
            SELECT LTRIM(RTRIM(SART.DOS)) AS Dos, LTRIM(RTRIM(SART.REF)) AS Ref, LTRIM(RTRIM(SART.SREF1)) AS Sref1, LTRIM(RTRIM(SART.SREF2)) AS Sref2, LTRIM(RTRIM(SART.CONF)) AS Conf, LTRIM(RTRIM(ART.HSDT)) AS Hsdt,
            CASE
            WHEN SART.REF <> '' THEN 999999999998
            END AS Identification,
            CASE
            WHEN SART.REF <> '' THEN 'JEROME'
            END AS Utilisateur,
            CASE
            WHEN SART.REF <> '' THEN 'jpochet@lhermitte.fr'
            END AS Email,
            CASE
            WHEN SART.REF <> '' THEN 1
            ELSE 0
            END AS Nombre_Sref,
            CASE
            WHEN SART.CONF IN ('Usrd') THEN 1
            ELSE 0
            END AS Nombre_Conf
            FROM SART
            LEFT JOIN ART ON ART.DOS = SART.DOS AND ART.REF = SART.REF
            WHERE ART.HSDT IS NULL AND (SART.SREF1 <> '' OR SART.SREF2 <> '') AND ART.SREFCOD = 2)reponse
            GROUP BY Identification, Utilisateur, Email, Dos, Ref, Hsdt)reponse2
            WHERE NbSref = NbConf
            ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function StockBlobMatiereDangeureuse(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT l.REF AS ref, l.SREF1 AS sref1, l.SREF2 AS sref2, a.DES AS designation, a.VENUN AS uv, l.EDCOD_0001 AS code,
        a.TIERS AS fournisseur, a.FAM_0001 AS famille, a.POIB AS poib, a.POIN AS poin,SUM(s.QTETJSENSTOCK) AS stock, l.NOTE_0001 AS note, n.NOTEBLOB AS blob
        FROM LART l
        INNER JOIN ART a ON a.REF = l.REF AND a.DOS = l.DOS
        INNER JOIN MNOTE n ON n.NOTE = l.NOTE_0001
        LEFT JOIN MVTL_STOCK_V s ON a.REF = s.REFERENCE AND l.SREF1 = s.SREFERENCE1 AND l.SREF2 = s.SREFERENCE2
        WHERE l.EDCOD_0001 <> '' AND l.DOS = 1 AND a.HSDT IS NULL
        GROUP BY l.REF, l.SREF1, l.SREF2, a.DES, a.VENUN,a.FAM_0001, a.POIB, a.POIN, l.EDCOD_0001, a.TIERS, l.NOTE_0001, n.NOTEBLOB
        ORDER BY a.TIERS
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function ArticlesOuvertBlob($search = null, $fous = null): array
    {
        if ($search) {
            $andSearch = "AND a.REF LIKE '$search%'";
        } else {
            $andSearch = "";
        }

        if ($fous) {
            $andFous = "AND a.TIERS IN ($fous)";
        } else {
            $andFous = "";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.TIERS AS tiers, a.REF AS ref, s.SREF1 AS sref1, s.SREF2 AS sref2, a.DES AS designation, a.VENUN AS uv, a.FAM_0001 AS famille, a.FAM_0002 AS metier,a.TVAART AS tva, n.NOTEBLOB AS blob, s.CONF AS conf, a.HSDT AS dateFermeture, SUM(st.QTETJSENSTOCK) AS stock
        FROM ART a
        LEFT JOIN SART s ON a.DOS = s.DOS AND a.REF = s.REF
        LEFT JOIN LART l ON a.DOS = l.DOS AND a.REF = l.REF AND s.SREF1 = l.SREF1 AND s.SREF2 = l.SREF2
        LEFT JOIN MNOTE n ON l.NOTE_0010 = n.NOTE
        LEFT JOIN MVTL_STOCK_V st ON a.REF = st.REFERENCE AND l.SREF1 = st.SREFERENCE1 AND l.SREF2 = st.SREFERENCE2
        WHERE a.DOS = 1 AND a.HSDT IS NULL  $andFous $andSearch
        GROUP BY a.TIERS, a.REF, s.SREF1, s.SREF2, a.DES, a.VENUN, n.NOTEBLOB, s.CONF, a.HSDT, a.FAM_0001, a.FAM_0002, a.TVAART
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getPhyto(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(ART.REF)) AS reference, RTRIM(LTRIM(ART.DES)) AS designation FROM ART
        WHERE DOS = 1 AND (ART.REF LIKE ('PPP%') OR ART.REF LIKE ('MAP%') OR ART.REF LIKE ('SYNG%')) AND ART.HSDT IS NULL
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
    public function getFamilleProduitOuvert(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT RTRIM(LTRIM(ART.FAM_0001)) AS famille
        FROM ART
        WHERE ART.HSDT IS NULL AND NOT ART.FAM_0001 IN ('ACCESS','ACOMPTE', 'COMMISSI', 'DIVERS','FIN','NC','PRESTA','REMISE','TAXE','TRANSPOR')
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
    public function getFamilleProduitOuvertParDossier($dos): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT RTRIM(LTRIM(ART.FAM_0001)) AS famille
        FROM ART
        WHERE ART.HSDT IS NULL AND NOT ART.FAM_0001 IN ('ACCESS','ACOMPTE', 'COMMISSI', 'DIVERS','FIN','NC','PRESTA','REMISE','TAXE','TRANSPOR') AND ART.DOS = '$dos'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getSrefByArt($dos, $prefixe): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, qteCmdCli, qteCmdFou, qteBlCli, SUM(m.BLQTE) AS qteBlFou
        FROM(
            SELECT dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, qteCmdCli, qteCmdFou, SUM(m.BLQTE) AS qteBlCli
            FROM(
                SELECT dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, qteCmdCli, SUM(m.CDQTE) AS qteCmdFou
                FROM(
                    SELECT dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, SUM(m.CDQTE) AS qteCmdCli
                    FROM(
                        SELECT s.DOS AS dos, LTRIM(RTRIM(s.REF)) AS ref, LTRIM(RTRIM(a.DES)) AS designation, LTRIM(RTRIM(s.SREF1)) AS sref1, LTRIM(RTRIM(s.SREF2)) AS sref2, LTRIM(RTRIM(a.VENUN)) AS uv,
                        LTRIM(RTRIM(a.FAM_0001)) AS famille, LTRIM(RTRIM(a.FAM_0002)) AS metier, a.TVAART AS tva, LTRIM(RTRIM(s.CONF)) AS conf, SUM(v.QTETJSENSTOCK) AS stock
                        FROM SART s
                        INNER JOIN ART a ON  a.REF = s.REF AND a.DOS = s.DOS
                        LEFT JOIN MVTL_STOCK_V v ON s.REF = v.REFERENCE AND s.SREF1 = v.SREFERENCE1 AND s.SREF2 = v.SREFERENCE2 AND s.DOS = v.DOSSIER
                        WHERE s.REF LIKE '$prefixe%' AND s.DOS = '$dos' AND NOT s.CONF IN ('Usrd') AND a.HSDT IS NULL
                        GROUP BY s.DOS, s.REF, a.DES, s.SREF1, s.SREF2,a.VENUN,a.FAM_0001 ,a.FAM_0002 , a.TVAART, s.CONF)repo
                    LEFT JOIN MOUV m ON ref = m.REF AND sref1 = m.SREF1 AND sref2 = m.SREF2 AND dos = m.DOS AND m.PICOD = 2 AND m.TICOD IN ('C') AND m.CDCE4 IN (1)
                    GROUP BY dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock
                    )reponse
                LEFT JOIN MOUV m ON ref = m.REF AND sref1 = m.SREF1 AND sref2 = m.SREF2 AND dos = m.DOS AND m.PICOD = 2 AND m.TICOD IN ('F') AND m.CDCE4 IN (1)
                GROUP BY dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, qteCmdCli)repon
            LEFT JOIN MOUV m ON ref = m.REF AND sref1 = m.SREF1 AND sref2 = m.SREF2 AND dos = m.DOS AND m.PICOD = 3 AND m.TICOD IN ('C') AND m.BLCE4 IN (1)
            GROUP BY dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, qteCmdCli, qteCmdFou)rep
        LEFT JOIN MOUV m ON ref = m.REF AND sref1 = m.SREF1 AND sref2 = m.SREF2 AND dos = m.DOS AND m.PICOD = 3 AND m.TICOD IN ('F') AND m.BLCE4 IN (1)
        GROUP BY dos, ref, designation, sref1, sref2,uv,famille,metier, tva, conf, stock, qteCmdCli, qteCmdFou, qteBlCli
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getEanStock($dos, $ean)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ref AS ref, sref1 AS sref1, sref2 AS sref2, designation AS designation, uv AS uv, ean AS ean, ferme AS ferme, SUM(stock) AS stock
        FROM(
        SELECT LTRIM(RTRIM(a.REF)) AS ref, LTRIM(RTRIM(s.SREF1)) AS sref1, LTRIM(RTRIM(s.SREF2)) AS sref2, LTRIM(RTRIM(a.DES)) AS designation, LTRIM(RTRIM(a.VENUN)) AS uv, m.QTETJSENSTOCK AS stock,
        CASE
        WHEN a.SREFCOD = 1 THEN LTRIM(RTRIM(a.EAN))
        WHEN a.SREFCOD = 2 THEN LTRIM(RTRIM(s.EAN)) --, a.HSDT AS ferme, s.EAN AS sean, s.USERMODH AS fermeSref
        END AS ean,
        CASE
        WHEN a.SREFCOD = 1 THEN LTRIM(RTRIM(a.HSDT))
        WHEN a.SREFCOD = 2 AND s.CONF = 'Usrd' THEN LTRIM(RTRIM(s.USERMODH)) --, a.HSDT AS ferme, s.EAN AS sean, s.USERMODH AS fermeSref
        END AS ferme
        FROM ART a
        LEFT JOIN SART s ON a.DOS = s.DOS AND a.REF = s.REF
        LEFT JOIN MVTL_STOCK_V m ON a.REF = m.REFERENCE AND s.SREF1 = m.SREFERENCE1 AND s.SREF2 = m.SREFERENCE2 AND m.NATURESTOCK IN ('N','O')
        WHERE a.DOS = $dos)reponse
        WHERE ean = '$ean'
		GROUP BY ref, sref1, sref2, designation, uv, ean, ferme
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

    public function getEmpl($dos, $emplacement)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(e.LIEU)) AS empl
        FROM T018 e
        WHERE e.DOS = $dos AND e.LIEU = '$emplacement'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    public function gettrancheEmpl($dos, $empl1, $empl2)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(e.LIEU)) AS empl
        FROM T018 e
        WHERE e.DOS = $dos AND e.LIEU BETWEEN '$empl1' AND '$empl2'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getSearchArt($dos, $produit, $type): array
    {
        if ($type == 'EAN') {
            $where = "ean = '$produit'";
        } elseif ($type == 'REF') {
            $where = "(ref LIKE '%$produit%' OR designation LIKE '%$produit%')";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ref AS ref, sref1 AS sref1, sref2 AS sref2, designation AS designation, uv AS uv, SUM(stock) AS stock, ean AS ean, ferme AS ferme, color AS color
        FROM(
        SELECT LTRIM(RTRIM(a.REF)) AS ref, LTRIM(RTRIM(s.SREF1)) AS sref1, LTRIM(RTRIM(s.SREF2)) AS sref2, LTRIM(RTRIM(a.DES)) AS designation, LTRIM(RTRIM(a.VENUN)) AS uv, m.QTETJSENSTOCK AS stock,
        CASE
        WHEN a.SREFCOD = 1 THEN LTRIM(RTRIM(a.EAN))
        WHEN a.SREFCOD = 2 THEN LTRIM(RTRIM(s.EAN)) --, a.HSDT AS ferme, s.EAN AS sean, s.USERMODH AS fermeSref
        END AS ean,
        CASE
        WHEN a.FAM_0002 = 'HP' THEN 'danger'
        WHEN a.FAM_0002 = 'EV' THEN 'success'
        WHEN a.FAM_0002 = 'ME' THEN 'warning'
        END AS color,
        CASE
        WHEN a.SREFCOD = 1 THEN LTRIM(RTRIM(a.HSDT))
        WHEN a.SREFCOD = 2 AND s.CONF = 'Usrd' THEN LTRIM(RTRIM(s.USERMODH)) --, a.HSDT AS ferme, s.EAN AS sean, s.USERMODH AS fermeSref
        END AS ferme
        FROM ART a
        LEFT JOIN SART s ON a.DOS = s.DOS AND a.REF = s.REF
        LEFT JOIN MVTL_STOCK_V m ON a.REF = m.REFERENCE AND s.SREF1 = m.SREFERENCE1 AND s.SREF2 = m.SREFERENCE2
        WHERE a.DOS = $dos)reponse
        WHERE $where
        GROUP BY ref, sref1, sref2, designation, uv, ean, ferme, color
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getAchatParProduit($dos, $produit): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.FADT AS dateFacture, REPLACE(LTRIM(RTRIM(m.REF)), '/', '#') AS ref, REPLACE(LTRIM(RTRIM(m.SREF1)), '/', '#') AS sref1, REPLACE(LTRIM(RTRIM(m.SREF2)), '/', '#') AS sref2, LTRIM(RTRIM(a.DES)) AS designation,  LTRIM(RTRIM(a.VENUN)) AS uv, LTRIM(RTRIM(a.FAM_0002)) AS metier,
        LTRIM(RTRIM(m.OP)) AS op, LTRIM(RTRIM(m.FAQTE)) AS qte, LTRIM(RTRIM(m.PUSTAT)) AS pa, m.PUSTAT * m.FAQTE AS montant
        FROM MOUV m
        INNER JOIN ART a ON a.REF = m.REF AND a.DOS = m.DOS
        WHERE  m.TICOD = 'F' AND m.PICOD = 4 AND m.DOS = $dos AND YEAR(m.FADT) <= YEAR(getdate()) -1
        AND m.OP IN ('F','FD', 'G', 'GD') AND m.REF LIKE '$produit%'

        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getAchatParProduitQteSign($dos, $produit, $sref1, $sref2): array
    {
        $code = '';
        if ($sref1 && !$sref2) {
            $code = "AND m.SREF1 ='" . $sref1 . "'";
        }
        if (!$sref1 && $sref2) {
            $code = "AND m.SREF2 ='" . $sref2 . "'";
        }
        if ($sref1 && $sref2) {
            $code = "AND m.SREF1 ='" . $sref1 . "'" . "AND m.SREF2 ='" . $sref2 . "'";
        }

        $produit = str_replace('#', '/', $produit);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.FADT AS dateFacture, REPLACE(LTRIM(RTRIM(m.REF)), '/', '#') AS ref, REPLACE(LTRIM(RTRIM(m.SREF1)), '/', '#') AS sref1, REPLACE(LTRIM(RTRIM(m.SREF2)), '/', '#') AS sref2, LTRIM(RTRIM(m.DES)) AS designation, LTRIM(RTRIM(m.VENUN)) AS uv, LTRIM(RTRIM(m.PUSTAT)) AS pu, LTRIM(RTRIM(a.FAM_0002)) AS metier,
        CASE
        WHEN m.OP IN ('F', 'FD') THEN m.FAQTE
        WHEN m.OP IN ('G', 'GD') THEN -1 * m.FAQTE
        END AS qte
        FROM MOUV m
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = $dos AND m.PICOD = 4 AND m.TICOD = 'F' AND m.REF = '$produit' AND YEAR(m.FADT) <= YEAR(getdate()) -1 AND m.OP IN ('F', 'G')
        $code
        ORDER BY m.FADT DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getAchatParProduitQteSignDepotDirect($dos, $produit, $sref1, $sref2): array
    {
        $code = '';
        if ($sref1 && !$sref2) {
            $code = "AND m.SREF1 ='" . $sref1 . "'";
        }
        if (!$sref1 && $sref2) {
            $code = "AND m.SREF2 ='" . $sref2 . "'";
        }
        if ($sref1 && $sref2) {
            $code = "AND m.SREF1 ='" . $sref1 . "'" . "AND m.SREF2 ='" . $sref2 . "'";
        }

        $produit = str_replace('#', '/', $produit);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.FADT AS dateFacture, REPLACE(LTRIM(RTRIM(m.REF)), '/', '#') AS ref, REPLACE(LTRIM(RTRIM(m.SREF1)), '/', '#') AS sref1, REPLACE(LTRIM(RTRIM(m.SREF2)), '/', '#') AS sref2, LTRIM(RTRIM(m.DES)) AS designation, LTRIM(RTRIM(m.VENUN)) AS uv, LTRIM(RTRIM(m.PUSTAT)) AS pu, LTRIM(RTRIM(a.FAM_0002)) AS metier,
        CASE
        WHEN m.OP IN ('F', 'FD') THEN m.FAQTE
        WHEN m.OP IN ('G', 'GD') THEN -1 * m.FAQTE
        END AS qte
        FROM MOUV m
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = $dos AND m.PICOD = 4 AND m.TICOD = 'F' AND m.REF = '$produit' AND YEAR(m.FADT) <= YEAR(getdate()) -1 AND m.OP IN ('F', 'G', 'FD','GD')
        $code
        ORDER BY m.FADT DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getAchatParProduitQteSignDepotDirectDernier($dos, $produit, $sref1, $sref2): array
    {
        $code = '';
        if ($sref1 && !$sref2) {
            $code = "AND m.SREF1 ='" . $sref1 . "'";
        }
        if (!$sref1 && $sref2) {
            $code = "AND m.SREF2 ='" . $sref2 . "'";
        }
        if ($sref1 && $sref2) {
            $code = "AND m.SREF1 ='" . $sref1 . "'" . "AND m.SREF2 ='" . $sref2 . "'";
        }

        $produit = str_replace('#', '/', $produit);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT TOP 1 m.FADT AS dateFacture, REPLACE(LTRIM(RTRIM(m.REF)), '/', '#') AS ref, REPLACE(LTRIM(RTRIM(m.SREF1)), '/', '#') AS sref1, REPLACE(LTRIM(RTRIM(m.SREF2)), '/', '#') AS sref2, LTRIM(RTRIM(m.DES)) AS designation, LTRIM(RTRIM(m.VENUN)) AS uv, LTRIM(RTRIM(m.PUSTAT)) AS pu, LTRIM(RTRIM(a.FAM_0002)) AS metier,
        CASE
        WHEN m.OP IN ('F', 'FD') THEN m.FAQTE
        WHEN m.OP IN ('G', 'GD') THEN -1 * m.FAQTE
        END AS qte
        FROM MOUV m
        INNER JOIN ART a ON a.DOS = m.DOS AND a.REF = m.REF
        WHERE m.DOS = $dos AND m.PICOD = 4 AND m.TICOD = 'F' AND m.REF = '$produit' AND YEAR(m.FADT) <= YEAR(getdate()) -1 AND m.OP IN ('F', 'G', 'FD','GD')
        $code
        ORDER BY m.FADT DESC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getListCode()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(TACOD)) AS tacod
        FROM T014
        WHERE T014.DOS = 1 AND TICOD = 'C'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
