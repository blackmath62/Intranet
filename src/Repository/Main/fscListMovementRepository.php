<?php

namespace App\Repository\Main;

use App\Entity\Main\fscListMovement;
use DateTime;
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
    public function getCountTypeDocByOrderFsc($id): array
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
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Compter le nombre de piéce pour toutes les commandes
    public function getCountTypeDocByOrderFscForAll(): array
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
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getListFacture(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT documentsfsc.file AS fichier, documentsfsc.fscListMovement_id AS movid, fsclistmovement.numFact
        FROM documentsfsc INNER JOIN fsclistmovement ON fsclistmovement.id = documentsfsc.fscListMovement_id
        WHERE documentsfsc.TypeDoc_id = 9
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // pour l'envoi de mail automatique permetant l'alimentation des documents fsc sur les piéces fournisseurs
    public function getPieceFscAAlimenter(): array
    {
        $d = new DateTime('2021/01/01');
        $d = $d->format('Y-m-d');
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM fsclistmovement
        WHERE fsclistmovement.probleme = 0 AND (fsclistmovement.dateCmd >= '$d' OR fsclistmovement.dateBl >= '$d' OR fsclistmovement.dateFact >= '$d')
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Extraction des factures d'achat d'une période
    public function getExtractPeriod($start, $end): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM fsclistmovement a
        WHERE a.dateFact BETWEEN '$start' AND '$end'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
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
