<?php

namespace App\Repository\Main;

use App\Entity\Main\Annuaire;
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

    public function findFinish()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.id AS id, a.code AS code, a.libelle AS libelle,
         a.tiers AS tiers, a.nom AS nom, a.start AS start, a.end AS end,
         a.progress AS progress, a.duration AS duration, a.etat AS etat,
         a.backgroundColor AS backgroundColor, a.textColor AS textColor,
         COUNT(p.entId) AS nbe
         FROM affairepiece p
         INNER JOIN affaires a ON a.code = p.affaire
         WHERE a.etat IN ('Termine')
         GROUP BY affaire
         ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /*
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
    /*
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
    /*
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
 */
}
