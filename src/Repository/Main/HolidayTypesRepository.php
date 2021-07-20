<?php

namespace App\Repository\Main;

use App\Entity\Main\HolidayTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HolidayTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method HolidayTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method HolidayTypes[]    findAll()
 * @method HolidayTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HolidayTypes::class);
    }

    // /**
    //  * @return HolidayTypes[] Returns an array of HolidayTypes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HolidayTypes
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
