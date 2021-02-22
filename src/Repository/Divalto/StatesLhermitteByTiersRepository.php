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
   

    public function getStatesLhermitteTiersByMonth($annee,$mois,$sectArt1, $sectArt2,$sectCli1,$sectCli2):array
    {
        $N1 = $annee - 1;
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT YEAR(DateFacture) AS Annee,LTRIM(RTRIM(Commercial)) AS Commercial,LTRIM(RTRIM(Client)) AS Tiers, LTRIM(RTRIM(nom)) as Nom,
        LTRIM(RTRIM(SUM(montantSign))) AS MontantSign
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT MOUV.TIERS as Client, CLI.NOM AS nom, MOUV.OP ,VRP.NOM AS Commercial,MOUV.MONT AS Montant,MOUV.REMPIEMT_0004 AS Remise, 
        MOUV.FADT AS DateFacture,MOUV.FANO AS Facture,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS montantSign
        FROM MOUV
        INNER JOIN ART ON MOUV.REF = ART.REF AND ART.DOS = MOUV.DOS
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND CLI.DOS = MOUV.DOS -- jointure avec la table Client
        LEFT JOIN VRP ON MOUV.DOS = VRP.DOS AND VRP.TIERS = CLI.REPR_0001
        WHERE MOUV.DOS = 1 AND ART.REF NOT IN('ZRPO196','ZRPO196HP','ZRPO7','ZRPO7HP')
        AND MOUV.PICOD = 4 AND MOUV.TICOD = 'C' AND YEAR(MOUV.FADT) IN(?,?) AND MONTH(MOUV.FADT) IN (?) AND ART.FAM_0002 IN (?,?) AND CLI.STAT_0002 IN(?, ?) --IN(YEAR(GETDATE()), YEAR(GETDATE())-1)
        AND MOUV.OP IN('C','CD','DD','D') AND CLI.TIERS <> 'C0160500' 	-- Condition
        GROUP BY MOUV.TIERS, CLI.NOM, MOUV.OP ,VRP.NOM,MOUV.MONT ,MOUV.REMPIEMT_0004 ,MOUV.FADT,MOUV.FANO ) Reponse
        GROUP BY YEAR(DateFacture), Commercial ,Client, nom";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$N1,$mois,$sectArt1, $sectArt2,$sectCli1,$sectCli2]);
        return $stmt->fetchAll();
    }
}
