<?php

namespace App\Repository\Main;

use App\Entity\Main\ProduitsCommissionnaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProduitsCommissionnaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitsCommissionnaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitsCommissionnaires[]    findAll()
 * @method ProduitsCommissionnaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsCommissionnairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitsCommissionnaires::class);
    }

    // /**
    //  * @return ProduitsCommissionnaires[] Returns an array of ProduitsCommissionnaires objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProduitsCommissionnaires
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
