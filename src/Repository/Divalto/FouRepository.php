<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Fou;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fou|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fou|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fou[]    findAll()
 * @method Fou[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FouRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fou::class);
    }

     /**
     * @return Fou[] Returns an array of Fou objects
     */
    
    public function findByNom($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dos = :val')
            #->innerJoin('a.activiteEnseignements', 'acti')
            ->setParameter('val', $value)
            ->orderBy('a.nom', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Fou
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
