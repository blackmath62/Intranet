<?php

namespace App\Repository\Main;

use App\Entity\Main\Priorities;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Priorities|null find($id, $lockMode = null, $lockVersion = null)
 * @method Priorities|null findOneBy(array $criteria, array $orderBy = null)
 * @method Priorities[]    findAll()
 * @method Priorities[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrioritiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Priorities::class);
    }

    // /**
    //  * @return Priorities[] Returns an array of Priorities objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Priorities
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
