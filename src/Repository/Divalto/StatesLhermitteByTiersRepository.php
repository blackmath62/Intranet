<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class StatesLhermitteByTiersRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getStatesLhermitteGlobalesByMonth($annee,$mois):array
    {
        $N1 = $annee - 1;
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT YEAR(MOUV.FADT) AS Annee,MOUV.BLNO AS Bl,MOUV.FANO AS Facture,
        CASE
        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'EV' THEN 'EV'
        WHEN ART.FAM_0002 IN ('EV', 'HP') AND CLI.STAT_0002 = 'HP' THEN 'HP'
        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'ME'
        ELSE 'WTF !'
        END AS SecteurMouvement,
        CASE
        WHEN ART.FAM_0002 IN ('EV', 'HP') THEN VRP.NOM
        WHEN ART.FAM_0002 IN ('ME', 'MO') THEN 'DESCHODT ALEX Port: 06.20.63.40.97'
        END AS Commercial,
        CLI.STAT_0001 AS FamClient,CLI.STAT_0002 AS SecteurClient, MOUV.TIERS AS Tiers,CLI.NOM AS Nom,
        ART.TYPEARTCOD AS TypeArticle,ART.FAM_0001 AS FamArticle,ART.FAM_0002 AS SecteurArticle, MOUV.REF AS Ref, MOUV.DES AS Designation, MOUV.SREF1 AS Sref1, MOUV.SREF2 AS Sref2,MOUV.VENUN AS UV, LTRIM(RTRIM(MOUV.OP)) AS OP,
        CASE
        WHEN MOUV.OP IN('C','CD') THEN MOUV.FAQTE
        WHEN MOUV.OP IN('DD','D') THEN -1 * MOUV.FAQTE
        END AS QteSign,
        CASE
        WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
        WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004)
        END AS MontantSign
        FROM MOUV
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND MOUV.DOS = CLI.DOS
        LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
        WHERE MOUV.DOS = 1 AND YEAR(MOUV.FADT) IN (?,?) AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MONTH(MOUV.FADT) IN (?) --AND ART.FAM_0002 IN ('EV','HP') AND CLI.STAT_0002 IN('EV','EV')
        AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$N1,$mois]);
        return $stmt->fetchAll();
    }

    public function getStatesLhermitteByArticles($annee, $mois, $tiers,$sectArt1, $sectArt2,$sectCli1,$sectCli2):array
    {
        $N1 = $annee - 1;
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT YEAR(fadt) AS Annee, nom, ref, des, sref1, sref2, uv,SUM(QTESIGN) AS qte, SUM(MONTANTSIGN) AS MontantSign FROM
        (SELECT MOUV.TIERS AS tiers, CLI.NOM AS nom, MOUV.REF AS ref, MOUV.DES AS des, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, 
        MOUV.OP AS op, MOUV.VENUN AS uv, MOUV.FADT AS fadt, MOUV.FAQTE AS qte,MOUV.MONT AS montant, MOUV.REMPIEMT_0004 AS remise,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS MONTANTSIGN,
        CASE
            WHEN MOUV.OP IN('C','CD') THEN MOUV.FAQTE
            WHEN MOUV.OP IN('D','DD') THEN -1*MOUV.FAQTE
            ELSE 0
        END AS QTESIGN
        FROM MOUV
        INNER JOIN CLI ON CLI.TIERS = MOUV.TIERS AND CLI.DOS = MOUV.DOS
        INNER JOIN ART ON ART.REF = MOUV.REF AND ART.DOS = MOUV.DOS
        WHERE YEAR(MOUV.FADT) IN (?,?) AND MONTH(MOUV.FADT) IN (?) 
        AND ART.FAM_0002 IN (?,?) AND CLI.STAT_0002 IN (?,?)  AND MOUV.TICOD = 'C' AND MOUV.PICOD = 4 AND MOUV.TIERS IN (?) AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')
        GROUP BY MOUV.TIERS,CLI.NOM, MOUV.REF, MOUV.DES, MOUV.SREF1, MOUV.SREF2,MOUV.OP, MOUV.VENUN,MOUV.FADT, MOUV.FAQTE,MOUV.MONT, MOUV.REMPIEMT_0004)REPONSE
        GROUP BY YEAR(fadt),nom, ref, des, sref1, sref2, uv";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$N1,$mois,$sectArt1, $sectArt2,$sectCli1,$sectCli2, $tiers]);
        return $stmt->fetchAll();
    }
}
