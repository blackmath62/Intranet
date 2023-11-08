<?php

namespace App\Repository\Main;

use App\Entity\Main\StatutsGeneraux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutsGeneraux>
 *
 * @method StatutsGeneraux|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutsGeneraux|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutsGeneraux[]    findAll()
 * @method StatutsGeneraux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutsGenerauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutsGeneraux::class);
    }

//    /**
//     * @return StatutsGeneraux[] Returns an array of StatutsGeneraux objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatutsGeneraux
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
