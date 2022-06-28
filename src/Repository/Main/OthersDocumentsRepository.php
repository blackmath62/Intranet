<?php

namespace App\Repository\Main;

use App\Entity\Main\OthersDocuments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OthersDocuments|null find($id, $lockMode = null, $lockVersion = null)
 * @method OthersDocuments|null findOneBy(array $criteria, array $orderBy = null)
 * @method OthersDocuments[]    findAll()
 * @method OthersDocuments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OthersDocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OthersDocuments::class);
    }

    // /**
    //  * @return OthersDocuments[] Returns an array of OthersDocuments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OthersDocuments
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
