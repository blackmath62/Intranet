<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Vrp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vrp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vrp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vrp[]    findAll()
 * @method Vrp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VrpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vrp::class);
    }

    // liste des utilisateurs de Divalto pour la mise à jour
public function UpdateListDivaltoUser():array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT LTRIM(RTRIM(MUSER.MUSER_ID)) AS divalto_id, LTRIM(RTRIM(MUSER.USERX)) AS userX,
    LTRIM(RTRIM(MUSER.NOM)) AS nom, LTRIM(RTRIM(MUSER.DOS)) AS dos, LTRIM(RTRIM(MUSER.EMAIL)) AS email 
    FROM MUSER
    WHERE MUSER.HSDT IS NULL
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

}
