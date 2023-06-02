<?php

namespace App\Repository\Main;

use App\Entity\Main\AffairePiece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AffairePiece>
 *
 * @method AffairePiece|null find($id, $lockMode = null, $lockVersion = null)
 * @method AffairePiece|null findOneBy(array $criteria, array $orderBy = null)
 * @method AffairePiece[]    findAll()
 * @method AffairePiece[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffairePieceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AffairePiece::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(AffairePiece $entity, bool $flush = true): void
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
    public function remove(AffairePiece $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findNotFinish($affaire)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM affairePiece a
        WHERE a.etat <> 'Termine' AND a.affaire = '$affaire'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return AffairePiece[] Returns an array of AffairePiece objects
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
public function findOneBySomeField($value): ?AffairePiece
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
