<?php

namespace App\Repository\Main;

use App\Entity\Main\DocumentsReglementairesFsc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentsReglementairesFsc|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentsReglementairesFsc|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentsReglementairesFsc[]    findAll()
 * @method DocumentsReglementairesFsc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentsReglementairesFscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentsReglementairesFsc::class);
    }

    // /**
    //  * @return DocumentsReglementairesFsc[] Returns an array of DocumentsReglementairesFsc objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentsReglementairesFsc
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
