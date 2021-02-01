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

    // /**
    //  * @return Sart[] Returns an array of Sart objects
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
    public function findOneBySomeField($value): ?Sart
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
