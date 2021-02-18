<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class DebRobyRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   

    public function getDebRobyByMonth($annee,$mois):array
    {

        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT DateFact, NumFact, notreRef, Tiers, Nom, Pays, Volume, Poids,SUM(MontSign) AS Montant
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT ENT.PIDT AS DateFact,ENT.PINO AS NumFact, ENT.PIREF AS notreRef, FOU.TIERS AS Tiers, FOU.NOM AS Nom, FOU.PAY AS Pays, ENT.VOLTOT AS Volume,ENT.POITOT AS Poids, MOUV.OP AS OP,
        CASE -- Signature du montant
                WHEN (MOUV.OP = 'F' OR MOUV.OP = 'FD') AND SUM(MOUV.REMPIEMT_0004) <> 0  THEN SUM(MOUV.MONT)+SUM(-1 * MOUV.REMPIEMT_0004)
                WHEN (MOUV.OP = 'F' OR MOUV.OP = 'FD') AND SUM(MOUV.REMPIEMT_0004) = 0 THEN SUM(MOUV.MONT)		-- Si Sens = 2 alors c'est positif
                WHEN (MOUV.OP = 'GD' OR MOUV.OP = 'G') AND SUM(MOUV.REMPIEMT_0004) <> 0 THEN SUM(-1 * MOUV.MONT)+SUM(-1 * MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                WHEN (MOUV.OP = 'GD' OR MOUV.OP = 'G') AND SUM(MOUV.REMPIEMT_0004) = 0 THEN SUM(-1 * MOUV.MONT)
                ELSE 0
        END AS MontSign
        FROM  ART, MOUV, FOU, ENT 
        WHERE (ART.DOS=MOUV.DOS AND MOUV.REF=ART.REF AND FOU.TIERS=MOUV.TIERS 
            AND FOU.DOS=MOUV.DOS AND ENT.DOS=MOUV.DOS AND ENT.TICOD=MOUV.TICOD 
            AND ENT.PICOD=MOUV.PICOD AND ENT.TIERS=MOUV.TIERS AND ENT.PINO=MOUV.FANO  
            AND YEAR(MOUV.FADT)= ? AND MONTH(MOUV.FADT) = ? AND ART.DOS=3 AND MOUV.TICOD='F' AND MOUV.PICOD=4 
            AND FOU.STAT_0001<>'TRANSPOR' AND FOU.PAY NOT  IN ('BY', 'RU', 'CH '))
            GROUP BY ENT.PIDT, ENT.PINO, ENT.PIREF, FOU.TIERS, FOU.NOM, FOU.PAY, ENT.VOLTOT, ENT.POITOT, MOUV.OP) Reponse
        GROUP BY DateFact, NumFact, notreRef, Tiers, Nom, Pays, Volume, Poids
        ORDER BY NumFact";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois]);
        return $stmt->fetchAll();
    }
}
