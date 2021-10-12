<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ControleComptabiliteAchatRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getControleRegimeTiersAchat($annee,$mois):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.PICOD AS typePiece, ENT.PINO AS numeroPiece, ENT.TIERS AS tiers, ENT.TVATIE AS regimePiece, FOU.TVATIE AS regimeTiers FROM ENT
        INNER JOIN FOU ON ENT.DOS = FOU.DOS AND ENT.TIERS = FOU.TIERS 
        WHERE ENT.DOS = 1 AND YEAR(ENT.PIDT) IN (?) AND MONTH(ENT.PIDT) IN (?) AND ENT.TVATIE <> FOU.TVATIE AND ENT.CE4 = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois]);
        return $stmt->fetchAll();
    }
    
}
