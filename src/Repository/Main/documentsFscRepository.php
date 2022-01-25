<?php

namespace App\Repository\Main;

use App\Entity\Main\documentsFsc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method documentsFsc|null find($id, $lockMode = null, $lockVersion = null)
 * @method documentsFsc|null findOneBy(array $criteria, array $orderBy = null)
 * @method documentsFsc[]    findAll()
 * @method documentsFsc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class documentsFscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, documentsFsc::class);
    }

    // /**
    //  * @return documentsFsc[] Returns an array of documentsFsc objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?documentsFsc
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
