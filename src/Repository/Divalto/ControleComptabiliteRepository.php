<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ControleComptabiliteRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getControleTaxesComptabilite($annee,$mois,$typeTiers):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MOUV.REF AS ref, MOUV.DES AS des, MOUV.TVAART AS regime,MOUV.FANO AS facture,MOUV.FADT AS date, MOUV.TIERS AS tiers, ENT.CE4 AS status
        FROM MOUV
        INNER JOIN ENT ON MOUV.DOS = ENT.DOS AND MOUV.TIERS = ENT.TIERS AND ENT.PINO = MOUV.FANO
        RIGHT JOIN T085 ON T085.DOS = 999 AND MOUV.TVAART = T085.TVAART
        WHERE MOUV.DOS = 1 --AND ENT.CE4 <> 8
        AND MOUV.REF IN('PCMTAXEGAZOLE10') AND MOUV.TVAART <> 2
        AND YEAR(MOUV.FADT) IN(?) AND MONTH(MOUV.FADT) IN(?) AND MOUV.TICOD = ?
		GROUP BY MOUV.REF, MOUV.DES,MOUV.TVAART, MOUV.FANO, MOUV.FADT, MOUV.TIERS, ENT.CE4";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois,$typeTiers]);
        return $stmt->fetchAll();
    }
    
    public function getControleRegimeTransport($annee,$mois,$typeTiers):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.TIERS AS tiers, MOUV.REF AS reference, MOUV.DES AS designation,ART.FAM_0001 AS famille, MOUV.TVAART AS regimeTva,MOUV.FANO AS facture,MOUV.FADT AS dateFacture
        FROM MOUV 
        INNER JOIN ENT ON MOUV.FANO = ENT.PINO AND MOUV.DOS = ENT.DOS AND MOUV.TIERS = ENT.TIERS
        INNER JOIN ART ON MOUV.REF = ART.REF AND MOUV.DOS = ART.DOS
        WHERE MOUV.DOS = 1 AND YEAR(MOUV.FADT) IN(?) AND MONTH(MOUV.FADT) IN(?) AND ART.FAM_0001 IN('TRANSPOR') AND MOUV.TVAART <> 3 AND ENT.TICOD = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois,$typeTiers]);
        return $stmt->fetchAll();
    }



}
