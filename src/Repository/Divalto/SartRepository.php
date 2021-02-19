<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Sart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sart[]    findAll()
 * @method Sart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sart::class);
    }


}
