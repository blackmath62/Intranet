<?php

namespace App\Repository\Main;

use DateTime;
use App\Entity\Main\Holiday;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Holiday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holiday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holiday[]    findAll()
 * @method Holiday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holiday::class);
    }

    public function getLastHoliday()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.id FROM holiday ORDER BY id DESC LIMIT 0,1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getOverlapHoliday($start, $end, $service)
    {

        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.id AS id, holiday.start AS start, holiday.end AS end, holiday.createdAt AS createdAt, holiday.details AS details,
        holiday.treatmentedAt AS treatmentedAt, holiday.treatmentedBy_id AS treatmentedBy, users.id AS users_id, users.pseudo AS pseudo, holidaytypes.name AS type, statusholiday.name AS statut, statusholiday.id AS statutId
        FROM holiday
        INNER JOIN holiday_users ON holiday_users.holiday_id = holiday.id
        INNER JOIN users ON holiday_users.users_id = users.id
        INNER JOIN holidaytypes ON holidaytypes.id = holiday.holidayType_id
        INNER JOIN statusholiday ON statusholiday.id = holiday.holidayStatus_id
        WHERE holiday.start >= ? AND holiday.end <= ? AND users.service_id = $service
        ORDER BY holiday.createdAt
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll();
    }

    public function getUserIdHoliday($id)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday_users.users_id 
        FROM `holiday`
        INNER JOIN holiday_users ON holiday_users.holiday_id = holiday.id
        WHERE holiday.id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getMailDecideurConges()
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT users.email FROM users WHERE users.roles LIKE '%ROLE_CONGES%'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getListeCongesEtServices()
    {
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.id, holiday.start, holiday.end, holiday.holidayStatus_id, users.service_id 
        FROM holiday
        INNER JOIN holiday_users ON holiday_users.holiday_id = holiday.id
        INNER JOIN users ON holiday_users.users_id = users.id
        WHERE holiday.end > NOW()
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
}
