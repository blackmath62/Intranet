<?php

namespace App\Repository\Main;

use App\Entity\Main\Trackings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracking[]    findAll()
 * @method Tracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trackings::class);
    }

    public function getLastConnect():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT user_id, MAX(createdAt) AS createdAt 
        FROM `trackings`
        GROUP BY user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStatesIntranet():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT COUNT(trackings.page) AS CountPage,trackings.page AS Page
        FROM trackings
        INNER JOIN users ON users.id = trackings.user_id
        WHERE users.id NOT IN (1, 3)
        GROUP BY trackings.page  
        ORDER BY CountPage DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserStatesIntranet($id):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT COUNT(trackings.page) AS CountPage,trackings.page AS Page, MAX(trackings.createdAt) AS LastView
        FROM trackings
        INNER JOIN users ON users.id = trackings.user_id
        WHERE users.id = $id
        GROUP BY trackings.page  
        ORDER BY LastView DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    
    // /**
    //  * @return Tracking[] Returns an array of Tracking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tracking
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
