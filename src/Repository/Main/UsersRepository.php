<?php

namespace App\Repository\Main;


use App\Entity\Main\Users;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    // trouver le mail d'un utilisateur en congés
    public function getFindEmail($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT users.email AS email FROM holiday_users
        INNER JOIN users ON holiday_users.users_id = users.id
        INNER JOIN holiday ON holiday.id = holiday_users.holiday_id
        WHERE holiday.id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

}
