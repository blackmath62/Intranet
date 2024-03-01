<?php

namespace App\Repository\Main;

use App\Entity\Main\MouvPreparationCmdAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvPreparationCmdAdmin>
 *
 * @method MouvPreparationCmdAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method MouvPreparationCmdAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method MouvPreparationCmdAdmin[]    findAll()
 * @method MouvPreparationCmdAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvPreparationCmdAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvPreparationCmdAdmin::class);
    }

//    /**
//     * @return MouvPreparationCmdAdmin[] Returns an array of MouvPreparationCmdAdmin objects
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

//    public function findOneBySomeField($value): ?MouvPreparationCmdAdmin
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
