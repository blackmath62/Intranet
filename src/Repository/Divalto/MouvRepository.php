<?php

namespace App\Repository\Divalto;


use App\Entity\Divalto\Mouv;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Mouv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mouv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mouv[]    findAll()
 * @method Mouv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvRepository extends ServiceEntityRepository
{   
    private $connection;
    private $em;
    private $registry;
    public function __construct(ManagerRegistry $registry)
    {
        //$this->connection = $registry->getManager('divaltoreel');
        //$this->entityManager = $registry->getConnection('divaltoreel');
        parent::__construct($registry, Mouv::class);
            
    }
    /**
     * @return Mouv[]
     */
    public function test($entityManager, $value, $maxFano):array
    {
        //echo 'je suis un test';
        //$productManager = $this->em->getDoctrine()->getManagerForClass(Mouv::class);
        //$entityManager = $this->getEntityManager()->getManager('divaltoreel');
        
       //$entityManager = $this->getEntityManager();
       //dd($entityManager);

       $query = $entityManager->createQuery(
        "SELECT p
        FROM App\Entity\Divalto\Mouv p
        WHERE p.fadt > :fadt AND p.fadt <= :maxFadt AND p.picod = 4 AND p.ticod = 'C'
        ORDER BY p.fano ASC"
    )->setParameter('fadt', $value)
     ->setParameter('maxFadt', $maxFano);
    //dd($query->getResult());
    // returns an array of Product objects
    return $query->getResult();

    }
    
  
    /*
    /**
    * @return Mouv[] Returns an array of Mouv objects
    */
    /*
    // cette requête fonctionne mais ne tient pas compte du between
    public function findByFadt($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.fadt >= :from')
            ->andWhere('a.fadt <= :to')
            ->orderBy('a.tiers', 'DESC')
            ->setParameter('from', $value[0] )
            ->setParameter('to', $value[1] )
            ->getQuery()
        ;
    }
    */
/*
    public function findByRepr0001($value)
    {
        return $this->createQueryBuilder('a')
            ->select('a.fano , a.fadt, a.repr0001')
            ->Where('a.fadt >= :from ')
            ->andWhere('a.fadt <= :to ')
            ->andWhere('a.repr0001 = :vrp ')
            #->orderBy('a.fano', 'DESC')
            ->setParameter('from', $value[0])
            ->setParameter('to', $value[1])
            ->setParameter('vrp', $value[2])
            ->setMaxResults(100)
            ->getQuery()
        ;
    }
*/


}
