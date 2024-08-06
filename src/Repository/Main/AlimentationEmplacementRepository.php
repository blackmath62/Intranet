<?php

namespace App\Repository\Main;

use App\Entity\Main\AlimentationEmplacement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AlimentationEmplacement|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlimentationEmplacement|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlimentationEmplacement[]    findAll()
 * @method AlimentationEmplacement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlimentationEmplacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlimentationEmplacement::class);
    }

    // Liste retrait non soumis
    public function getEmplacementNonSoumis(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT  emplacement AS emplacement, u.pseudo AS user, DATE_FORMAT(a.createdAt, '%d/%m/%Y') AS dateCreation
         FROM alimentationemplacement a
         LEFT JOIN users u ON a.createdBy_id = u.id
         WHERE a.sendAt is null
         GROUP BY emplacement, user, dateCreation
         ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getGroupedOldLocationByEan($ean, $oldLocation)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select('a.oldLocation', 'SUM(a.qte) AS qte')
            ->where('a.sendAt IS NULL')
            ->andWhere('a.ean = :ean')
            ->andWhere('a.oldLocation = :oldLocation')
            ->groupBy('a.oldLocation')
            ->setParameter('ean', $ean)
            ->setParameter('oldLocation', $oldLocation);

        return $qb->getQuery()->getResult();
    }

    // Quantité à ajouter et retirer de l'emplacement
    public function getGroupedNewLocationByEan($ean, $emplacement)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT  a.emplacement, SUM(
        CASE
            WHEN a.oldLocation = 'Remove' THEN -1 * a.qte
            ELSE a.qte
        END
        ) AS qte
        FROM alimentationemplacement a
        WHERE a.sendAt IS NULL AND a.emplacement = '$emplacement' AND a.ean = '$ean'
        GROUP BY a.emplacement
         ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return AlimentationEmplacement[] Returns an array of AlimentationEmplacement objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('a')
    ->andWhere('a.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('a.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?AlimentationEmplacement
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
