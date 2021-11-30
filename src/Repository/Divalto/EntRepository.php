<?php

namespace App\Repository\Divalto;

use DateTime;
use App\Entity\Divalto\Ent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ent[]    findAll()
 * @method Ent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ent::class);
    }
    // Controle des vielles commandes actives dans le systéme
    public function getOldCmds($dos, $numeros):array
    {
        $d = new DateTime();
        $d->modify('-12 month');
        $d = $d->format('Y/m/d');
    
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Dos, Identification, Tiers,Nom, Cmd, DateCmd, Commercial, SUM(CompteurEvHp) AS CompteurHp, SUM(CompteurMe) AS CompteurMe, Utilisateur, MUSER.EMAIL AS Email FROM(
            SELECT ENT.DOS AS Dos, ENT.ENT_ID AS Identification, ENT.TIERS AS Tiers, CLI.NOM AS Nom, ENT.PINO AS Cmd, ENT.PIDT AS DateCmd, ART.FAM_0002, VRP.SELCOD, ENT.USERCR, ENT.USERMO,
            CASE
                WHEN ART.FAM_0002 IN ('ME', 'MO') AND ENT.DOS = 1 THEN 1
            END AS CompteurMe,
            CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND ENT.DOS = 1 THEN 1
            END AS CompteurEvHp,
            CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND ENT.DOS = 1 THEN VRP.SELCOD
                WHEN ART.FAM_0002 IN ('ME', 'MO') AND ENT.DOS = 1 THEN 'Alexandre Deschodt'
                WHEN ENT.DOS = 3 THEN VRP.SELCOD
                ELSE VRP.SELCOD
            END AS Commercial,
            CASE
                WHEN ENT.USERMO IS NOT NULL THEN ENT.USERMO
                ELSE ENT.USERCR
            END AS Utilisateur
            FROM ENT
            INNER JOIN CLI ON ENT.TIERS = CLI.TIERS AND ENT.DOS = CLI.DOS
            INNER JOIN VRP ON VRP.DOS = ENT.DOS AND VRP.TIERS = CLI.REPR_0001
            INNER JOIN MOUV ON MOUV.DOS = ENT.DOS AND MOUV.CDNO = ENT.PINO AND MOUV.CDCE4 = 1
            INNER JOIN ART ON ART.DOS = ENT.DOS AND ART.REF = MOUV.REF
            WHERE ENT.PICOD = 2 AND ENT.CE4 = 1 AND PIDT <= '$d' AND ENT.TICOD = 'C' AND ENT.DOS IN($dos) AND ENT.PINO NOT IN ($numeros)
            GROUP BY ENT.DOS, ENT.ENT_ID, ENT.TIERS, CLI.NOM, ENT.PINO, ENT.PIDT, ART.FAM_0002, VRP.SELCOD, ENT.USERCR, ENT.USERMO)reponse
            INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
            GROUP BY Dos, Identification, Tiers,Nom, Cmd, DateCmd, Commercial, Utilisateur, MUSER.EMAIL
            ORDER BY DateCmd
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getOldCmdsMouv($dos, $numeros):array
    {
        $d = new DateTime();
        $d->modify('-12 month');
        $d = $d->format('Y/m/d');
    
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Dos, Tiers,Nom, Cmd, DateCmd,Famille, Ref, Sref1, Sref2, Designation, Qte, Utilisateur, MUSER.EMAIL AS Email FROM(
            SELECT ENT.DOS AS Dos, ENT.ENT_ID AS Identification, ENT.TIERS AS Tiers, CLI.NOM AS Nom, ENT.PINO AS Cmd, ENT.PIDT AS DateCmd, MOUV.REF AS Ref, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2, MOUV.DES AS Designation,MOUV.CDQTE AS Qte, ART.FAM_0002 AS Famille, VRP.SELCOD, ENT.USERCR, ENT.USERMO,
            CASE
                WHEN ART.FAM_0002 IN ('ME', 'MO') AND ENT.DOS = 1 THEN 1
            END AS CompteurMe,
            CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND ENT.DOS = 1 THEN 1
            END AS CompteurEvHp,
            CASE
                WHEN ART.FAM_0002 IN ('EV', 'HP') AND ENT.DOS = 1 THEN VRP.SELCOD
                WHEN ART.FAM_0002 IN ('ME', 'MO') AND ENT.DOS = 1 THEN 'Alexandre Deschodt'
                WHEN ENT.DOS = 3 THEN VRP.SELCOD
                ELSE VRP.SELCOD
            END AS Commercial,
            CASE
                WHEN ENT.USERMO IS NOT NULL THEN ENT.USERMO
                ELSE ENT.USERCR
            END AS Utilisateur
            FROM ENT
            INNER JOIN CLI ON ENT.TIERS = CLI.TIERS AND ENT.DOS = CLI.DOS
            INNER JOIN VRP ON VRP.DOS = ENT.DOS AND VRP.TIERS = CLI.REPR_0001
            INNER JOIN MOUV ON MOUV.DOS = ENT.DOS AND MOUV.CDNO = ENT.PINO AND MOUV.CDCE4 = 1
            INNER JOIN ART ON ART.DOS = ENT.DOS AND ART.REF = MOUV.REF
            WHERE ENT.PICOD = 2 AND ENT.CE4 = 1 AND PIDT <= '2020/11/17' AND ENT.TICOD = 'C' AND ENT.DOS IN($dos) AND ENT.PINO NOT IN ($numeros)
            GROUP BY ENT.DOS, ENT.ENT_ID, ENT.TIERS, CLI.NOM, ENT.PINO, ENT.PIDT,MOUV.REF, MOUV.SREF1, MOUV.SREF2, MOUV.DES, MOUV.CDQTE, ART.FAM_0002, VRP.SELCOD, ENT.USERCR, ENT.USERMO)reponse
            INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
            GROUP BY Dos, Identification, Tiers,Nom,Famille, Ref, Sref1, Sref2, Designation, Qte, Cmd, DateCmd, Commercial, Utilisateur, MUSER.EMAIL
            ORDER BY DateCmd
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // lancer les mises à jour des commandes Roby présentent dans divalto
    public function majCmdsRobyDelaiAccepteReporte():array
    {    
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(ENT.ENT_ID)) AS Identification, LTRIM(RTRIM(ENT.TIERS)) AS Tiers, LTRIM(RTRIM(CLI.NOM)) AS Nom, LTRIM(RTRIM(CLI.TEL)) AS Tel, LTRIM(RTRIM(ENT.PINO)) AS Cmd, ENT.PIDT AS DateCmd, LTRIM(RTRIM(ENT.PIREF)) AS NotreRef,  ENT.DELACCDT AS DelaiAccepte, ENT.DELREPDT AS DelaiReporte
        FROM ENT 
        INNER JOIN CLI ON ENT.DOS = CLI.DOS AND ENT.TIERS = CLI.TIERS
        WHERE ENT.DOS = 3 AND ENT.PICOD = 2 AND ENT.CE4 = 1 AND ENT.TICOD = 'C'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // vérifier le statut d'une commande dans divalto (CE4)
    public function controleStatusOfCmd($cmd)
    {    
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.CE4 FROM ENT WHERE ENT.DOS = 3 AND ENT.PINO = $cmd AND ENT.TICOD = 'C' AND ENT.PICOD = 2
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    

}
