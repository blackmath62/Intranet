<?php

namespace App\Repository\Main;

use App\Entity\Main\ListDivaltoUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListDivaltoUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListDivaltoUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListDivaltoUsers[]    findAll()
 * @method ListDivaltoUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListDivaltoUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListDivaltoUsers::class);
    }

    // /**
    //  * @return ListDivaltoUsers[] Returns an array of ListDivaltoUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ListDivaltoUsers
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
