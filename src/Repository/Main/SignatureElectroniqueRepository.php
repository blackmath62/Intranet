<?php

namespace App\Repository\Main;

use App\Entity\Main\SignatureElectronique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SignatureElectronique>
 *
 * @method SignatureElectronique|null find($id, $lockMode = null, $lockVersion = null)
 * @method SignatureElectronique|null findOneBy(array $criteria, array $orderBy = null)
 * @method SignatureElectronique[]    findAll()
 * @method SignatureElectronique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SignatureElectroniqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SignatureElectronique::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SignatureElectronique $entity, bool $flush = true): void
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
    public function remove(SignatureElectronique $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return SignatureElectronique[] Returns an array of SignatureElectronique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SignatureElectronique
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
