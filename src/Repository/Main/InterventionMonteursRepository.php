<?php

namespace App\Repository\Main;

use App\Entity\Main\InterventionMonteurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InterventionMonteurs>
 *
 * @method InterventionMonteurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterventionMonteurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterventionMonteurs[]    findAll()
 * @method InterventionMonteurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionMonteursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterventionMonteurs::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InterventionMonteurs $entity, bool $flush = true): void
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
    public function remove(InterventionMonteurs $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // Top 5 des chantiers actuels
    public function getChantiersActuel()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM InterventionMonteurs i
        INNER JOIN affaires a ON a.code = i.code
        WHERE NOW() BETWEEN i.start AND i.end OR (i.start > NOW() AND i.end > NOW() )
        LIMIT 5
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return InterventionMonteurs[] Returns an array of InterventionMonteurs objects
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
public function findOneBySomeField($value): ?InterventionMonteurs
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
