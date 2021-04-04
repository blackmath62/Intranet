<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ControleComptabiliteVenteRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   
    public function getControleRegimeTiersVente($annee,$mois):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ENT.PICOD AS typePiece, ENT.PINO AS numeroPiece, ENT.TIERS AS tiers, ENT.TVATIE AS regimePiece, CLI.TVATIE AS regimeTiers FROM ENT
        INNER JOIN CLI ON ENT.DOS = CLI.DOS AND ENT.TIERS = CLI.TIERS 
        WHERE ENT.DOS = 1 AND YEAR(ENT.PIDT) IN (?) AND MONTH(ENT.PIDT) IN (?) AND ENT.TVATIE <> CLI.TVATIE";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$annee,$mois]);
        return $stmt->fetchAll();
    }
    
}
