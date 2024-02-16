<?php

namespace App\Repository\Main;

use App\Entity\Main\MouvPreprationCommandeSaisie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvPreprationCommandeSaisie>
 *
 * @method MouvPreprationCommandeSaisie|null find($id, $lockMode = null, $lockVersion = null)
 * @method MouvPreprationCommandeSaisie|null findOneBy(array $criteria, array $orderBy = null)
 * @method MouvPreprationCommandeSaisie[]    findAll()
 * @method MouvPreprationCommandeSaisie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvPreprationCommandeSaisieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvPreprationCommandeSaisie::class);
    }

//    /**
//     * @return MouvPreprationCommandeSaisie[] Returns an array of MouvPreprationCommandeSaisie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MouvPreprationCommandeSaisie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
