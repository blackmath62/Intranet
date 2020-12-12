<?php

namespace App\Repository;

use App\Entity\Divalto\Ent;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ent[]    findAll()
 * @method Ent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ent::class);
    }
    /*
    public function getPino()
    {
        $queryBuilder = $this->createQueryBuilder("Ent")
            ->orderBy("Ent.pidt", "desc")
            ->setMaxResults(100);

            return $queryBuilder->getQuery()->getResult();
    }
    */




    public function getSPino($facture)
    {
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('Ent', 'Ent');
        $rsm->addFieldResult('Ent', 'Pidt', 'Pidt');
        $rsm->addFieldResult('Ent', 'Tiers', 'Tiers');
        $rsm->addFieldResult('Ent', 'Pino', 'Pino');
        $rsm->addFieldResult('Ent', 'Piref', 'Piref' );
        $rsm->addFieldResult('Ent', 'Voltot', 'Voltot');
        $rsm->addFieldResult('Ent', 'Poitot', 'Poitot');
        $rsm->addJoinedEntityResult('Tiers' , 'Fou', 'Ent', 'Tiers');
        $rsm->addFieldResult('Fou', 'Tiers', 'Tiers');
        $rsm->addFieldResult('Fou', 'Nom', 'Nom');
        $rsm->addFieldResult('Fou', 'Pay', 'Pay');
        $rsm->addJoinedEntityResult('Fano' , 'Mouv', 'Ent', 'Pino');
        $rsm->addJoinedEntityResult('Fadt' , 'Mouv', 'Ent', 'Pidt');
        $rsm->addFieldResult('Mouv', 'Op', 'Op');
      
        $sql = "SELECT Ent.pino, Ent.Piref, Ent.Voltot, Fou.Nom, Mouv.fadt
                FROM Ent Ent
                INNER JOIN Fou Fou ON Fou.dos = Ent.Dos AND Fou.Tiers = Ent.Tiers
                INNER JOIN Mouv Mouv ON Mouv.Dos = Ent.Dos AND Mouv.
                WHERE Ent.Dos = 1 AND YEAR(Ent.Pidt) IN (2019)
        ";
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $facture);

        $Articles = $query->getResult();
        return $this->render('deb_roby/index.html.twig', [
            'controller_name' => 'DebRobyController',
            'Articles' => $Articles
        ]);
    }

    



    /**
    * @return Ent[] Returns an array of Ent objects
    */
   /*
    public function findByTiers($value)
    {
        return $this->createQueryBuilder('Ent')
            ->andWhere('Ent.tiers = :val')
            #->innerJoin('a.activiteEnseignements', 'acti')
            ->setParameter('val', $value)
            #->orderBy('Ent.op', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
 */

    /*
    public function findOneBySomeField($value): ?Ent
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
