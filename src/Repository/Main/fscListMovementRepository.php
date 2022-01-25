<?php

namespace App\Repository\Main;

use App\Entity\Main\fscListMovement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method fscListMovement|null find($id, $lockMode = null, $lockVersion = null)
 * @method fscListMovement|null findOneBy(array $criteria, array $orderBy = null)
 * @method fscListMovement[]    findAll()
 * @method fscListMovement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class fscListMovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, fscListMovement::class);
    }

    // /**
    //  * @return fscListMovement[] Returns an array of fscListMovement objects
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
    public function findOneBySomeField($value): ?fscListMovement
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
