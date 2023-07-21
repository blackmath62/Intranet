<?php

namespace App\Repository\Main;

use App\Entity\Main\InterventionFichesMonteursHeures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InterventionFichesMonteursHeures>
 *
 * @method InterventionFichesMonteursHeures|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterventionFichesMonteursHeures|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterventionFichesMonteursHeures[]    findAll()
 * @method InterventionFichesMonteursHeures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionFichesMonteursHeuresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterventionFichesMonteursHeures::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InterventionFichesMonteursHeures $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(InterventionFichesMonteursHeures $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InterventionFichesMonteursHeures[] Returns an array of InterventionFichesMonteursHeures objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InterventionFichesMonteursHeures
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
