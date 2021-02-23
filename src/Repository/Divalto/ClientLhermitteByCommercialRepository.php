<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ClientLhermitteByCommercialRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }
   

    public function getClientLhermitteByCommercial($commercial):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT VRP.NOM AS Commercial, CLI.TIERS AS Tiers, CLI.NOM AS Nom, CLI.RUE AS Rue, CLI.CPOSTAL AS CP, CLI.VIL AS Ville, CLI.TEL AS Tel, CLI.EMAIL AS Mail
        FROM CLI
        LEFT JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE CLI.DOS = 1 AND CLI.HSDT IS NULL AND CLI.REPR_0001 = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$commercial]);
        return $stmt->fetchAll();
    }
}
