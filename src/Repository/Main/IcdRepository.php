<?php

namespace App\Repository\Main;

use App\Entity\Main\Icd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Icd>
 *
 * @method Icd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Icd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Icd[]    findAll()
 * @method Icd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IcdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Icd::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Icd $entity, bool $flush = true): void
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
    public function remove(Icd $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    //DELETE FROM icd

    // /**
    //  * @return Icd[] Returns an array of Icd objects
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
public function findOneBySomeField($value): ?Icd
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
