<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StatesRobyByTiersRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);
    }

    public function getStatesRobyTiersByMonth($annee, $mois): array
    {
        $N1 = $annee - 1;
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = "SELECT YEAR(DateFacture) AS Annee,LTRIM(RTRIM(Commercial)) AS Commercial,LTRIM(RTRIM(Client)) AS Tiers, LTRIM(RTRIM(nom)) as Nom,LTRIM(RTRIM(Pays)) as Pays,
        LTRIM(RTRIM(Devise)) AS Devise,LTRIM(RTRIM(SUM(montantSign))) AS MontantSign
        FROM -- imbrication d'une requête pour extraire les données à calculer
        (SELECT VRP.NOM AS Commercial, MOUV.TIERS as Client, CLI.NOM AS nom, CLI.PAY AS Pays, MOUV.DEV AS Devise, MOUV.OP,MOUV.MONT AS Montant,
        MOUV.REMPIEMT_0004 AS Remise,MOUV.FADT AS DateFacture,
        CASE -- Signature du montant
                WHEN MOUV.OP IN('C','CD') THEN (MOUV.MONT)+(-1 * MOUV.REMPIEMT_0004)
                WHEN MOUV.OP IN('DD','D') THEN (-1 * MOUV.MONT)+(MOUV.REMPIEMT_0004) -- Si Sens = 1 alors c'est négatif
                ELSE 0
        END AS montantSign
        FROM MOUV
        INNER JOIN CLI ON MOUV.TIERS = CLI.TIERS AND CLI.DOS = MOUV.DOS
        LEFT JOIN VRP ON CLI.REPR_0001 = VRP.TIERS AND MOUV.DOS = VRP.DOS
        WHERE MOUV.DOS = 3 AND MOUV.PICOD = 4 AND MOUV.TICOD = 'C' AND YEAR(MOUV.FADT) IN(? , ?) AND MONTH(MOUV.FADT) IN(?)
        AND MOUV.OP IN('C','CD','DD','D')
        GROUP BY VRP.NOM, MOUV.TIERS, CLI.NOM,CLI.PAY,MOUV.DEV, MOUV.OP ,MOUV.MONT, MOUV.REMPIEMT_0004, MOUV.FADT, MOUV.FANO) Reponse
        GROUP BY YEAR(DateFacture), Commercial, Client, nom,Pays, Devise
        ORDER BY Tiers";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
}
