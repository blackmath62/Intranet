<?php

namespace App\Repository;

use App\Entity\FscListMovement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FscListMovement|null find($id, $lockMode = null, $lockVersion = null)
 * @method FscListMovement|null findOneBy(array $criteria, array $orderBy = null)
 * @method FscListMovement[]    findAll()
 * @method FscListMovement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FscListMovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FscListMovement::class);
    }

    // /**
    //  * @return FscListMovement[] Returns an array of FscListMovement objects
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
    public function findOneBySomeField($value): ?FscListMovement
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
