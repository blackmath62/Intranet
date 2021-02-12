<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Art;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Art|null find($id, $lockMode = null, $lockVersion = null)
 * @method Art|null findOneBy(array $criteria, array $orderBy = null)
 * @method Art[]    findAll()
 * @method Art[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtRepository extends ServiceEntityRepository
{
    private $em;
    private $registry;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Art::class);
        $em = $this->getEntityManager();
    }


    public function entityArticles($entityManager){
        
        
        //$em = $this->registry->getConnection();
        //$config = $this->em->getConfiguration();
        dd($this->em);
        
        $ref = 'CO2130';
        $query = $this->em->createQuery(
            'SELECT p
            FROM App\Entity\Divalto\Art p
            WHERE p.ref > :ref
            ORDER BY p.ref ASC'
        )->setParameter('ref', $ref);

        // returns an array of Product objects
        return $query->getResult();
    }

    // /**
    //  * @return Art[] Returns an array of Art objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->innerJoin('a.activiteEnseignements', 'acti')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Art
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
