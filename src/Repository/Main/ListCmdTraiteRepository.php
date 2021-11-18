<?php

namespace App\Repository\Main;

use App\Entity\Main\ListCmdTraite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListCmdTraite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListCmdTraite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListCmdTraite[]    findAll()
 * @method ListCmdTraite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListCmdTraiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListCmdTraite::class);
    }

    // /**
    //  * @return ListCmdTraite[] Returns an array of ListCmdTraite objects
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
    public function findOneBySomeField($value): ?ListCmdTraite
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
