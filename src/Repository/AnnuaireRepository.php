<?php

namespace App\Repository;

use App\Entity\Annuaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annuaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annuaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annuaire[]    findAll()
 * @method Annuaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annuaire::class);
    }

    /**
    * @return Annuaire[]
    */

    public function findByNom(int $value):array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT nom
            FROM App\Entity\Annuaire nom
            WHERE Annuaire.interne > :value'
        )->setParameter('interne', $value);

        // returns an array of Product objects
        return $query->getResult()
        ;
    }


    public function test()
    {
        echo "je suis un test </br>";
    }

    
    public function test2($value)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Annuaire p
            WHERE p.societe = :val1 AND p.id >= :val2
            ORDER BY p.id ASC'
        )->setParameter('val1', $value[0])
        ->setParameter('val2', $value[1])
        ;

        // returns an array of Product objects
        return $query->getResult();
    }
    
}
