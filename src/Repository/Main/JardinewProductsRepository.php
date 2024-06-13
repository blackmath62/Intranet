<?php

namespace App\Repository\Main;

use App\Entity\Main\JardinewProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JardinewProducts>
 *
 * @method JardinewProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method JardinewProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method JardinewProducts[]    findAll()
 * @method JardinewProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JardinewProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JardinewProducts::class);
    }

//    /**
//     * @return JardinewProducts[] Returns an array of JardinewProducts objects
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

//    public function findOneBySomeField($value): ?JardinewProducts
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
