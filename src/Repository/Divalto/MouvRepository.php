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

    // Liste des piéces avec des produits FSC
    public function getFscOrderList($listpieceOk):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT RTRIM(LTRIM(MOUV.TIERS)) AS tiers, RTRIM(LTRIM(ENT.PIREF)) AS notreRef, RTRIM(LTRIM(MOUV.PICOD)) AS codePiece, RTRIM(LTRIM(MOUV.CDNO)) AS numCmd,MOUV.CDDT AS dateCmd,RTRIM(LTRIM(MOUV.BLNO)) AS numBl, MOUV.BLDT AS dateBl,RTRIM(LTRIM(MOUV.FANO)) AS numFact, MOUV.FADT AS dateFact,
        CASE
        WHEN MOUV.TIERS IS NULL THEN 'MARINA'
        ELSE 'MARINA'
        END AS utilisateur
        FROM MOUV
        INNER JOIN ENT ON ENT.DOS = MOUV.DOS AND ENT.TIERS = MOUV.TIERS AND ENT.PICOD = MOUV.PICOD
        WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.TICOD IN ('C','F') AND MOUV.CDNO NOT IN($listpieceOk) AND MOUV.PICOD IN (2,3,4) AND (MOUV.CDDT >= '2022/01/01' OR MOUV.BLDT >= '2022/01/01' OR MOUV.FADT >= '2022/01/01')
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Liste des piéces avec des produits FSC pour tourner à vide
    public function getFscOrderListRun():array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT RTRIM(LTRIM(MOUV.TIERS)) AS tiers, RTRIM(LTRIM(ENT.PIREF)) AS notreRef, RTRIM(LTRIM(MOUV.PICOD)) AS codePiece, RTRIM(LTRIM(MOUV.CDNO)) AS numCmd,MOUV.CDDT AS dateCmd,RTRIM(LTRIM(MOUV.BLNO)) AS numBl, MOUV.BLDT AS dateBl,RTRIM(LTRIM(MOUV.FANO)) AS numFact, MOUV.FADT AS dateFact,
        CASE
        WHEN MOUV.TIERS IS NULL THEN 'MARINA'
        ELSE 'MARINA'
        END AS utilisateur
        FROM MOUV
        INNER JOIN ENT ON ENT.DOS = MOUV.DOS AND ENT.TIERS = MOUV.TIERS AND ENT.PICOD = MOUV.PICOD
        WHERE MOUV.DOS = 3 AND MOUV.REF LIKE 'FSC%' AND MOUV.TICOD IN ('C','F') AND MOUV.PICOD IN (2,3,4) AND (MOUV.CDDT >= '2022/01/01' OR MOUV.BLDT >= '2022/01/01' OR MOUV.FADT >= '2022/01/01')
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Mouvements sur la piéce
    public function getMouvOnOrder($num, $typePiece, $tiers):array
    {
        if ($typePiece == 2 ) {
            $code = 'MOUV.CDNO';
            $dateP = 'MOUV.CDDT';        
        }elseif ($typePiece == 3 ) {
            $code = 'MOUV.BLNO';
            $dateP = 'MOUV.BLDT';    
        }elseif ($typePiece == 4 ) {
            $code = 'MOUV.FANO';
            $dateP = 'MOUV.FADT';    
        }
        $where = $code . ' = ' . $num;
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT $dateP AS datePiece, $code AS num, MOUV.PICOD AS codePiece, MOUV.TIERS AS tiers, MOUV.REF AS ref, MOUV.SREF1 AS sref1, MOUV.SREF2 AS sref2, MAX(MOUV.DES) AS designation
        FROM MOUV
        WHERE MOUV.DOS = 3 AND MOUV.PICOD = $typePiece AND MOUV.TIERS = '$tiers' AND $where
        GROUP BY $dateP, $code, MOUV.PICOD, MOUV.TIERS, MOUV.REF, MOUV.SREF1, MOUV.SREF2
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Mouvements sur la piéce
    public function getMouvByOrder($num, $tiers, $codePiece)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT RTRIM(LTRIM(MOUV.CDNO)) AS numCmd, RTRIM(LTRIM(MOUV.PICOD)) AS codePiece, MOUV.TIERS AS tiers,RTRIM(LTRIM(MOUV.BLNO)) AS numBl, MOUV.BLDT AS dateBl,RTRIM(LTRIM(MOUV.FANO)) AS numFact, MOUV.FADT AS dateFact
        FROM MOUV
        WHERE MOUV.DOS = 3 AND MOUV.PICOD = $codePiece AND MOUV.TIERS = '$tiers' AND MOUV.REF LIKE 'FSC%' AND MOUV.CDNO = $num
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}
