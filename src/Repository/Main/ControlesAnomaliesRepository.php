<?php

namespace App\Repository\Main;

use App\Entity\Main\ControlesAnomalies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ControlesAnomalies|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControlesAnomalies|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControlesAnomalies[]    findAll()
 * @method ControlesAnomalies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControlesAnomaliesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControlesAnomalies::class);
    }

    // /**
    //  * @return ControlesAnomalies[] Returns an array of ControlesAnomalies objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ControlesAnomalies
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
