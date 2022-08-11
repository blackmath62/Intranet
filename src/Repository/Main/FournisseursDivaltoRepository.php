<?php

namespace App\Repository\Main;

use App\Entity\Main\FournisseursDivalto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FournisseursDivalto|null find($id, $lockMode = null, $lockVersion = null)
 * @method FournisseursDivalto|null findOneBy(array $criteria, array $orderBy = null)
 * @method FournisseursDivalto[]    findAll()
 * @method FournisseursDivalto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FournisseursDivaltoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FournisseursDivalto::class);
    }

    // /**
    //  * @return FournisseursDivalto[] Returns an array of FournisseursDivalto objects
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
    public function findOneBySomeField($value): ?FournisseursDivalto
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
