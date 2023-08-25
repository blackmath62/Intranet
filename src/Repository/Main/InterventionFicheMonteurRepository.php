<?php

namespace App\Repository\Main;

use App\Entity\Main\InterventionFicheMonteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InterventionFicheMonteur>
 *
 * @method InterventionFicheMonteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterventionFicheMonteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterventionFicheMonteur[]    findAll()
 * @method InterventionFicheMonteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionFicheMonteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterventionFicheMonteur::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InterventionFicheMonteur $entity, bool $flush = true): void
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
    public function remove(InterventionFicheMonteur $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InterventionFicheMonteur[] Returns an array of InterventionFicheMonteur objects
    //  */

    public function findFicheAttenteValidation()
    {
        return $this->createQueryBuilder('f')
            ->where('f.lockedBy is not null')
            ->andWhere('f.validedBy is null')
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFicheManquantes()
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.intervention', 'i')
        //->where('f.lockedBy is not null')
        //->andWhere('f.validedBy is null')
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFicheDatesIncohérentes()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT i.id AS intId, f.id AS ficheId, u.pseudo AS pseudo, f.createdAt AS createdAt, a.code AS code, i.start AS start, i.end AS end
        FROM interventionfichemonteur f
        INNER JOIN interventionmonteurs i ON i.id = f.intervention_id
        INNER JOIN users u ON u.id = f.intervenant_id
        INNER JOIN affaires a ON a.id = i.code_id
        WHERE DATE_FORMAT(f.createdAt, '%Y/%m/%d') NOT BETWEEN DATE_FORMAT(i.start, '%Y/%m/%d') AND DATE_FORMAT(i.end, '%Y/%m/%d')
        AND f.validedAt IS NULL
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function findFicheSansHeures()
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.heures', 'h')
            ->where('h is null')
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFichePeriode($user, $start, $end)
    {
        return $this->createQueryBuilder('i')
            ->where('f.intervenant = :user')
            ->andWhere('f.validedBy is not null')
            ->andWhere('f.createdAt between :start and :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPointagePeriode($start, $end)
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.intervention', 'i')
            ->Where('i.createdAt between :start and :end')
            ->andWhere('f.validedBy is not null')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('i.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;

    }

    /*$queryBuilder->select('rm')
    ->from('App:Room', 'rm')
    ->leftJoin('rm.reservations', 'r', 'WITH', $queryBuilder->expr()->andX(
    $queryBuilder->expr()->lt('r.start', '?1'),
    $queryBuilder->expr()->gt('r.stop', '?2'),
    $queryBuilder->expr()->neq('r.status', '?3')
    )
    )->where(
    $queryBuilder->expr()->andX(
    $queryBuilder->expr()->isNull('r')
    )
    )->setParameters(
    array(
    1 => $stop,
    2 => $start,
    3 => Reservation::STATUS_EXPIRED
    )
    );*/

    /*
public function findOneBySomeField($value): ?InterventionFicheMonteur
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
