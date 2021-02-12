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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
          
    }
    
    /**
     * @return Mouv[]
     */
    public function getStatesMouvEv($entityManager, $minFadt, $maxFadt):array
    {

        $query = $entityManager->createQuery(
            "SELECT p.tiers,
                    p.repr0001,
                    SUM(p.mont) AS SumMontant
             FROM App\Entity\Divalto\Mouv p
              WHERE p.fadt >= :minFadt 
                AND p.fadt <= :maxFadt 
                AND p.picod = 4 
                AND p.ticod = 'C' 
                AND p.dos = 1
                AND p.repr0001 <> ''
                GROUP BY p.tiers, p.repr0001
                "
              //INNER JOIN p.tiers c
        //c.nom, 
    )->setParameter('minFadt', $minFadt)
    ->setParameter('maxFadt', $maxFadt);
    
    return $query->getResult();
    
}

/**
 * @return Mouv[]
 */
public function test($entityManager, $value, $maxFano):array
{

   $query = $entityManager->createQuery(
    "SELECT p
    FROM App\Entity\Divalto\Mouv p
    WHERE p.fadt > :fadt AND p.fadt <= :maxFadt AND p.picod = 4 AND p.ticod = 'C'
    GROUP BY p.tiers"
)->setParameter('fadt', $value)
 ->setParameter('maxFadt', $maxFano);

return $query->getResult();

}

/**
 * @return Mouv[]
 */
public function test2():array
{

   $entityManager = $this->getEntityManager();
   $query = $entityManager->createQuery(
    "SELECT p
    FROM App\Entity\Divalto\Mouv p
    WHERE p.fano = :fano AND p.picod = 4 AND p.ticod = 'C'
    "
)->setParameter('fano', 19015120);
//INNER JOIN p.tiers c

return $query->getResult();

}
}
