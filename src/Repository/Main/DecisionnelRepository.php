<?php

namespace App\Repository\Main;

use App\Entity\Main\Decisionnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Decisionnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Decisionnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Decisionnel[]    findAll()
 * @method Decisionnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DecisionnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Decisionnel::class);
    }

    // /**
    //  * @return Decisionnel[] Returns an array of Decisionnel objects
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
    public function findOneBySomeField($value): ?Decisionnel
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
