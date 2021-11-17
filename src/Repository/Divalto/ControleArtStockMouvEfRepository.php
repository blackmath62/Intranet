<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ControleArtStockMouvEfRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getControleArtStockMouvEfRepository($search):array
    {
        $search = $search . '%';

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ArtFerme,Ref, Sref1, Sref2, Designation, Sum(Stock) AS Stock,Op, Cmd,QteCmd, Bl, QteBl, Ef, EfQte
        FROM(SELECT RTRIM(LTRIM(SART.REF)) AS Ref, RTRIM(LTRIM(SART.SREF1)) AS Sref1, RTRIM(LTRIM(SART.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation,
        CASE
            WHEN SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 THEN MVTL_STOCK_V.QTETJSENSTOCK
        END AS Stock,
        CASE
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NULL THEN 'SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NOT NULL THEN 'ART & SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IS NULL AND ART.HSDT IS NOT NULL THEN 'ART FERME'
            WHEN (SART.SREF1 = '' AND SART.SREF2 = '') AND ART.HSDT IS NOT NULL THEN 'ARTICLE FERME'
            ELSE ''
        END AS  ArtFerme,
        CASE
            WHEN (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1)) AND MOUV.TICOD IN ('C','F') THEN MOUV.OP
        END AS  Op,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDNO
        END AS  Cmd,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDQTE
        END AS  QteCmd,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLNO
        END AS  Bl,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLQTE
        END AS  QteBl,
        CASE
            WHEN MVTL.QTE IS NOT NULL THEN MVTL.OP
        END AS  Ef,
        CASE
            WHEN MVTL.QTE IS NOT NULL THEN MVTL.QTE
        END AS  EfQte
        FROM SART
        INNER JOIN ART ON SART.REF = ART.REF AND SART.DOS = ART.DOS
        LEFT JOIN MVTL_STOCK_V ON SART.DOS = MVTL_STOCK_V.DOSSIER AND SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL
        LEFT JOIN MOUV ON SART.DOS = MOUV.DOS AND SART.REF = MOUV.REF AND SART.SREF1 = MOUV.SREF1 AND SART.SREF2 = MOUV.SREF2 AND (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1) ) AND MOUV.TICOD IN ('C','F') AND (MOUV.CDNO > 0 OR MOUV.BLNO > 0)
        LEFT JOIN MVTL ON SART.REF = MVTL.REF AND SART.DOS = MVTL.DOS AND MVTL.OP IN ('999') AND MVTL.CE2 = 1 AND SART.SREF1 = MVTL.SREF1 AND SART.SREF2 =  MVTL.SREF2
        WHERE SART.DOS = 1 AND SART.REF LIKE (?)
        ) reponse
        WHERE Stock IS NOT NULL OR Op IS NOT NULL OR Cmd IS NOT NULL OR QteCmd IS NOT NULL OR Bl IS NOT NULL OR QteBl IS NOT NULL OR Ef IS NOT NULL OR EfQte IS NOT NULL
        GROUP BY Ref, Sref1, Sref2, Designation,ArtFerme,Op, Cmd,QteCmd, Bl, QteBl, Ef, EfQte
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$search]);
        return $stmt->fetchAll();
    }

    public function getControleOneArtStockMouvEf($ref, $sref1,$sref2):array
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Identification, ArtFerme,Ref, Sref1, Sref2, Designation, Sum(Stock) AS Stock,SUM(QteCmd) AS Cmd,SUM(QteBl) AS Bl, SUM(EfQte) AS Ef
        FROM(SELECT RTRIM(LTRIM(SART.SART_ID)) AS Identification, RTRIM(LTRIM(SART.REF)) AS Ref, RTRIM(LTRIM(SART.SREF1)) AS Sref1, RTRIM(LTRIM(SART.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation,
        CASE
            WHEN SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 THEN MVTL_STOCK_V.QTETJSENSTOCK
        END AS Stock,
        CASE
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NULL THEN 'SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NOT NULL THEN 'ART & SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IS NULL AND ART.HSDT IS NOT NULL THEN 'ART FERME'
            WHEN (SART.SREF1 = '' AND SART.SREF2 = '') AND ART.HSDT IS NOT NULL THEN 'ARTICLE FERME'
            ELSE ''
        END AS  ArtFerme,
        CASE
            WHEN (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1)) AND MOUV.TICOD IN ('C','F') THEN MOUV.OP
        END AS  Op,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDNO
        END AS  Cmd,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDQTE
        END AS  QteCmd,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLNO
        END AS  Bl,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLQTE
        END AS  QteBl,
        CASE
            WHEN MVTL.QTE IS NOT NULL THEN MVTL.OP
        END AS  Ef,
        CASE
            WHEN MVTL.QTE IS NOT NULL THEN MVTL.QTE
        END AS  EfQte
        FROM SART
        INNER JOIN ART ON SART.REF = ART.REF AND SART.DOS = ART.DOS
        LEFT JOIN MVTL_STOCK_V ON SART.DOS = MVTL_STOCK_V.DOSSIER AND SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL
        LEFT JOIN MOUV ON SART.DOS = MOUV.DOS AND SART.REF = MOUV.REF AND SART.SREF1 = MOUV.SREF1 AND SART.SREF2 = MOUV.SREF2 AND (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1) ) AND MOUV.TICOD IN ('C','F') AND (MOUV.CDNO > 0 OR MOUV.BLNO > 0)
        LEFT JOIN MVTL ON SART.REF = MVTL.REF AND SART.DOS = MVTL.DOS AND MVTL.OP IN ('999') AND MVTL.CE2 = 1 AND SART.SREF1 = MVTL.SREF1 AND SART.SREF2 =  MVTL.SREF2
        WHERE SART.DOS = 1 AND SART.REF IN (?) AND SART.SREF1 IN (?) AND SART.SREF2 IN (?)
        ) reponse
        WHERE Stock IS NOT NULL OR Op IS NOT NULL OR Cmd IS NOT NULL OR QteCmd IS NOT NULL OR Bl IS NOT NULL OR QteBl IS NOT NULL OR Ef IS NOT NULL OR EfQte IS NOT NULL
        GROUP BY Identification, Ref, Sref1, Sref2, Designation,ArtFerme
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ref, $sref1,$sref2]);
        return $stmt->fetchAll();
    }
    public function getControleAnomaliesArticlesFermes($dossier):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ArtFerme,Ref, Sref1, Sref2, Designation, Sum(Stock) AS Stock,Op,CmdDate, Cmd,QteCmd, BlDate, Bl, QteBl, Ef, EfQte, ArtFam, UserCr, UserMo, ArtDateFermeture, UserModh
        FROM(SELECT RTRIM(LTRIM(SART.REF)) AS Ref, RTRIM(LTRIM(SART.SREF1)) AS Sref1, RTRIM(LTRIM(SART.SREF2)) AS Sref2, RTRIM(LTRIM(ART.DES)) AS Designation,
        RTRIM(LTRIM(ART.FAM_0002)) as ArtFam, RTRIM(LTRIM(MOUV.USERCR)) as UserCr, RTRIM(LTRIM(MOUV.USERMO)) as UserMo, RTRIM(LTRIM(ART.HSDT)) AS ArtDateFermeture, RTRIM(LTRIM(SART.USERMODH)) AS UserModh,
        CASE
            WHEN SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 THEN MVTL_STOCK_V.QTETJSENSTOCK
        END AS Stock,
        CASE
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NULL THEN 'SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NOT NULL THEN 'ART & SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IS NULL AND ART.HSDT IS NOT NULL THEN 'ART FERME'
            WHEN (SART.SREF1 = '' AND SART.SREF2 = '') AND ART.HSDT IS NOT NULL THEN 'ARTICLE FERME'
            ELSE NULL
        END AS  ArtFerme,
        CASE
            WHEN (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1)) AND MOUV.TICOD IN ('C','F') THEN MOUV.OP
        END AS  Op,
		CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDDT
			ELSE NULL
        END AS  CmdDate,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDNO
        END AS  Cmd,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDQTE
        END AS  QteCmd,
		
		CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLDT
			ELSE NULL
        END AS  BlDate,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLNO
        END AS  Bl,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLQTE
        END AS  QteBl,
        CASE
            WHEN MVTL.QTE IS NOT NULL THEN MVTL.OP
        END AS  Ef,
        CASE
            WHEN MVTL.QTE IS NOT NULL THEN MVTL.QTE
        END AS  EfQte
        FROM SART
        INNER JOIN ART ON SART.REF = ART.REF AND SART.DOS = ART.DOS
        LEFT JOIN MVTL_STOCK_V ON SART.DOS = MVTL_STOCK_V.DOSSIER AND SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL
        LEFT JOIN MOUV ON SART.DOS = MOUV.DOS AND SART.REF = MOUV.REF AND SART.SREF1 = MOUV.SREF1 AND SART.SREF2 = MOUV.SREF2 AND (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1) ) AND MOUV.TICOD IN ('C','F') AND (MOUV.CDNO > 0 OR MOUV.BLNO > 0)
        LEFT JOIN MVTL ON SART.REF = MVTL.REF AND SART.DOS = MVTL.DOS AND MVTL.OP IN ('999') AND MVTL.CE2 = 1 AND SART.SREF1 = MVTL.SREF1 AND SART.SREF2 =  MVTL.SREF2
        WHERE SART.DOS IN ($dossier) 
        ) reponse
        WHERE (Stock IS NOT NULL OR Op IS NOT NULL OR Cmd IS NOT NULL OR QteCmd IS NOT NULL OR Bl IS NOT NULL OR QteBl IS NOT NULL OR Ef IS NOT NULL OR EfQte IS NOT NULL) AND ArtFerme IS NOT NULL
        GROUP BY Ref, Sref1, Sref2, Designation,ArtFerme,Op, Cmd,QteCmd, Bl, QteBl, Ef, EfQte, ArtFam, UserCr, UserMo,ArtDateFermeture, UserModh, CmdDate, BlDate
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getControleSaisieArticlesSrefFermes():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT Commercial, Identification, ArtFerme,Ref, Sref1, Sref2, Designation, Sum(Stock) AS Stock,Op,CmdDate, Cmd,QteCmd, BlDate, Bl, QteBl, ArtFam, Utilisateur, UserMo, ArtDateFermeture, UserModh, Email
        FROM(SELECT RTRIM(LTRIM(MOUV.MOUV_ID)) AS Identification, RTRIM(LTRIM(SART.REF)) AS Ref, RTRIM(LTRIM(SART.SREF1)) AS Sref1, RTRIM(LTRIM(SART.SREF2)) AS Sref2,
        RTRIM(LTRIM(ART.DES)) AS Designation,RTRIM(LTRIM(ART.FAM_0002)) as ArtFam, RTRIM(LTRIM(MOUV.USERCR)) as Utilisateur, RTRIM(LTRIM(MUSER.EMAIL)) AS Email, 
        RTRIM(LTRIM(MOUV.USERMO)) as UserMo, RTRIM(LTRIM(ART.HSDT)) AS ArtDateFermeture, RTRIM(LTRIM(SART.USERMODH)) AS UserModh,
        CASE
            WHEN SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 THEN MVTL_STOCK_V.QTETJSENSTOCK
        END AS Stock,
        CASE
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NULL THEN 'SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IN ('Usrd') AND ART.HSDT IS NOT NULL THEN 'ART & SREF FERME'
            WHEN (SART.SREF1 <> '' OR SART.SREF2 <> '') AND SART.CONF IS NULL AND ART.HSDT IS NOT NULL THEN 'ART FERME'
            WHEN (SART.SREF1 = '' AND SART.SREF2 = '') AND ART.HSDT IS NOT NULL THEN 'ARTICLE FERME'
            ELSE NULL
        END AS  ArtFerme,
        CASE
            WHEN (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1)) AND MOUV.TICOD IN ('C','F') THEN MOUV.OP
        END AS  Op,
		
		CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDDT
			ELSE NULL
        END AS  CmdDate,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDNO
        END AS  Cmd,
        CASE
            WHEN MOUV.CDCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.CDQTE
        END AS  QteCmd,
		
		CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLDT
			ELSE NULL
        END AS  BlDate,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLNO
        END AS  Bl,
        CASE
            WHEN MOUV.BLCE4 IN (1) AND MOUV.TICOD IN ('C','F') THEN MOUV.BLQTE
        END AS  QteBl,
        CASE
            WHEN ART.FAM_0002 NOT IN ('ME', 'MO') THEN VRP.EMAIL
			WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'adeschodt@lhermitte.fr'
        END AS  Commercial
        FROM SART
        INNER JOIN ART ON SART.REF = ART.REF AND SART.DOS = ART.DOS
        LEFT JOIN MVTL_STOCK_V ON SART.DOS = MVTL_STOCK_V.DOSSIER AND SART.REF = MVTL_STOCK_V.REFERENCE AND SART.SREF1 = MVTL_STOCK_V.SREFERENCE1 AND SART.SREF2 = MVTL_STOCK_V.SREFERENCE2 AND MVTL_STOCK_V.QTETJSENSTOCK IS NOT NULL
        LEFT JOIN MOUV ON SART.DOS = MOUV.DOS AND SART.REF = MOUV.REF AND SART.SREF1 = MOUV.SREF1 AND SART.SREF2 = MOUV.SREF2 AND (MOUV.CDCE4 IN (1) OR MOUV.BLCE4 IN (1) ) AND MOUV.TICOD IN ('C','F') AND (MOUV.CDNO > 0 OR MOUV.BLNO > 0)
        LEFT JOIN MVTL ON SART.REF = MVTL.REF AND SART.DOS = MVTL.DOS AND MVTL.OP IN ('999') AND MVTL.CE2 = 1 AND SART.SREF1 = MVTL.SREF1 AND SART.SREF2 =  MVTL.SREF2
		LEFT JOIN CLI ON CLI.DOS = MOUV.DOS AND CLI.TIERS = MOUV.TIERS
		LEFT JOIN VRP ON VRP.DOS = CLI.DOS AND VRP.TIERS = CLI.REPR_0001
        LEFT JOIN MUSER ON MUSER.DOS = MOUV.DOS AND  MUSER.USERX = MOUV.USERCR
        WHERE SART.DOS IN (1) AND (MOUV.BLCE4 = 1 OR MOUV.CDCE4 = 1) AND MOUV.USERCR NOT IN ('G3S') AND MOUV.USERMO NOT IN ('G3S') AND (YEAR(MOUV.CDDT) >=2021 OR YEAR(MOUV.BLDT) >=2021)
        ) reponse
        WHERE (Stock IS NOT NULL OR Op IS NOT NULL OR Cmd IS NOT NULL OR QteCmd IS NOT NULL OR Bl IS NOT NULL OR QteBl IS NOT NULL) AND ArtFerme IS NOT NULL 
        GROUP BY Commercial, Identification, Ref, Sref1, Sref2, Designation,ArtFerme,Op, Cmd,QteCmd, Bl, QteBl, ArtFam, Utilisateur, UserMo,ArtDateFermeture, UserModh, CmdDate, BlDate, Email
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    

}


