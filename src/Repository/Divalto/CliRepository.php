<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Cli;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cli|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cli|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cli[]    findAll()
 * @method Cli[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CliRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cli::class);
    }

    public function SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(CLI.CLI_ID)) AS Identification, RTRIM(LTRIM(CLI.TIERS)) AS TIERS, RTRIM(LTRIM(CLI.NOM)) AS NOM, RTRIM(LTRIM(CLI.REGL)) AS REGL, RTRIM(LTRIM(CLI.STAT_0001)) AS STAT_0001,
        RTRIM(LTRIM(CLI.STAT_0002)) AS STAT_0002, RTRIM(LTRIM(CLI.STAT_0003)) AS STAT_0003, RTRIM(LTRIM(CLI.REPR_0001)) AS REPR_0001, RTRIM(LTRIM(CLI.BLMOD)) AS BLMOD, RTRIM(LTRIM(CLI.VISA)) AS VISA, RTRIM(LTRIM(CLI.TVATIE)) AS TVATIE, RTRIM(LTRIM(CLI.PAY)) AS PAY,RTRIM(LTRIM(CLI.HSDT)),
        CASE
        WHEN CLI.USERMO IS NOT NULL AND USERMO = 'VIVIEN' THEN 'VIVIEN'
        WHEN CLI.USERMO IS NULL AND CLI.USERCR = 'VIVIEN' THEN 'VIVIEN'
        ELSE 'JEROME'
        END AS Utilisateur,
        CASE
        WHEN CLI.USERMO IS NOT NULL AND USERMO = 'VIVIEN' THEN 'vlesenne@lhermitte.fr'
        WHEN CLI.USERMO IS NULL AND CLI.USERCR = 'VIVIEN' THEN 'vlesenne@lhermitte.fr'
        ELSE 'jpochet@lhermitte.fr'
        END AS Email
        FROM CLI
        WHERE CLI.DOS = 1 AND CLI.HSDT IS NULL
        AND (
        CLI.REGL IS NULL 
        OR CLI.STAT_0001 IS NULL
        OR CLI.STAT_0002 IS NULL 
        OR CLI.STAT_0003 IS NULL 
        OR CLI.REPR_0001 IS NULL
        OR CLI.BLMOD IS NULL
        OR CLI.REGL = ''
        OR CLI.STAT_0001 = ''
        OR CLI.STAT_0001 = '0' 
        OR CLI.STAT_0002 = ''
        OR CLI.STAT_0002 = '0'
        OR CLI.STAT_0003 = ''
        OR CLI.STAT_0003 = '0'
        OR CLI.REPR_0001 = ''
        OR CLI.REPR_0001 = '0'
        OR CLI.BLMOD = ''
        OR CLI.BLMOD = '0'
        OR CLI.VISA NOT IN (2)
        OR CLI.PAY = ''
        OR CLI.PAY = '0'
        )";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function SendMailMajCertiphytoClient():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CLI_ID AS Identification, CLI.TIERS AS Tiers, CLI.NOM AS Nom, VRP.EMAIL AS Email, VRP.SELCOD AS Utilisateur 
        FROM CLI
        LEFT JOIN VRP ON VRP.DOS = CLI.DOS AND VRP.TIERS = CLI.REPR_0001
        WHERE CLI.HSDT IS NULL AND CLI.DOS = 1 AND CLI.UP_PH_AUTORISE = 2 AND CLI.UP_PH_DECID_OBLIG = 1 AND CLI.TIERS NOT IN ('C0218400')
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function SendMailProblemePaysRegimeClients():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Identification,Tiers, Nom, Pays, RegimeTiers, Dos, Utilisateur, MUSER.EMAIL AS Email
        FROM(
        SELECT CLI.CLI_ID AS Identification, CLI.TIERS AS Tiers, CLI.NOM AS Nom, CLI.PAY AS Pays, CLI.TVATIE AS RegimeTiers, CLI.DOS AS Dos,
        CASE
        WHEN USERMO IS NOT NULL THEN USERMO
        ELSE USERCR
        END AS Utilisateur
        FROM CLI 
        WHERE 
        CLI.HSDT IS NULL AND CLI.DOS IN (1,3) AND(
        CLI.PAY = 'FR' AND CLI.TVATIE NOT IN ('0','01')
        OR CLI.PAY IN('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','GR','HR','HU','IRL','IT','IE','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK') AND CLI.TVATIE NOT IN ('1','5')
        OR CLI.PAY NOT IN('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','GR','HR','HU','IRL', 'IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK','FR') AND CLI.TVATIE NOT IN ('2')
        ))reponse
        INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function MesClients($commercial):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CLI.TIERS AS codeTier, CLI.NOM AS nom, CLI.RUE AS rue, CLI.CPOSTAL AS cp, CLI.VIL AS ville, CLI.TEL AS tel, CLI.EMAIL AS mail FROM CLI
        WHERE CLI.REPR_0001 = $commercial AND CLI.HSDT IS NULL
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }



    
}
