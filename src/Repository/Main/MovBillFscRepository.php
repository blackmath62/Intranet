<?php

namespace App\Repository\Main;

use DateTime;
use App\Entity\Main\MovBillFsc;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method MovBillFsc|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovBillFsc|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovBillFsc[]    findAll()
 * @method MovBillFsc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovBillFscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovBillFsc::class);
    }

    // Factures Clients FSC qui ont moins de 5 ans 
    public function getFactCliFiveYearsAgo():array
    {
        $fiveYearsAgo = new DateTime();
        $fiveYearsAgo = date('Y-m-d', strtotime('-5 years'));

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM movbillfsc
        WHERE movbillfsc.dateFact >= '$fiveYearsAgo'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    // /**
    //  * @return MovBillFsc[] Returns an array of MovBillFsc objects
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
    public function findOneBySomeField($value): ?MovBillFsc
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

