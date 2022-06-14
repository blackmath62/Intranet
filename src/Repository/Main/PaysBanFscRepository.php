<?php

namespace App\Repository\Main;

use App\Entity\Main\PaysBanFsc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaysBanFsc|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaysBanFsc|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaysBanFsc[]    findAll()
 * @method PaysBanFsc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaysBanFscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaysBanFsc::class);
    }

    // /**
    //  * @return PaysBanFsc[] Returns an array of PaysBanFsc objects
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
    public function findOneBySomeField($value): ?PaysBanFsc
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
