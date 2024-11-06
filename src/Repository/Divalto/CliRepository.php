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

    public function SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay(): array
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
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function SendMailMajCertiphytoClient(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CLI_ID AS Identification, CLI.TIERS AS Tiers, CLI.NOM AS Nom, VRP.EMAIL AS Email, VRP.SELCOD AS Utilisateur
        FROM CLI
        LEFT JOIN VRP ON VRP.DOS = CLI.DOS AND VRP.TIERS = CLI.REPR_0001
        WHERE CLI.HSDT IS NULL AND CLI.DOS = 1 AND CLI.UP_PH_AUTORISE = 2 AND CLI.UP_PH_DECID_OBLIG = 1 AND CLI.TIERS NOT IN ('C0218400')
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Récupérer toutes les adresses de tous les clients ouverts
    public function getClient(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM(
        SELECT RTRIM(LTRIM(c.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom,RTRIM(LTRIM(c.RUE)) AS rue,
        RTRIM(LTRIM(c.CPOSTAL)) AS cp, RTRIM(LTRIM(c.VIL)) AS ville
        FROM CLI c
        WHERE c.DOS = 1 AND c.HSDT IS NULL
        UNION
        SELECT RTRIM(LTRIM(c.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom,RTRIM(LTRIM(a.RUE)) AS rue,
        RTRIM(LTRIM(a.CPOSTAL)) AS cp, RTRIM(LTRIM(a.VIL)) AS ville
        FROM T1 a
        INNER JOIN CLI c ON c.TIERS = a.TIERS AND c.DOS = a.DOS
        WHERE a.DOS = 1 AND c.HSDT IS NULL AND (a.RUE <> '' OR (a.CPOSTAL <> '' AND a.VIL <> ''))
        )reponse
        ORDER BY nom
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Récupérer les codes affaires
    public function getCodeAffaire(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.AFFAIRE AS affaire, a.LIB80 AS lib
        FROM PRJAP a
        ORDER BY a.AFFAIRE ASC
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getThisCodeClient($code): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(c.TIERS)) AS tiers, RTRIM(LTRIM(c.NOM)) AS nom,RTRIM(LTRIM(c.RUE)) AS rue, RTRIM(LTRIM(c.CPOSTAL)) AS cp, RTRIM(LTRIM(c.VIL)) AS ville
        FROM CLI c
        WHERE c.DOS = 1 AND c.HSDT IS NULL AND c.TIERS = '$code'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

    public function SendMailProblemePaysRegimeClients(): array
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
        OR CLI.PAY IN('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','GR','HR','HU','IRL','IT','IE','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK') AND CLI.TVATIE NOT IN ('1','11','5','51')
        OR CLI.PAY NOT IN('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','GR','HR','HU','IRL', 'IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK','FR') AND CLI.TVATIE NOT IN ('2', '21')
        ))reponse
        INNER JOIN MUSER ON MUSER.DOS = Dos AND MUSER.USERX = Utilisateur
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function MesClients($commercial): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CLI.TIERS AS codeTier, CLI.NOM AS nom, CLI.RUE AS rue, CLI.CPOSTAL AS cp, CLI.VIL AS ville, CLI.TEL AS tel, CLI.EMAIL AS mail FROM CLI
        WHERE CLI.REPR_0001 = $commercial AND CLI.HSDT IS NULL
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getClientsForCoupe(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CONCAT(LTRIM(RTRIM(c.TIERS)), 'AD00' ) AS CdDestinataire, REPLACE(LTRIM(RTRIM(c.PAY)),';',',') AS CdInseePays, REPLACE(LTRIM(RTRIM(c.NOM)),';',',') AS Nom, REPLACE(LTRIM(RTRIM(c.ADRCPL1)),';',',') AS Adresse1,
        REPLACE(LTRIM(RTRIM(c.RUE)),';',',') AS Adresse2,
		CONVERT(VARCHAR,REPLACE(LTRIM(RTRIM(c.CPOSTAL)),';',',')) AS CdPostal, REPLACE(LTRIM(RTRIM(c.VIL)),';',',') AS Ville, REPLACE(LTRIM(RTRIM(c.TEL)),';',',') AS Telephone,
        CASE
        WHEN c.TEXCOD_0003 <> '' THEN convert(varchar(max), convert(varbinary(max), n.NOTEBLOB))
        ELSE ''
        END AS InstructionLiv
        ,
        CASE
        WHEN c.EMAIL LIKE '%@%' AND c.EMAIL NOT LIKE '%;%' THEN REPLACE(LTRIM(RTRIM(c.EMAIL)),';',',')
        END AS Email,
         REPLACE(LTRIM(RTRIM(c.ADRCPL2)),';',',') AS sService
        FROM CLI c
        LEFT JOIN T041 note ON c.DOS = note.DOS AND c.TEXCOD_0003 = note.TEXCOD
        LEFT JOIN MNOTE AS n ON note.NOTE = n.NOTE
        WHERE c.DOS = 1 AND c.HSDT IS NULL AND c.TIERS NOT IN ('CodeTier', 'C0160500', 'C0000001')

        UNION

        SELECT CONCAT(LTRIM(RTRIM(a.TIERS)), 'AD' ,a.ADRCOD ) AS CdDestinataire,
		CASE
		WHEN a.PAY = '' THEN REPLACE(LTRIM(RTRIM(c.PAY)),';',',')
		WHEN NOT a.PAY = '' THEN REPLACE(LTRIM(RTRIM(a.PAY)),';',',')
		END AS CdInseePays,
		REPLACE(LTRIM(RTRIM(a.NOM)),';',',') AS Nom, REPLACE(LTRIM(RTRIM(a.ADRCPL1)),';',',') AS Adresse1,
        REPLACE(LTRIM(RTRIM(a.RUE)),';',',') AS Adresse2,
		convert(VARCHAR,REPLACE(LTRIM(RTRIM(a.CPOSTAL)),';',',')) AS CdPostal, REPLACE(LTRIM(RTRIM(a.VIL)),';',',') AS Ville, REPLACE(LTRIM(RTRIM(a.TEL)),';',',') AS Telephone,
        CASE
        WHEN c.TEXCOD_0003 <> '' THEN convert(varchar(max), convert(varbinary(max), n.NOTEBLOB))
        ELSE ''
        END AS InstructionLiv
        ,CASE
        WHEN a.EMAIL LIKE '%@%' AND a.EMAIL NOT LIKE '%;%' THEN REPLACE(LTRIM(RTRIM(a.EMAIL)),';',',')
        END AS Email,
        REPLACE(LTRIM(RTRIM(a.ADRCPL2)),';',',') AS sService
        FROM T1 a
        INNER JOIN CLI c ON a.DOS = c.DOS AND a.TIERS = c.TIERS AND c.TIERS NOT IN ('CodeTier', 'C0160500', 'C0000001')
        LEFT JOIN T041 note ON a.DOS = note.DOS AND c.TEXCOD_0003 = note.TEXCOD
        LEFT JOIN MNOTE AS n ON note.NOTE = n.NOTE
        WHERE a.DOS = 1 AND c.HSDT IS NULL
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function translateBlobTiers($dos, $typeTiers): array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT c.TIERS AS Reference, c.NOM AS Nom, MAX(convert(varchar(max),n.NOTEBLOB)) AS Blob,
        MAX(convert(varchar(max),n2.NOTEBLOB)) AS Blob2,MAX(convert(varchar(max),n3.NOTEBLOB)) AS Blob3,MAX(convert(varchar(max),n4.NOTEBLOB)) AS Blob4
        FROM $typeTiers c
        LEFT JOIN MNOTE n ON n.NOTE = c.NOTE
        LEFT JOIN T041 nt2 ON nt2.TEXCOD = c.TEXCOD_0002
        LEFT JOIN MNOTE n2 ON n2.NOTE = nt2.NOTE
        LEFT JOIN T041 nt3 ON nt3.TEXCOD = c.TEXCOD_0003
        LEFT JOIN MNOTE n3 ON n3.NOTE = nt3.NOTE
        LEFT JOIN T041 nt4 ON nt4.TEXCOD = c.TEXCOD_0004
        LEFT JOIN MNOTE n4 ON n4.NOTE = nt4.NOTE
        WHERE c.DOS = $dos AND c.HSDT is NULL AND
        (c.NOTE > 0 OR c.TEXCOD_0002 <> '' OR c.TEXCOD_0003 <> '' OR c.TEXCOD_0004 <> ''
        )
        GROUP BY c.TIERS, c.NOM
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getTiersContactsAdresses($dos, $typeTiers): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(c.TIERS)) AS tiers, CONCAT(LTRIM(RTRIM(c.TIERS)),'#',LTRIM(RTRIM(u.CONTACT))) AS code,
CASE
WHEN u.PRENOM <> '' AND u.NOM <> '' THEN CONCAT(LTRIM(RTRIM(u.PRENOM)), ' ', LTRIM(RTRIM(u.NOM)) )
WHEN u.PRENOM = '' AND u.NOM <> '' THEN LTRIM(RTRIM(u.NOM))
WHEN u.PRENOM <> '' AND u.NOM = '' THEN LTRIM(RTRIM(u.PRENOM))
WHEN u.PRENOM = '' AND u.NOM = '' THEN LTRIM(RTRIM(c.NOM))
END AS nom
, CASE
WHEN u.TIT = 'MR' THEN 'Monsieur'
WHEN u.TIT = 'Mme' THEN 'Madame'
ELSE ''
END AS titre,
u.LIB AS fonction,
CASE
WHEN u.TEL <> '0000000000' THEN REPLACE(REPLACE(u.TEL,'.',''),' ','')
ELSE ''
END AS tel,
CASE
WHEN u.TELGSM  <> '0000000000' THEN REPLACE(REPLACE(u.TELGSM,'.',''),' ','')
ELSE ''
END AS gsm
,
CASE
WHEN u.FAX <> '0000000000' THEN REPLACE(REPLACE(u.FAX,'.',''),' ','')
ELSE ''
END AS fax, LOWER(u.EMAIL) AS email, 'Contact' AS typeAdr,
'' AS compl1, '' AS compl2, '' AS rue, '' AS localite
, '' AS cpostal, '' AS ville, '' AS pays
FROM $typeTiers c
INNER JOIN T2 u ON c.DOS = u.DOS AND c.TIERS = u.TIERS -- Contacts
WHERE c.DOS = $dos AND c.HSDT IS NULL

UNION
-- ADRESSES
SELECT LTRIM(RTRIM(c.TIERS)) AS tiers, CONCAT(LTRIM(RTRIM(c.TIERS)),'#',LTRIM(RTRIM(a.ADRCOD))) AS code, LTRIM(RTRIM(a.NOM)) AS nom, '' AS titre, '' AS fonction,
CASE
WHEN a.TEL <> '0000000000' THEN REPLACE(REPLACE(a.TEL,'.',''),' ','')
ELSE ''
END AS tel, '' AS gsm,
CASE
WHEN a.FAX <> '0000000000' THEN REPLACE(REPLACE(a.FAX,'.',''),' ','')
ELSE ''
END AS fax
, LOWER(a.EMAIL) AS email,
CASE
WHEN c.ADRCOD_0002 = a.ADRCOD THEN 'Adresse Commande'
WHEN c.ADRCOD_0003 = a.ADRCOD THEN 'Adresse de livraison'
WHEN c.ADRCOD_0004 = a.ADRCOD THEN 'Adresse de facturation'
ELSE 'Autre adresse'
END AS typeAdr,
LTRIM(RTRIM(a.ADRCPL1)) AS compl1, LTRIM(RTRIM(a.ADRCPL2)) AS compl2, LTRIM(RTRIM(a.RUE)) AS rue, LTRIM(RTRIM(a.LOC)) AS localite
, LTRIM(RTRIM(a.CPOSTAL)) AS cpostal, LTRIM(RTRIM(a.VIL)) AS ville, LTRIM(RTRIM(a.PAY)) AS pays
FROM $typeTiers c
	INNER JOIN T1 a ON c.DOS = a.DOS AND c.TIERS = a.TIERS -- Adresses
WHERE c.DOS = $dos AND c.HSDT IS NULL
GROUP BY c.TIERS, a.ADRCOD, a.NOM, a.TEL, a.FAX, a.EMAIL, a.ADRCOD, a.ADRCPL1, a.ADRCPL2, a.RUE, a.LOC, a.CPOSTAL, a.VIL, a.PAY, c.ADRCOD_0002, c.ADRCOD_0003, c.ADRCOD_0004

UNION
-- ADRESSE TIERS LIVRAISON

SELECT LTRIM(RTRIM(c.TIERS)) AS tiers, CONCAT(LTRIM(RTRIM(c.ADRTIERS_0003)), '#',LTRIM(RTRIM(c.ADRCOD_0003))) AS code,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.NOM))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.NOM))
END AS nom, '' AS titre, '' AS fonction,
CASE
WHEN a.TEL <> '0000000000' AND c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN REPLACE(REPLACE(a.TEL,'.',''),' ','')
WHEN c2.TEL <> '0000000000' AND c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN REPLACE(REPLACE(c2.TEL,'.',''),' ','')
ELSE ''
END AS tel, '' AS gsm,
CASE
WHEN a.FAX <> '0000000000' AND c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN REPLACE(REPLACE(a.FAX,'.',''),' ','')
WHEN c2.FAX <> '0000000000' AND c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN REPLACE(REPLACE(c2.FAX,'.',''),' ','')
ELSE ''
END AS fax,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LOWER(a.EMAIL)
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LOWER(c2.EMAIL)
END AS email,
'Adresse de livraison' AS typeAdr,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.ADRCPL1))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.ADRCPL1))
END AS compl1,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.ADRCPL2))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.ADRCPL2))
END AS compl2,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.RUE))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.RUE))
END AS rue,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.LOC))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.LOC))
END AS localite,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.CPOSTAL))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.CPOSTAL))
END AS cpostal,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.VIL))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.VIL))
END AS ville,
CASE
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 <> '' THEN LTRIM(RTRIM(a.PAY))
WHEN c.ADRTIERS_0003 <> '' AND c.ADRCOD_0003 = '' THEN LTRIM(RTRIM(c2.PAY))
END AS pays
FROM $typeTiers c
INNER JOIN $typeTiers c2 ON c.ADRTIERS_0003 = c2.TIERS AND c.DOS = c2.DOS
LEFT JOIN T1 a ON c.DOS = a.DOS AND c.ADRTIERS_0003 = a.TIERS AND c.ADRCOD_0003 = a.ADRCOD
WHERE c.DOS = $dos AND c.HSDT IS NULL AND c.ADRTIERS_0003 <> ''
GROUP BY c.TIERS, a.ADRCOD, c2.NOM, a.NOM, a.TEL, a.FAX, a.EMAIL, a.ADRCOD, a.ADRCPL1, a.ADRCPL2, a.RUE, a.LOC, a.CPOSTAL, a.VIL, a.PAY, c.ADRCOD_0003, c.ADRTIERS_0003,
c2.TEL, c2.FAX, c2.EMAIL, c2.ADRCPL1, c2.ADRCPL2, c2.RUE, c2.LOC, c2.CPOSTAL, c2.VIL, c2.PAY

