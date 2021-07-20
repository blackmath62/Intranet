<?php

namespace App\Repository\Main;

use App\Entity\Main\statusHoliday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method statusHoliday|null find($id, $lockMode = null, $lockVersion = null)
 * @method statusHoliday|null findOneBy(array $criteria, array $orderBy = null)
 * @method statusHoliday[]    findAll()
 * @method statusHoliday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class statusHolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, statusHoliday::class);
    }

    // /**
    //  * @return statusHoliday[] Returns an array of statusHoliday objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?statusHoliday
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
