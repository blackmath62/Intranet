<?php

namespace App\Repository\Main;

use App\Entity\Main\ControleArticlesFsc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ControleArticlesFsc|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControleArticlesFsc|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControleArticlesFsc[]    findAll()
 * @method ControleArticlesFsc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControleArticlesFscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControleArticlesFsc::class);
    }


    // /**
    //  * @return ControleArticlesFsc[] Returns an array of ControleArticlesFsc objects
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
    public function findOneBySomeField($value): ?ControleArticlesFsc
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