UNION
-- ADRESSE TIERS FACTURATION

SELECT LTRIM(RTRIM(c.TIERS)) AS tiers, CONCAT(LTRIM(RTRIM(c.ADRTIERS_0004)), '#',LTRIM(RTRIM(c.ADRCOD_0004))) AS code,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.NOM))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.NOM))
END AS nom, '' AS titre, '' AS fonction,
CASE
WHEN a.TEL <> '0000000000' AND c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN REPLACE(REPLACE(a.TEL,'.',''),' ','')
WHEN c2.TEL <> '0000000000' AND c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN REPLACE(REPLACE(c2.TEL,'.',''),' ','')
ELSE ''
END AS tel, '' AS gsm,
CASE
WHEN a.FAX <> '0000000000' AND c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN REPLACE(REPLACE(a.FAX,'.',''),' ','')
WHEN c2.FAX <> '0000000000' AND c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN REPLACE(REPLACE(c2.FAX,'.',''),' ','')
ELSE ''
END AS fax,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LOWER(a.EMAIL)
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LOWER(c2.EMAIL)
END AS email,
'Adresse de facturation' AS typeAdr,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.ADRCPL1))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.ADRCPL1))
END AS compl1,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.ADRCPL2))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.ADRCPL2))
END AS compl2,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.RUE))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.RUE))
END AS rue,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.LOC))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.LOC))
END AS localite,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.CPOSTAL))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.CPOSTAL))
END AS cpostal,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.VIL))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.VIL))
END AS ville,
CASE
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 <> '' THEN LTRIM(RTRIM(a.PAY))
WHEN c.ADRTIERS_0004 <> '' AND c.ADRCOD_0004 = '' THEN LTRIM(RTRIM(c2.PAY))
END AS pays
FROM $typeTiers c
INNER JOIN $typeTiers c2 ON c.ADRTIERS_0004 = c2.TIERS AND c.DOS = c2.DOS
LEFT JOIN T1 a ON c.DOS = a.DOS AND c.ADRTIERS_0004 = a.TIERS AND c.ADRCOD_0004 = a.ADRCOD
WHERE c.DOS = $dos AND c.HSDT IS NULL AND c.ADRTIERS_0004 <> ''
GROUP BY c.TIERS, a.ADRCOD, c2.NOM, a.NOM, a.TEL, a.FAX, a.EMAIL, a.ADRCOD, a.ADRCPL1, a.ADRCPL2, a.RUE, a.LOC, a.CPOSTAL, a.VIL, a.PAY, c.ADRCOD_0004, c.ADRTIERS_0004,
c2.TEL, c2.FAX, c2.EMAIL, c2.ADRCPL1, c2.ADRCPL2, c2.RUE, c2.LOC, c2.CPOSTAL, c2.VIL, c2.PAY
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getTiersFiche($dos, $typeTiers): array
    {
        if ($typeTiers == 'FOU') {
            $representant = 'c.SALCOD';
        } elseif ($typeTiers == 'CLI') {
            $representant = 'c.REPR_0001';
        }

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(c.TIERS)) AS tiers, LTRIM(RTRIM(c.TIT)) AS titre, UPPER(LTRIM(RTRIM(c.NOM))) AS nom,
CASE
WHEN CHARINDEX('@', c.RUE) > 0 THEN LOWER(LTRIM(RTRIM(c.RUE)))
ELSE UPPER(LTRIM(RTRIM(c.RUE)))
END AS rue,
CASE
WHEN CHARINDEX('@', c.ADRCPL1) > 0 THEN LOWER(LTRIM(RTRIM(c.ADRCPL1)))
ELSE UPPER(LTRIM(RTRIM(c.ADRCPL1)))
END AS Compl1,
CASE
WHEN CHARINDEX('@', c.ADRCPL2) > 0 THEN LOWER(LTRIM(RTRIM(c.ADRCPL2)))
ELSE UPPER(LTRIM(RTRIM(c.ADRCPL2)))
END AS Compl2,
LTRIM(RTRIM(c.CPOSTAL)) AS CodePostal, LTRIM(RTRIM(c.VIL)) AS Ville, LTRIM(RTRIM(c.PAY)) AS Pays,
CASE
WHEN c.TEL <> '0000000000' AND c.TEL <> '0' THEN REPLACE(REPLACE(REPLACE(c.TEL,'.',''),' ',''),'-','')
ELSE ''
END AS tel,
CASE
WHEN c.FAX <> '0000000000' AND c.FAX <> '0' THEN REPLACE(REPLACE(REPLACE(c.FAX,'.',''),' ',''),'-','')
ELSE ''
END AS fax,
CASE
WHEN CHARINDEX('@', c.EMAIL) > 0 THEN LOWER(LTRIM(RTRIM(c.EMAIL)))
END AS Email,
CASE
WHEN CHARINDEX('@', c.ZONA) > 0 THEN
LOWER(LTRIM(RTRIM(c.ZONA))) END AS mail2,
LTRIM(RTRIM(c.WEB)) AS SiteWeb, LTRIM(RTRIM(c.SIRET)) AS Siret, LTRIM(RTRIM(c.REGL)) AS ConditionPaiement,
LTRIM(RTRIM(c.STAT_0001)) AS Etiquettes, LTRIM(RTRIM(c.STAT_0002)) AS Stat2, LTRIM(RTRIM(c.STAT_0003)) AS Stat3, LTRIM(RTRIM($representant)) AS Representant, -- c.SALCOD AS Acheteur, --
LTRIM(RTRIM(c.ENMAX_0002)) AS LimiteDeCredit, LTRIM(RTRIM(c.NAF)) AS Naf,
CASE
WHEN c.NOTE > 0 THEN MAX(convert(varchar(max),n.NOTEBLOB))
END AS Note,
LTRIM(RTRIM(c.JOINT)) AS FichiersJoints, LTRIM(RTRIM(c.TVANO)) AS Intra,
CASE
WHEN c.FEU = 1 THEN 'Aucun message'
WHEN c.FEU = 2 THEN 'Avertissement'
WHEN c.FEU = 3 THEN 'Message bloquant'
END AS  avertissement,
CASE
WHEN c.FEU = 1 THEN ''
WHEN c.FEU = 2 THEN 'Client Feu Orange'
WHEN c.FEU = 3 THEN 'Client Feu Rouge'
END AS  Feu, LTRIM(RTRIM(c.BLMOD)) AS ModPort,
CASE
WHEN c.TEXCOD_0002 <> '' THEN MAX(convert(varchar(max),n2.NOTEBLOB))
END  AS T2,
CASE
WHEN c.TEXCOD_0003 <> '' THEN MAX(convert(varchar(max),n3.NOTEBLOB))
END  AS T3,
CASE
WHEN c.TEXCOD_0004 <> '' THEN MAX(convert(varchar(max),n4.NOTEBLOB))
END  AS T4
FROM $typeTiers c
	LEFT JOIN MNOTE n ON n.NOTE = c.NOTE
    LEFT JOIN T041 nt2 ON nt2.TEXCOD = c.TEXCOD_0002
    LEFT JOIN MNOTE n2 ON n2.NOTE = nt2.NOTE
    LEFT JOIN T041 nt3 ON nt3.TEXCOD = c.TEXCOD_0003
    LEFT JOIN MNOTE n3 ON n3.NOTE = nt3.NOTE
    LEFT JOIN T041 nt4 ON nt4.TEXCOD = c.TEXCOD_0004
    LEFT JOIN MNOTE n4 ON n4.NOTE = nt4.NOTE
