<?php

namespace App\Repository\Main;

use App\Entity\Main\RetraitMarchandisesEan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RetraitMarchandisesEan|null find($id, $lockMode = null, $lockVersion = null)
 * @method RetraitMarchandisesEan|null findOneBy(array $criteria, array $orderBy = null)
 * @method RetraitMarchandisesEan[]    findAll()
 * @method RetraitMarchandisesEan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetraitMarchandisesEanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RetraitMarchandisesEan::class);
    }

    // Liste retrait non soumis
    public function getRetraiNonSoumis(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT  chantier AS chantier, u.pseudo AS user, DATE_FORMAT(r.createdAt, '%d/%m/%Y') AS dateCreation
        FROM retraitmarchandisesean r
        LEFT JOIN users u ON r.createdBy_id = u.id
        WHERE r.sendAt is null
        GROUP BY chantier, user, dateCreation
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // /**
    //  * @return RetraitMarchandisesEan[] Returns an array of RetraitMarchandisesEan objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('r')
    ->andWhere('r.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('r.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?RetraitMarchandisesEan
{
return $this->createQueryBuilder('r')
->andWhere('r.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
