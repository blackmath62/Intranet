<?php

namespace App\Repository\Main;

use App\Entity\Main\MailList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MailList|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailList|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailList[]    findAll()
 * @method MailList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailList::class);
    }

    // Email d'envoi
    public function getEmailEnvoi()
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT maillist.email AS email
        FROM maillist WHERE maillist.page = 'app_admin_email' AND maillist.SecondOption = 'envoi'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // Email de traitement
    public function getEmailTreatement()
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT maillist.email AS email
        FROM maillist WHERE maillist.page = 'app_admin_email' AND maillist.SecondOption = 'traitement'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    // /**
    //  * @return MailList[] Returns an array of MailList objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('m')
    ->andWhere('m.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('m.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?MailList
{
return $this->createQueryBuilder('m')
->andWhere('m.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
