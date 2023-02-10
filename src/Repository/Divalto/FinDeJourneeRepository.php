<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FinDeJourneeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    public function getFinDeJournee(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM ENT
        WHERE ENT.DOS = 1 AND ENT.PINO > 80000 AND PIDT > '2021-01-01'
        AND ENT.PICOD = 3 AND ENT.TICOD = 'C' AND CE4  IN(1)";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

}
