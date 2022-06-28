<?php

namespace App\Repository\Main;

use App\Entity\Main\ConduiteDeTravauxMe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConduiteDeTravauxMe|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConduiteDeTravauxMe|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConduiteDeTravauxMe[]    findAll()
 * @method ConduiteDeTravauxMe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConduiteDeTravauxMeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConduiteDeTravauxMe::class);
    }

    // Liste des piéces Nok Conduite de travaux
    public function getNok():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ct.id AS ident, ct.codeClient AS codeClient, ct.nom AS nom, ct.adresseLivraison AS adresseLivraison, 
		ct.affaire AS affaire, ct.modeDeTransport AS modeDeTransport, ct.op AS op, ct.dateCmd AS dateCmd, 
        ct.numCmd AS numCmd, ct.dateBl AS dateBl, ct.numeroBl AS numeroBl, ct.dateFacture AS dateFacture, 
        ct.numeroFacture AS numeroFacture, ct.delaiDemande AS delaiDemande, ct.delaiAccepte AS delaiAccepte,
        ct.delaiReporte AS delaiReporte, ct.dateDebutChantier AS dateDebutChantier, 
        ct.dateFinChantier AS dateFinChantier, ct.etat AS etat, ct.dureeTravaux AS dureeTravaux,
        ct.updatedAt AS updatedAt, ct.updatedBy_id AS updatedBy, ct.entId AS entId,COUNT(od.identifiant) AS nbeDocs
        FROM conduitedetravauxme ct
    	LEFT JOIN othersdocuments od
        	ON LTRIM(RTRIM(ct.entId)) = LTRIM(RTRIM(od.identifiant)) and 'conduiteTravaux' = RTRIM(LTRIM(od.tables))
        WHERE ct.etat <> 'Termine'
        GROUP BY ct.id  
        ORDER BY `nbeDocs`  DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // Liste des piéces ok Conduite de travaux
    public function getOk():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ct.id AS ident, ct.codeClient AS codeClient, ct.nom AS nom, ct.adresseLivraison AS adresseLivraison, 
		ct.affaire AS affaire, ct.modeDeTransport AS modeDeTransport, ct.op AS op, ct.dateCmd AS dateCmd, 
        ct.numCmd AS numCmd, ct.dateBl AS dateBl, ct.numeroBl AS numeroBl, ct.dateFacture AS dateFacture, 
        ct.numeroFacture AS numeroFacture, ct.delaiDemande AS delaiDemande, ct.delaiAccepte AS delaiAccepte,
        ct.delaiReporte AS delaiReporte, ct.dateDebutChantier AS dateDebutChantier, 
        ct.dateFinChantier AS dateFinChantier, ct.etat AS etat, ct.dureeTravaux AS dureeTravaux,
        ct.updatedAt AS updatedAt, ct.updatedBy_id AS updatedBy, ct.entId AS entId,COUNT(od.identifiant) AS nbeDocs
        FROM conduitedetravauxme ct
    	LEFT JOIN othersdocuments od
        	ON LTRIM(RTRIM(ct.entId)) = LTRIM(RTRIM(od.identifiant)) and 'conduiteTravaux' = RTRIM(LTRIM(od.tables))
        WHERE ct.etat = 'Termine'
        GROUP BY ct.id  
        ORDER BY `nbeDocs`  DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Nombre de commentaires lié à cette affaire conduite de travaux OK
    public function getCommentsOk():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ct.id AS ident,
        CASE
        WHEN c.identifiant THEN COUNT(c.identifiant)
        END AS nbeCommentaires
        FROM conduitedetravauxme ct
        LEFT JOIN commentaires c
             ON LTRIM(RTRIM(ct.id)) = LTRIM(RTRIM(c.identifiant)) and 'conduiteTravaux' = RTRIM(LTRIM(c.Tables))
        WHERE ct.etat = 'Termine'
        
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Nombre de commentaires lié à cette affaire conduite de travaux NOK
    public function getCommentsNok():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ct.id AS ident,
        CASE
        WHEN c.identifiant THEN COUNT(c.identifiant)
        END AS nbeCommentaires
        FROM conduitedetravauxme ct
        LEFT JOIN commentaires c
             ON LTRIM(RTRIM(ct.id)) = LTRIM(RTRIM(c.identifiant)) and 'conduiteTravaux' = RTRIM(LTRIM(c.Tables))
        WHERE ct.etat <> 'Termine'
        
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Conduite de travaux avec date Début et fin de chantier en cours
    public function getDateDebutFinChantierEnCours():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT ct.id AS id, ct.nom AS nom, ct.adresseLivraison AS adresse, ct.numCmd AS cmd, ct.affaire AS affaire, ct.dateDebutChantier AS start, ct.dateFinChantier AS end
        FROM conduitedetravauxme ct
        WHERE ct.etat <> 'Termine' AND YEAR(ct.dateDebutChantier) >= 2010 AND YEAR(ct.dateFinChantier) >= 2010
        
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // /**
    //  * @return ConduiteDeTravauxMe[] Returns an array of ConduiteDeTravauxMe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ConduiteDeTravauxMe
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
