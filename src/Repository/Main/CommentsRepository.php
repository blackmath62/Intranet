<?php

namespace App\Repository\Main;

use App\Entity\Main\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
    //  */
   
    public function findAllExceptStatu($value)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT comments.ticket_id, COUNT(DISTINCT comments.title) AS nbComments
        FROM `comments`
        INNER JOIN tickets ON tickets.statu_id <> $value AND tickets.id = comments.ticket_id
        GROUP BY comments.ticket_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    

    /*
    public function findOneBySomeField($value): ?Comments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
