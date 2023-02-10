<?php

namespace App\Repository\Main;

use App\Entity\Main\CmdRobyDelaiAccepteReporte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmdRobyDelaiAccepteReporte|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmdRobyDelaiAccepteReporte|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmdRobyDelaiAccepteReporte[]    findAll()
 * @method CmdRobyDelaiAccepteReporte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmdRobyDelaiAccepteReporteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmdRobyDelaiAccepteReporte::class);
    }

    // /**
    //  * @return CmdRobyDelaiAccepteReporte[] Returns an array of CmdRobyDelaiAccepteReporte objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('c')
    ->andWhere('c.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('c.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    // Compter le nombre de notes par commande
    public function getAllDataAndCountNoteByCmd(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT cmdrobydelaiacceptereporte.id,cmdrobydelaiacceptereporte.identification, cmdrobydelaiacceptereporte.statut,
        cmdrobydelaiacceptereporte.createdAt, cmdrobydelaiacceptereporte.modifiedAt, cmdrobydelaiacceptereporte.tiers,
        cmdrobydelaiacceptereporte.Nom, cmdrobydelaiacceptereporte.dateCmd, cmdrobydelaiacceptereporte.notreRef,
        cmdrobydelaiacceptereporte.delaiAccepte,cmdrobydelaiacceptereporte.delaiReporte,cmdrobydelaiacceptereporte.modifiedBy_id, cmdrobydelaiacceptereporte.cmd,COUNT(note.cmdRobyDelaiAccepteReporte_id)
        FROM note
        INNER JOIN cmdrobydelaiacceptereporte ON cmdrobydelaiacceptereporte.id = note.cmdRobyDelaiAccepteReporte_id
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Total HT des commandes en attentes
    public function getTotalHt()
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT SUM(ht) AS total
        FROM cmdrobydelaiacceptereporte
        WHERE cmdrobydelaiacceptereporte.statut <> 'Terminé'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchOne();
    }

    /*
public function findOneBySomeField($value): ?CmdRobyDelaiAccepteReporte
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
