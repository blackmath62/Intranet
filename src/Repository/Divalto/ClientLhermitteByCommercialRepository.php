<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientLhermitteByCommercialRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    public function getClientByContactName($nom, $dossier): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT CLI.TIERS AS Tiers, CLI.NOM AS RaisonSociale,T2.TIT AS Titre, T2.NOM AS Nom, T2.PRENOM AS Prenom, T2.TEL AS Telephone, T2.TELGSM AS Portable, T2.EMAIL AS Email
        FROM T2
        INNER JOIN CLI ON CLI.TIERS = T2.TIERS AND CLI.DOS = T2.DOS
        WHERE T2.NOM LIKE '%$nom%' AND T2.DOS IN($dossier)";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getClientLhermitteByCommercial(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT VRP.NOM AS Commercial, VRP.TIERS AS Id, CLI.STAT_0001 AS Famille, VRP.EMAIL AS Mail, CLI.WEB AS Web,
        CLI.TIERS AS Tiers, CLI.NOM AS Nom, CLI.RUE AS Rue, CLI.CPOSTAL AS CP, CLI.VIL AS Ville, CLI.TEL AS Tel, CLI.EMAIL AS Mail
        FROM CLI
        LEFT JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE CLI.DOS = 1 AND CLI.HSDT IS NULL";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getContactsClient($tiers): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT T2.LIB AS Fonction, T2.CONTACT AS Id, T2.NOM AS Nom, T2.PRENOM AS Prenom, T2.TEL AS Telephone, T2.TELGSM AS Portable, T2.EMAIL AS Email
        FROM T2 WHERE T2.DOS = 1 AND T2.TIERS = '$tiers' ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getThisClient($tiers)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT CLI.TIERS AS Tiers, CLI.NOM AS Nom, CLI.STAT_0001 AS Famille, VRP.NOM AS Commercial
        FROM CLI
        INNER JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE CLI.DOS = 1 AND CLI.TIERS = '$tiers' ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }

    public function getClient($tiers)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT VRP.EMAIL AS Email, CLI.NOM AS Nom
        FROM CLI
        LEFT JOIN VRP ON CLI.DOS = VRP.DOS AND CLI.REPR_0001 = VRP.TIERS
        WHERE CLI.DOS = 1 AND CLI.HSDT IS NULL AND CLI.TIERS = '$tiers'";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAssociative();
    }
}
