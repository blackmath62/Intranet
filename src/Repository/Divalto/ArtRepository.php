<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Art;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Art|null find($id, $lockMode = null, $lockVersion = null)
 * @method Art|null findOneBy(array $criteria, array $orderBy = null)
 * @method Art[]    findAll()
 * @method Art[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Art::class);
    }

    public function getAddCopyArt($articles):array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT LTRIM(RTRIM(ART.ART_ID)) AS ART_ID , LTRIM(RTRIM(ART.REF)) AS REF,LTRIM(RTRIM(ART.DOS)) AS DOS, LTRIM(RTRIM(ART.DES)) AS DES, LTRIM(RTRIM(ART.VENUN)) AS VENUN, LTRIM(RTRIM(ART.FAM_0002)) AS FAM_0002, ART.HSDT
        FROM ART
        WHERE ART.REF NOT IN($articles) AND ART.DOS = 1 AND ART.HSDT IS NULL AND ART.FAM_0002 IN ('EV')
        ORDER BY ART.ART_ID ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUpdateCopyArt($articles):array
    {
        $conn = $this->getEntityManager()
        ->getConnection();
        $sql = "SELECT LTRIM(RTRIM(ART.ART_ID)) AS ART_ID , LTRIM(RTRIM(ART.REF)) AS REF,LTRIM(RTRIM(ART.DOS)) AS DOS, LTRIM(RTRIM(ART.DES)) AS DES, LTRIM(RTRIM(ART.VENUN)) AS VENUN, LTRIM(RTRIM(ART.FAM_0002)) AS FAM_0002,ART.HSDT
        FROM ART
        WHERE ART.REF IN($articles) AND ART.DOS = 1 AND ART.HSDT IS NULL AND ART.FAM_0002 IN ('EV')
        ORDER BY ART.ART_ID ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
