<?php

namespace App\Repository\Main;

use App\Entity\Main\Affaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Affaires>
 *
 * @method Affaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Affaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Affaires[]    findAll()
 * @method Affaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affaires::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Affaires $entity, bool $flush = true): void
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
    public function remove(Affaires $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Affaires[] Returns an array of Affaires objects
    //  */

    public function findNotFinish()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.id AS id, a.code AS code, a.libelle AS libelle, a.tiers AS tiers, a.nom AS nom, a.start AS start,
         a.end AS end, a.progress AS progress, a.duration AS duration, a.etat AS etat, a.backgroundColor AS backgroundColor,
          a.textColor AS textColor, COUNT(p.entId) AS nbe
        FROM affairepiece p
        INNER JOIN affaires a ON a.code = p.affaire
        WHERE a.etat <> 'Termine' AND p.etat <> 'Termine'
        GROUP BY affaire
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function findFinish()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.id AS id, a.code AS code, a.libelle AS libelle,
        a.tiers AS tiers, a.nom AS nom, a.start AS start, a.end AS end,
        a.progress AS progress, a.duration AS duration, a.etat AS etat,
        a.backgroundColor AS backgroundColor, a.textColor AS textColor,
        COUNT(p.entId) AS nbe
        FROM affairepiece p
        INNER JOIN affaires a ON a.code = p.affaire
        WHERE a.etat IN ('Termine')
        GROUP BY affaire
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /*
public function findOneBySomeField($value): ?Affaires
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
