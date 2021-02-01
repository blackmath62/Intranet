<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Vrp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vrp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vrp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vrp[]    findAll()
 * @method Vrp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VrpRepository extends ServiceEntityRepository
{
    private $connection;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vrp::class);
        $this->connection = $registry->getManager('divaltoreel');
    }

    // /**
    //  * @return Vrp[] Returns an array of Vrp objects
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
    public function findOneBySomeField($value): ?Vrp
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
