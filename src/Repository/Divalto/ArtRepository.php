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
    public function getControleArt():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Identification, Ref, Designation, REFUM, ACHUN, VENUN, STUN, FAM_0001, FAM_0002, FAM_0003,Utilisateur, MUSER.EMAIL AS Email
                    FROM(
                    SELECT RTRIM(LTRIM(ART.ART_ID)) AS Identification, RTRIM(LTRIM(ART.REF)) AS Ref, RTRIM(LTRIM(ART.DES)) AS Designation, RTRIM(LTRIM(ART.REFUN)) AS REFUM, RTRIM(LTRIM(ART.ACHUN)) AS ACHUN
                    , RTRIM(LTRIM(ART.VENUN)) AS VENUN, RTRIM(LTRIM(ART.STUN)) AS STUN , RTRIM(LTRIM(ART.FAM_0001)) AS FAM_0001, RTRIM(LTRIM(ART.FAM_0002)) AS FAM_0002, RTRIM(LTRIM(ART.FAM_0003)) AS FAM_0003,RTRIM(LTRIM(ART.DOS)) AS Dos,
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
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getControleStockDirect():array
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
        WHEN ART.FAM_0002 NOT IN ('ME', 'MO', 'HP', 'EV') THEN 'obue@roby-fr.com'
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
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Controle des articles à fermés tenant compte des réappro
    public function getControleArticleAFermer():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Dos,Ref, Sref1, Sref2, Designation,  SUM(Stock) AS Stock, SUM(Alerte) AS Alerte, SUM(Cmd) AS Cmd, SUM(Bl) AS Bl, Op, Hsdt, Blob, Identification, Utilisateur, Email
        FROM(
        SELECT LART.DOS AS Dos, LART.REF AS Ref, LART.SREF1 AS Sref1, LART.SREF2 AS Sref2, ART.DES AS Designation,MVTL_STOCK_V.QTETJSENSTOCK AS Stock, RSO.STALERTQTE AS Alerte,MOUV.CDQTE AS Cmd, MOUV.BLQTE AS Bl, MVTL.OP AS Op, ART.HSDT AS Hsdt,MAX(convert(varchar(max),MNOTE.NOTEBLOB)) AS Blob,
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
        GROUP BY LART.DOS, LART.REF, LART.SREF1, LART.SREF2,ART.DES ,MVTL_STOCK_V.QTETJSENSTOCK, RSO.STALERTQTE,MOUV.CDQTE, MOUV.BLQTE,MVTL.OP, ART.HSDT)reponse
        WHERE (Stock IN (0) OR Stock IS NULL)
        AND Blob LIKE '%FERMETU%'
        AND (Cmd = 0 OR Cmd IS NULL) 
        AND (Bl = 0 OR Bl IS NULL) 
        AND Op IS NULL 
        GROUP BY Dos,Ref, Sref1, Sref2, Designation, Op, Hsdt, Blob, Identification, Utilisateur, Email
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Controle des Sous références articles à fermées tenant compte des réappro
    public function getControleSousRefArticleAFermer():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Dos,Ref, Sref1, Sref2, Designation, SUM(Stock) AS Stock, SUM(Alerte) AS Alerte, SUM(Cmd) AS Cmd, SUM(Bl) AS Bl, Op, Conf, Hsdt, Blob, Identification, Utilisateur, Email
        FROM(
        SELECT LART.DOS AS Dos, LART.REF AS Ref, LART.SREF1 AS Sref1, LART.SREF2 AS Sref2, ART.DES AS Designation, MVTL_STOCK_V.QTETJSENSTOCK AS Stock, RSO.STALERTQTE AS Alerte,MOUV.CDQTE AS Cmd, MOUV.BLQTE AS Bl, MVTL.OP AS Op, SART.CONF AS Conf , ART.HSDT AS Hsdt,MAX(convert(varchar(max),MNOTE.NOTEBLOB)) AS Blob,
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
        GROUP BY LART.DOS, LART.REF, LART.SREF1, LART.SREF2, ART.DES, MVTL_STOCK_V.QTETJSENSTOCK, RSO.STALERTQTE,MOUV.CDQTE, MOUV.BLQTE,MVTL.OP, SART.CONF, ART.HSDT)reponse
        WHERE (Stock IN (0) OR Stock IS NULL)
        AND Blob LIKE '%FERMETU%'
        AND (Cmd = 0 OR Cmd IS NULL) 
        AND (Bl = 0 OR Bl IS NULL) 
        AND Op IS NULL 
        AND Conf NOT IN ('Usrd')
        GROUP BY Dos,Ref, Sref1, Sref2, Designation, Op, Conf, Hsdt, Blob, Identification, Utilisateur, Email
        ORDER BY Ref
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function ControleToutesSrefFermeesArticle():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM(
            SELECT Identification, Utilisateur, Email, Dos, Ref, Hsdt, SUM(Nombre_Sref) AS NbSref, SUM(Nombre_Conf) AS NbConf
            FROM(
            SELECT SART.DOS AS Dos, SART.REF AS Ref, SART.SREF1 AS Sref1, SART.SREF2 AS Sref2, SART.CONF AS Conf, ART.HSDT AS Hsdt,
            CASE
            WHEN SART.REF <> '' THEN 999999999998
            END AS Identification,
            CASE
            WHEN SART.REF <> '' THEN 'Jérôme'
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
        $stmt->execute();
        return $stmt->fetchAll();
    }

    

}
