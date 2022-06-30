<?php

namespace App\Repository\Main;

use App\Entity\Main\ConduiteTravauxAddPiece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConduiteTravauxAddPiece|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConduiteTravauxAddPiece|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConduiteTravauxAddPiece[]    findAll()
 * @method ConduiteTravauxAddPiece[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConduiteTravauxAddPieceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConduiteTravauxAddPiece::class);
    }

    // /**
    //  * @return ConduiteTravauxAddPiece[] Returns an array of ConduiteTravauxAddPiece objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ConduiteTravauxAddPiece
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
