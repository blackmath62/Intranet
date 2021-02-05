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

    /**
    * @return Annuaire[]
    */
    
    public function findTest($value):array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Annuaire p
            WHERE p.interne >= :min AND p.interne <= :max
            ORDER BY p.id ASC'
        )->setParameter('min', $value['min'])
        ->setParameter('max', $value['max'])
        ;

        // returns an array of Product objects
        return $query->getResult();
    }
    /**
     * @return Annuaire[]
     */
    
    public function findAllGreaterThanInterne(int $interne): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Annuaire p
            WHERE p.interne > :interne
            ORDER BY p.interne ASC'
        )->setParameter('interne', $interne);

        // returns an array of Product objects
        return $query->getResult();
    }
    
}
