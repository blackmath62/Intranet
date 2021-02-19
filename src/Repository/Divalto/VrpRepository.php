<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Vrp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vrp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vrp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vrp[]    findAll()
 * @method Vrp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VrpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vrp::class);
    }

}
