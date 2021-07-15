<?php

namespace App\Repository\Main;

use App\Entity\Main\CopyFou;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CopyFou|null find($id, $lockMode = null, $lockVersion = null)
 * @method CopyFou|null findOneBy(array $criteria, array $orderBy = null)
 * @method CopyFou[]    findAll()
 * @method CopyFou[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CopyFouRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CopyFou::class);
    }

    // /**
    //  * @return CopyFou[] Returns an array of CopyFou objects
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
    public function findOneBySomeField($value): ?CopyFou
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
