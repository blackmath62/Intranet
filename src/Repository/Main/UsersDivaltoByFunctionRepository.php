<?php

namespace App\Repository\Main;

use App\Entity\Main\UsersDivaltoByFunction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsersDivaltoByFunction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersDivaltoByFunction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersDivaltoByFunction[]    findAll()
 * @method UsersDivaltoByFunction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersDivaltoByFunctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersDivaltoByFunction::class);
    }

    // /**
    //  * @return UsersDivaltoByFunction[] Returns an array of UsersDivaltoByFunction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersDivaltoByFunction
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
