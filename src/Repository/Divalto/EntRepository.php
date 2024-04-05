<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Ent;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ent[]    findAll()
 * @method Ent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntRepository extends ServiceEntityRepository
{
    private $artBanPreparation;
    private $fam1BanPreparation;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ent::class);
        $this->artBanPreparation = "'DIVDALBETON','RDDTOTAL20%','RIDIVERS','ASSTOTAL','STATOTAL20','ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP','ECOCONTRIBUTION10', 'ECOCONTRIBUTION10EV', 'ECOCONTRIBUTION20', 'DIVPRESTM','DIMTOTAL', 'RITOTAL', 'AFTOTAL'";
        $this->fam1BanPreparation = "'TRANSPOR', 'ACOMPTE', 'LOCATION', 'PRESTA'";
    }
    // Controle des vielles commandes actives dans le systéme
    public function getOldCmds($dos, $numeros): array
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
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
    public function getOldCmdsMouv($dos, $numeros): array
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
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
    // lancer les mises à jour des commandes Roby présentent dans divalto
    public function majCmdsRobyDelaiAccepteReporte(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(ENT.ENT_ID)) AS Identification, LTRIM(RTRIM(ENT.TIERS)) AS Tiers, LTRIM(RTRIM(CLI.NOM)) AS Nom,
        LTRIM(RTRIM(CLI.TEL)) AS Tel, LTRIM(RTRIM(ENT.PINO)) AS Cmd, ENT.PIDT AS DateCmd, LTRIM(RTRIM(ENT.PIREF)) AS NotreRef,
        ENT.DELACCDT AS DelaiAccepte, ENT.DELREPDT AS DelaiReporte, ENT.HTPDTMT AS ht
                FROM ENT
                INNER JOIN CLI ON ENT.DOS = CLI.DOS AND ENT.TIERS = CLI.TIERS
                WHERE ENT.DOS = 3 AND ENT.PICOD = 2 AND ENT.CE4 = 1 AND ENT.TICOD = 'C'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Obtenir la liste des commandes à traiter pour préparation de commande
    public function getListMouvPreparationCmd($filtreCmds): array
    {
        $and = "";
        if ($filtreCmds) {
            $and = " AND e.PINO IN (" . $filtreCmds . ")";
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT
        e.TIERS AS tiers,
        c.NOM AS nom,
        e.PINO AS cmd,
        e.PIDT AS dateCmd,
        e.PIREF AS notreREF,
        e.USERCR AS userCr,
        e.OP AS op,
        e.DELDEMDT AS delaiDemande,
        e.DELACCDT AS delaiAccepte,
        e.DELREPDT AS delaiReporte,
        nD.NOTEBLOB AS nDb,
        nF.NOTEBLOB AS nFb,
        COUNT(*) AS nbP,
        SUM(CASE WHEN a.FAM_0002 = 'ME' THEN 1 ELSE 0 END) AS nbP_ME,
        SUM(CASE WHEN a.FAM_0002 = 'HP' THEN 1 ELSE 0 END) AS nbP_HP,
        SUM(CASE WHEN a.FAM_0002 = 'EV' THEN 1 ELSE 0 END) AS nbP_EV
    FROM
        ENT e
    INNER JOIN
        CLI c WITH (INDEX = INDEX_C_CLI) ON c.DOS = e.DOS AND c.TIERS = e.TIERS
    INNER JOIN
        MOUV m WITH (INDEX = INDEX_I) ON m.DOS = e.DOS AND e.TICOD = m.TICOD AND e.PINO = m.CDNO
    INNER JOIN
        ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = e.DOS AND a.REF = m.REF
    LEFT JOIN
        MNOTE nD ON nD.NOTE = e.TXTNOTED
    LEFT JOIN
        MNOTE nF ON nF.NOTE = e.TXTNOTEF
    WHERE
        e.DOS = 1
        AND e.PICOD = 2
        AND e.TICOD = 'C'
        AND e.CE4 = 1
        AND e.OP IN ('C', 'D')
        AND NOT a.REF IN ($this->artBanPreparation)
        AND NOT a.FAM_0001 IN ($this->fam1BanPreparation) AND NOT a.FAM_0002 IN ('MO')
        AND m.CDQTE <> 0
        AND m.CE8 <> 1 AND m.OP IN ('C','D')
        $and
    GROUP BY
        e.TIERS , c.NOM, e.PINO, e.PIDT, e.PIREF, e.USERCR, e.OP,
        e.DELDEMDT, e.DELACCDT, e.DELREPDT, nD.NOTEBLOB, nF.NOTEBLOB
    ORDER BY e.PINO ASC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getTextHeaderAndFooter($cmd): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT nD.NOTEBLOB AS nDb, nF.NOTEBLOB AS nFb
            FROM ENT e
            LEFT JOIN MNOTE nD ON nD.NOTE = e.TXTNOTED
            LEFT JOIN MNOTE nF ON nF.NOTE = e.TXTNOTEF
            WHERE e.PINO = $cmd AND e.TICOD = 'C' AND e.DOS = 1 AND e.PICOD = 2
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative() ?: ['nDb' => null, 'nFb' => null];
    }
    // quantité en stock d'un EAN sur un emplacement
    public function getQteEanInLocation($dos, $ean, $empl)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SUM(qteStock) qteStock
        FROM(
        SELECT LTRIM(RTRIM(st.REFERENCE)) AS ref, LTRIM(RTRIM(st.SREFERENCE1)) AS sref1, LTRIM(RTRIM(st.SREFERENCE2)) AS sref2,
        LTRIM(RTRIM(a.DES)) AS designation, LTRIM(RTRIM(a.VENUN)) AS uv,LTRIM(RTRIM(st.EMPLACEMENT)) AS empl,
        LTRIM(RTRIM(nat.LIB)) AS natureStock, st.QTETJSENSTOCK AS qteStock,LTRIM(RTRIM(ean.EAN)) AS ean
        FROM MVTL_STOCK_V AS st
        LEFT JOIN SARTEAN ean ON st.DOSSIER = ean.DOS AND st.REFERENCE = ean.REF AND st.SREFERENCE1 = ean.SREF1 AND st.SREFERENCE2 = ean.SREF2
        INNER JOIN ART a ON a.DOS = st.DOSSIER AND a.REF = st.REFERENCE
        LEFT JOIN T025 nat ON st.NATURESTOCK = nat.NST
        WHERE st.DOSSIER = $dos)rep
        WHERE ean = '$ean' AND empl = '$empl'
        GROUP BY ref, sref1, sref2, designation, uv, empl, natureStock, ean
        ORDER BY ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // Liste des produits à préparer sur la commande
    public function getMouvPreparationCmdList($cdno): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.CDNO AS cdNo, m.TIERS AS tiers, m.REF AS ref, m.SREF1 AS sref1,
         m.SREF2 AS sref2, m.DES AS designation, m.VENUN AS uv,
        m.CDQTE AS cdQte, m.OP AS op, ean.EAN AS ean, m.ENRNO AS enrNo, noteLig.NOTEBLOB as note
        FROM MOUV m WITH (INDEX = INDEX_A)
        LEFT JOIN SARTEAN ean WITH (INDEX = INDEX_F_MINI) ON m.REF = ean.REF AND m.SREF1 = ean.SREF1 AND m.SREF2 = ean.SREF2 AND m.DOS = ean.DOS
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        LEFT JOIN MNOTE noteLig WITH (INDEX = INDEX_A) ON m.TXTNOTE = noteLig.NOTE
        WHERE m.CDNO = $cdno AND m.DOS = 1 AND m.TICOD = 'C' AND m.PICOD = 2 AND m.OP IN ('C','D')
        AND NOT a.REF IN ($this->artBanPreparation)
        AND NOT a.FAM_0001 IN ($this->fam1BanPreparation) AND NOT a.FAM_0002 IN ('MO')
        AND m.CE8 <> 1 AND m.OP IN ('C','D') AND m.CDQTE <> 0
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Liste des produits à préparer sur la commande
    public function getMouvCmdListWithoutFilter($cmd): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT m.CDNO AS cdNo, m.TIERS AS tiers, c.NOM AS nom, m.REF AS ref, m.SREF1 AS sref1,
        m.SREF2 AS sref2, m.DES AS designation, m.VENUN AS uv,
        m.CDQTE AS cdQte, m.OP AS op, ean.EAN AS ean, m.ENRNO AS enrNo, noteLig.NOTEBLOB as note
        FROM MOUV m WITH (INDEX = INDEX_A)
        LEFT JOIN SARTEAN ean WITH (INDEX = INDEX_F_MINI) ON m.REF = ean.REF AND m.SREF1 = ean.SREF1 AND m.SREF2 = ean.SREF2 AND m.DOS = ean.DOS
        INNER JOIN ART a WITH (INDEX = INDEX_A_MINI) ON a.DOS = m.DOS AND a.REF = m.REF
        INNER JOIN CLI c ON m.TIERS = c.TIERS AND m.DOS = c.DOS
        LEFT JOIN MNOTE noteLig WITH (INDEX = INDEX_A) ON m.TXTNOTE = noteLig.NOTE
        WHERE m.CDNO = $cmd AND m.DOS = 1 AND m.TICOD = 'C' AND m.PICOD = 2
    ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // vérifier le statut d'une commande dans divalto (CE4)
    public function controleStatusOfCmd($cmd)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.CE4 FROM ENT WHERE ENT.DOS = 3 AND ENT.PINO = $cmd AND ENT.TICOD = 'C' AND ENT.PICOD = 2
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // ramener les factures fournisseurs FSC de Divalto
    public function getMouvfactFournFsc(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.PINO AS facture, ENT.PIDT AS dateFacture, ENT.TIERS AS tiers, FOU.NOM AS nom, ENT.PIREF AS notreRef, ENT.TICOD AS typeTiers
        FROM ENT
        INNER JOIN MOUV ON MOUV.FANO = ENT.PINO AND MOUV.DOS = ENT.DOS
        INNER JOIN FOU ON MOUV.DOS = FOU.DOS AND MOUV.TIERS = FOU.TIERS
        WHERE MOUV.REF LIKE 'FSC%' AND ENT.TICOD = 'F' AND ENT.PICOD = 4
        GROUP BY ENT.PINO, ENT.PIDT, ENT.TIERS, FOU.NOM, ENT.PIREF, ENT.TICOD
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // ramener les factures clients FSC de Divalto
    public function getMouvfactCliFsc(): array
    {
        $fiveYearsAgo = new DateTime();
        $fiveYearsAgo = date('Y-m-d', strtotime('-5 years'));

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.PINO AS facture, ENT.PIDT AS dateFacture, ENT.TIERS AS tiers, CLI.NOM AS nom, ENT.PIREF AS notreRef, ENT.TICOD AS typeTiers
        FROM ENT
        INNER JOIN MOUV ON MOUV.FANO = ENT.PINO AND MOUV.DOS = ENT.DOS
        INNER JOIN CLI ON MOUV.DOS = CLI.DOS AND MOUV.TIERS = CLI.TIERS
        WHERE (MOUV.REF LIKE 'FSC%' OR MOUV.FANO IN ('19021495','19021076','19021428')) AND ENT.TICOD = 'C' AND ENT.PICOD = 4 AND ENT.PIDT >= '$fiveYearsAgo'
        GROUP BY ENT.PINO, ENT.PIDT, ENT.TIERS, CLI.NOM, ENT.PIREF, ENT.TICOD
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Affaire avec ligne sans code affaire
    public function getRowsWithoutAffair(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT e.PINO AS cmd,
        e.PROJET AS affaire, e.PIDT AS dateCmd,
        COUNT(CASE WHEN m.TIERS = e.TIERS AND m.DOS = e.DOS AND m.TICOD = e.TICOD AND m.PICOD = e.PICOD AND m.CDNO = e.PINO AND m.CDCE4 = 1 THEN 1 END) AS totalMouvement,
        COUNT(CASE WHEN m.TIERS = e.TIERS AND m.DOS = e.DOS AND m.TICOD = e.TICOD AND m.PICOD = e.PICOD AND m.CDNO = e.PINO AND m.CDCE4 = 1 AND m.PROJET = e.PROJET THEN 1 END) AS totalMouvementProjet
            FROM ENT e
            INNER JOIN MOUV m ON m.TIERS = e.TIERS
                                AND m.DOS = e.DOS
                                AND m.TICOD = e.TICOD
                                AND m.PICOD = e.PICOD
                                AND m.CDNO = e.PINO
            WHERE e.DOS = 1
                AND e.TICOD = 'C'
                AND e.PICOD IN (2)
                AND e.PROJET IS NOT NULL
                AND e.PROJET <> ''
                AND e.CE4 = 1
            GROUP BY e.PINO, e.PROJET, e.PIDT
            HAVING COUNT(CASE WHEN m.TIERS = e.TIERS AND m.DOS = e.DOS AND m.TICOD = e.TICOD AND m.PICOD = e.PICOD AND m.CDNO = e.PINO AND m.CDCE4 = 1 THEN 1 END) <> COUNT(CASE WHEN m.TIERS = e.TIERS AND m.DOS = e.DOS AND m.TICOD = e.TICOD AND m.PICOD = e.PICOD AND m.CDNO = e.PINO AND m.CDCE4 = 1 AND m.PROJET = e.PROJET THEN 1 END)
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
