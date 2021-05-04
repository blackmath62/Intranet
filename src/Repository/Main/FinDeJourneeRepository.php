<?php

namespace App\Repository\Main;

use App\Entity\Main\FinDeJournee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FinDeJournee|null find($id, $lockMode = null, $lockVersion = null)
 * @method FinDeJournee|null findOneBy(array $criteria, array $orderBy = null)
 * @method FinDeJournee[]    findAll()
 * @method FinDeJournee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinDeJourneeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinDeJournee::class);
    }

    // /**
    //  * @return FinDeJournee[] Returns an array of FinDeJournee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FinDeJournee
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
