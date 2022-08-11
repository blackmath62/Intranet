<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Fou;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fou|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fou|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fou[]    findAll()
 * @method Fou[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FouRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fou::class);
    }

    public function getControleRegimeFournisseur():array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT Identification,Tiers, Nom, Pays, RegimeTiers, Dos, Utilisateur, MUSER.EMAIL AS Email
        FROM(
        SELECT FOU.FOU_ID AS Identification, FOU.TIERS AS Tiers, FOU.NOM AS Nom, FOU.PAY AS Pays, FOU.TVATIE AS RegimeTiers, FOU.DOS AS Dos,
        CASE
        WHEN USERMO IS NOT NULL THEN USERMO
        ELSE USERCR
        END AS Utilisateur
        FROM FOU 
        WHERE 
        FOU.HSDT IS NULL AND FOU.DOS IN (1,3) AND(
        FOU.PAY = 'FR' AND FOU.TVATIE NOT IN ('0','01')
        OR FOU.PAY IN('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','GR','HR','HU','IRL','IT','IE','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK') AND FOU.TVATIE NOT IN ('1','5')
        OR FOU.PAY NOT IN('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','GR','HR','HU','IRL', 'IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK','FR') AND FOU.TVATIE NOT IN ('2')
        ))reponse
        INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFournisseurDivalto():array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT LTRIM(RTRIM(FOU.TIERS)) AS tiers, LTRIM(RTRIM(FOU.NOM)) AS nom
        FROM FOU
        WHERE FOU.DOS = 1 AND FOU.HSDT IS NULL
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function SurveillanceFournisseurLhermitteReglStatVrpTransVisaTvaPay():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Identification, Dos,TIERS, Nom, Pays, RegimeTVA, RFCCTRCOD,VISA,STAT_0002,SALCOD, Utilisateur, MUSER.EMAIL AS Email
        FROM(
        SELECT FOU.FOU_ID AS Identification, FOU.DOS AS Dos, FOU.TIERS AS TIERS, FOU.NOM AS Nom,FOU.PAY AS Pays, FOU.TVATIE AS RegimeTVA, FOU.RFCCTRCOD AS RFCCTRCOD,FOU.VISA AS VISA,FOU.STAT_0002 AS STAT_0002,FOU.SALCOD AS SALCOD,
                CASE
                WHEN FOU.USERMO IS NOT NULL AND FOU.USERMO <> '' THEN FOU.USERMO
                ELSE FOU.USERCR
                END AS Utilisateur
        FROM FOU
        WHERE FOU.HSDT IS NULL AND FOU.DOS IN (1,3) AND (
        FOU.RFCCTRCOD NOT IN (1)
        OR FOU.VISA NOT IN (2)
        OR FOU.TVATIE = ''
        OR FOU.TVATIE IS NULL
        OR FOU.PAY = ''
        OR FOU.PAY IS NULL
        ))reponse
        INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
