<?php

namespace App\Repository\Main;

use App\Entity\Main\JardinewProductConditions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JardinewProductConditions>
 *
 * @method JardinewProductConditions|null find($id, $lockMode = null, $lockVersion = null)
 * @method JardinewProductConditions|null findOneBy(array $criteria, array $orderBy = null)
 * @method JardinewProductConditions[]    findAll()
 * @method JardinewProductConditions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JardinewProductConditionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JardinewProductConditions::class);
    }

//    /**
//     * @return JardinewProductConditions[] Returns an array of JardinewProductConditions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JardinewProductConditions
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
