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
                    SELECT ART.ART_ID AS Identification, ART.REF AS Ref, ART.DES AS Designation, ART.REFUN AS REFUM, ART.ACHUN AS ACHUN, ART.VENUN AS VENUN, ART.STUN AS STUN , ART.FAM_0001 AS FAM_0001, ART.FAM_0002 AS FAM_0002, ART.FAM_0003 AS FAM_0003,ART.DOS AS Dos,
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
        $sql = "SELECT ART.FAM_0002 AS Metier, MVTL_STOCK_V.REFERENCE,MVTL_STOCK_V.DOSSIER, MVTL_STOCK_V.SREFERENCE1 
        ,MVTL_STOCK_V.SREFERENCE2,MVTL_STOCK_V.ARTICLE_DESIGNATION ,SUM(MVTL_STOCK_V.QTETJSENSTOCK) AS StockDirect
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

}
