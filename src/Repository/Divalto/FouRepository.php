<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Fou;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fou|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fou|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fou[]    findAll()
 * @method Fou[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FouRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fou::class);
    }

    public function getAddCopyFou($fournisseurs):array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT LTRIM(RTRIM(FOU.FOU_ID)) AS FOU_ID , LTRIM(RTRIM(FOU.TIERS)) AS TIERS,LTRIM(RTRIM(FOU.DOS)) AS DOS, LTRIM(RTRIM(FOU.NOM)) AS NOM,FOU.HSDT
        FROM FOU
        WHERE FOU.TIERS NOT IN($fournisseurs) AND FOU.DOS = 1
        ORDER BY FOU.FOU_ID ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUpdateCopyFou($fournisseurs):array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT LTRIM(RTRIM(FOU.FOU_ID)) AS FOU_ID , LTRIM(RTRIM(FOU.TIERS)) AS TIERS,LTRIM(RTRIM(FOU.DOS)) AS DOS, LTRIM(RTRIM(FOU.NOM)) AS NOM,FOU.HSDT
        FROM FOU
        WHERE FOU.TIERS IN($fournisseurs) AND FOU.DOS = 1
        ORDER BY FOU.FOU_ID ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
