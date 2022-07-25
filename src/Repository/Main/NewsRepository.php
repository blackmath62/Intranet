<?php

namespace App\Repository\Main;

use DateTime;
use App\Entity\Main\News;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function getNews()
    {
        $d = new DateTime();
        $dc = date_modify($d, '-30 Days');
        $dc= $dc->format('Y') . '-' . $dc->format('m') . '-' . $dc->format('d');
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
        FROM news
        WHERE news.createdAt >= '$dc'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // /**
    //  * @return News[] Returns an array of News objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?News
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