WHERE c.DOS = $dos AND c.HSDT is NULL
GROUP BY c.TIERS, c.TIT, c.NOM, c.RUE, c.ADRCPL1, c.ADRCPL2, c.CPOSTAL, c.VIL, c.PAY,
c.TEL, c.FAX, c.EMAIL, c.ZONA, c.WEB, c.SIRET, c.REGL, c.STAT_0001, c.STAT_0002, c.STAT_0003,$representant
, c.ENMAX_0002, c.NAF, c.NOTE, c.JOINT, c.TVANO, c.FEU, c.BLMOD,
c.TEXCOD_0001,c.TEXCOD_0002,c.TEXCOD_0003,c.TEXCOD_0004
ORDER BY c.TIERS
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getBanqueTiers($dos, $typeTiers): array
    {
        $pc = substr($typeTiers, 0, 1);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT LTRIM(RTRIM(r.TIERS)) AS tiers,CONCAT(LTRIM(RTRIM(r.TIERS)),'#',LTRIM(RTRIM(r.RIBCOD))) AS code,CONCAT(LTRIM(RTRIM(r.IBAN1)),LTRIM(RTRIM(r.IBAN2)),
                LTRIM(RTRIM(r.IBAN3))) AS IBAN, LTRIM(RTRIM(r.RIBBIC)) AS ribBic, LTRIM(RTRIM(r.RIBDO)) AS ribDo
                FROM T3 r
                INNER JOIN $typeTiers c ON c.DOS = r.DOS AND c.TIERS = r.TIERS
                WHERE r.DOS = $dos AND r.TIERS LIKE '$pc%' AND c.HSDT IS NULL AND r.HSDT IS NULL
                ORDER BY r.CODTYP
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
