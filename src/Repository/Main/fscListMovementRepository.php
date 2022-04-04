<?php

namespace App\Repository\Main;

use App\Entity\Main\fscListMovement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method fscListMovement|null find($id, $lockMode = null, $lockVersion = null)
 * @method fscListMovement|null findOneBy(array $criteria, array $orderBy = null)
 * @method fscListMovement[]    findAll()
 * @method fscListMovement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class fscListMovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, fscListMovement::class);
    }

    // Compter le nombre de piéce pour la commande ciblé
    public function getCountTypeDocByOrderFsc($id):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT
        typedocumentfsc.id AS idTypeDoc, typedocumentfsc.title AS titleTypeDoc, fsclistmovement.id AS idMov, COUNT(fsclistmovement.id) AS countMov
        FROM typedocumentfsc
        INNER JOIN documentsfsc ON typedocumentfsc.id = documentsfsc.TypeDoc_id
        INNER JOIN fsclistmovement ON fsclistmovement.id = documentsfsc.fscListMovement_id
        WHERE fsclistmovement.id = '$id'
        GROUP BY typedocumentfsc.id, typedocumentfsc.title, fsclistmovement.id
        ORDER BY fsclistmovement.id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Compter le nombre de piéce pour toutes les commandes
    public function getCountTypeDocByOrderFscForAll():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT
        typedocumentfsc.id AS idTypeDoc, typedocumentfsc.title AS titleTypeDoc, fsclistmovement.id AS idMov, COUNT(fsclistmovement.id) AS countMov
        FROM typedocumentfsc
        INNER JOIN documentsfsc ON typedocumentfsc.id = documentsfsc.TypeDoc_id
        INNER JOIN fsclistmovement ON fsclistmovement.id = documentsfsc.fscListMovement_id
        GROUP BY typedocumentfsc.id, typedocumentfsc.title, fsclistmovement.id
        ORDER BY fsclistmovement.id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    
    // /**
    //  * @return fscListMovement[] Returns an array of fscListMovement objects
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
    public function findOneBySomeField($value): ?fscListMovement
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
