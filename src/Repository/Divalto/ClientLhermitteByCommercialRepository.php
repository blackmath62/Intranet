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
   

    public function getClientLhermitteByCommercial():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT VRP.NOM AS Commercial, VRP.TIERS AS Id, VRP.EMAIL AS Mail, CLI.TIERS AS Tiers, CLI.NOM AS Nom, CLI.RUE AS Rue, CLI.CPOSTAL AS CP, CLI.VIL AS Ville, CLI.TEL AS Tel, CLI.EMAIL AS Mail
        FROM CLI
        LEFT JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE CLI.DOS = 1 AND CLI.HSDT IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getClient($tiers)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT VRP.EMAIL AS Email, CLI.NOM AS Nom
        FROM CLI
        LEFT JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE CLI.DOS = 1 AND CLI.HSDT IS NULL AND CLI.TIERS = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tiers]);
        return $stmt->fetch();
    }
}
