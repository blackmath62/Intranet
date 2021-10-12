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
        FROM(SELECT SART.REF AS Ref, SART.SREF1 AS Sref1, SART.SREF2 AS Sref2, ART.DES AS Designation,
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
    public function getControleAnomaliesArticlesFermes($dossier):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ArtFerme,Ref, Sref1, Sref2, Designation, Sum(Stock) AS Stock,Op,CmdDate, Cmd,QteCmd, BlDate, Bl, QteBl, Ef, EfQte, ArtFam, UserCr, UserMo, ArtDateFermeture, UserModh
        FROM(SELECT SART.REF AS Ref, SART.SREF1 AS Sref1, SART.SREF2 AS Sref2, ART.DES AS Designation,ART.FAM_0002 as ArtFam, MOUV.USERCR as UserCr, MOUV.USERMO as UserMo, ART.HSDT AS ArtDateFermeture, SART.USERMODH AS UserModh,
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
    

}


