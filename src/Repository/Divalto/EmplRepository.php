<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Mouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mouv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mouv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mouv[]    findAll()
 * @method Mouv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmplRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouv::class);

    }

    // Liste des piéces qui continnent un emplacement différent de celui dans info stock
    public function getBadPlaceProductOrder(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(dos)) as dos, RTRIM(LTRIM(ref)) AS ref, RTRIM(LTRIM(sref1)) AS sref1, RTRIM(LTRIM(sref2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(depot)) AS depot, RTRIM(LTRIM(empl)) AS empl,
        RTRIM(LTRIM(v.TIERS)) AS tiers, RTRIM(LTRIM(v.DEPO)) AS depotVtl, RTRIM(LTRIM(v.LIEU)) AS emplVtl, RTRIM(LTRIM(v.VTLNO)) AS vtlNumber, RTRIM(LTRIM(v.PINO)) AS numeroPiece,
        RTRIM(LTRIM(v.OP)) AS opVtl, RTRIM(LTRIM(v.PICOD)) AS typePiece , RTRIM(LTRIM(v.TICOD)) AS typeTiers
        FROM(
        SELECT e.DOS AS dos, e.REF AS ref, e.SREF1 AS sref1, e.SREF2 AS sref2, e.DEPO AS depot, e.LIEU AS empl
        FROM ARTDEPO e
        WHERE e.DOS NOT IN (777)) reponse
        LEFT JOIN MVTL v ON dos = v.DOS AND ref = v.REF AND sref1 = v.SREF1 AND sref2 = v.SREF2
        INNER JOIN ART a ON a.DOS = dos AND a.REF = ref
        WHERE v.LIEU <> empl AND v.TICOD NOT IN ('I') AND v.PICOD IN (2,3) -- AND v.OP NOT IN ('CD','DD','FD','GD')
        ORDER BY ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    // Liste des stocks qui continnent un emplacement différent de celui dans info stock
    public function getBadPlaceProductStock(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT RTRIM(LTRIM(dos)) as dos, RTRIM(LTRIM(ref)) AS ref, RTRIM(LTRIM(sref1)) AS sref1, RTRIM(LTRIM(sref2)) AS sref2, RTRIM(LTRIM(a.DES)) AS designation, RTRIM(LTRIM(depot)) AS depot, RTRIM(LTRIM(empl)) AS empl,
		RTRIM(LTRIM(s.DEPOT)) AS depotStock, SUM(s.QTETJSENSTOCK) AS stock, RTRIM(LTRIM(s.EMPLACEMENT)) AS emplStock, RTRIM(LTRIM(s.NATURESTOCK)) AS nature
        FROM(
        SELECT e.DOS AS dos, e.REF AS ref, e.SREF1 AS sref1, e.SREF2 AS sref2, e.DEPO AS depot, e.LIEU AS empl
        FROM ARTDEPO e
        WHERE e.DOS NOT IN (777)) reponse
        INNER JOIN MVTL_STOCK_V s ON dos = s.DOSSIER AND ref = s.REFERENCE AND sref1 = s.SREFERENCE1 AND sref2 = s.SREFERENCE2
		INNER JOIN ART a ON a.DOS = dos AND a.REF = ref
		WHERE empl <> s.EMPLACEMENT AND s.DEPOT IN ('1','2')
		GROUP BY dos, ref, sref1, sref2, a.DES, depot, empl, s.DEPOT, s.EMPLACEMENT, s.NATURESTOCK
		ORDER BY ref
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
}
