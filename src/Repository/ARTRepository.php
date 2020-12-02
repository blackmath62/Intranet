<?php

namespace App\Repository;

use DivaltoSvg\Entity\ART;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ART|null find($id, $lockMode = null, $lockVersion = null)
 * @method ART|null findOneBy(array $criteria, array $orderBy = null)
 * @method ART[]    findAll()
 * @method ART[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ARTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ART::class, 'DivaltoSvg');
    }

    // /**
    //  * @return ART[] Returns an array of ART objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->innerJoin('a.activiteEnseignements', 'acti')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ART
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
