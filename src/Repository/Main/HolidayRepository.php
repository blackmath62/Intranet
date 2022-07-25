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
        $sql = "SELECT MAX(holiday.id) AS holiday_id
        FROM holiday
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    // Pour contrôler si l'utilisateur n'a pas déjà posé durant cet interval
    public function getAlreadyInHolidayInThisPeriod($start, $end, $user, $id = 0){
        $start = date_format($start,"Y-m-d H:i:s");
        $end = date_format($end,"Y-m-d H:i:s");
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM holiday 
        WHERE 
        (holiday.start BETWEEN '$start' AND '$end' -- la date début est comprise entre les dates saisies
        OR holiday.end BETWEEN '$start' AND '$end'  -- la date fin est comprise entre les dates saisies
        OR '$start' BETWEEN holiday.start AND holiday.end  -- la date début saisie est comprise entre les dates début et fin
        OR '$end' BETWEEN holiday.start AND holiday.end) -- la date fin saisie est comprise entre les dates début et fin
        AND holiday.user_id = $user AND not holiday.id IN( $id )
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserActuallyHoliday($user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT holiday.user_id 
        FROM holiday 
        WHERE holiday.start <= NOW() AND holiday.end >= NOW() AND holiday.user_id = $user AND holiday.holidayStatus_id = 3 ORDER BY holiday.user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
        
    }

    public function getListHoliday()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * 
        FROM holiday_users
        INNER JOIN holiday ON holiday.id = holiday_users.holiday_id
        INNER JOIN users ON holiday_users.users_id = users.id
        INNER JOIN services ON services.id = users.service_id
        ORDER BY holiday.id DESC        
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
        
    }
    public function getMaxHolidayId()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MAX(holiday.id) FROM holiday        
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
        
    }

    // Chevauchement de congés d'un même service
    //TODO revoir cette requête qui va changer au vu du remaniment des relations
    public function getOverlapHoliday($start, $end, $service)
    {

        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.id AS id, holiday.start AS start, holiday.end AS end, holiday.createdAt AS createdAt, holiday.details AS details,
        holiday.treatmentedAt AS treatmentedAt, holiday.treatmentedBy_id AS treatmentedBy, holiday.user_id AS users_id, holidaytypes.name AS type, statusholiday.name AS statut, statusholiday.id AS statutId
        FROM holiday
        INNER JOIN holidaytypes ON holidaytypes.id = holiday.holidayType_id
        INNER JOIN statusholiday ON statusholiday.id = holiday.holidayStatus_id
        INNER JOIN users ON users.id = holiday.user_id
        INNER JOIN services ON services.id = users.service_id
        WHERE YEAR(holiday.start) >= '$start' AND MONTH(holiday.start) >= '$start' AND DAY(holiday.start) >= '$start' AND YEAR(holiday.end) <= '$end' AND MONTH(holiday.end) <= '$end' AND MONTH(holiday.end) <= '$end' AND services.id = $service
        ORDER BY holiday.createdAt
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll();
    }

    public function getUserIdHoliday($id)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.user_id 
        FROM holiday
        WHERE holiday.id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getMailDecideurConges()
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DISTINCT users.email FROM users WHERE users.roles LIKE '%ROLE_CONGES%'
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

    // Liste des types de congés avec des congés acceptés par utilisateurs
    public function getVacationTypeListByUsers($start, $end)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT pseudo, email, 
        SUM(conges) AS conges, SUM(rtt) AS rtt, SUM(sansSolde) AS sansSolde, SUM(famille) AS famille, 
        SUM(maternite) AS maternite, SUM(deces) AS deces, SUM(demenagement) AS demenagement, SUM(autre) AS autre, SUM(total) AS total
        FROM(
        SELECT users.pseudo AS pseudo, users.email AS email,
        CASE
        WHEN holiday.holidayType_id = 9 THEN holiday.nbJours
        END AS conges,
        CASE
        WHEN holiday.holidayType_id = 15 THEN holiday.nbJours
        END AS rtt,
        CASE
        WHEN holiday.holidayType_id = 10 THEN holiday.nbJours
        END AS sansSolde,
        CASE
        WHEN holiday.holidayType_id = 11 THEN holiday.nbJours
        END AS famille,
        CASE
        WHEN holiday.holidayType_id = 12 THEN holiday.nbJours
        END AS maternite,
        CASE
        WHEN holiday.holidayType_id = 13 THEN holiday.nbJours
        END AS deces,
        CASE
        WHEN holiday.holidayType_id = 14 THEN holiday.nbJours
        END AS demenagement,
        CASE
        WHEN holiday.holidayType_id = 16 THEN holiday.nbJours
        END AS autre,
        CASE
        WHEN holiday.holidayType_id > 0 THEN holiday.nbJours
        END AS total
        FROM holiday
        INNER JOIN users ON users.id = holiday.user_id
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.holidayStatus_id = 3)reponse
        GROUP BY pseudo, email
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Nombre de jour de congés acceptés pour tous les utilisateurs 
    public function getCountCongesAcceptedForAllUsers($start, $end)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT users.pseudo, users.email, holidaytypes.name, holidaytypes.color , holiday.holidayType_id, holiday.holidayStatus_id, SUM(holiday.nbJours) AS nbJours
        FROM holiday
        INNER JOIN  holidaytypes ON holiday.holidayType_id = holidaytypes.id
        INNER JOIN statusholiday ON holiday.holidayStatus_id = statusholiday.id
        INNER JOIN users ON users.id = holiday.user_id
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.holidayStatus_id = 3
        GROUP BY users.pseudo, users.email, holidaytypes.name, holidaytypes.color, holiday.holidayType_id, holiday.holidayStatus_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Nombre de jour de congés acceptés par utilisateur 
    public function getCountCongesAccepted($user, $start, $end)
    {

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holidaytypes.name, holidaytypes.color , holiday.holidayType_id, statusholiday.name AS statusName, holiday.holidayStatus_id, SUM(holiday.nbJours) AS nbJours
        FROM holiday
        INNER JOIN  holidaytypes ON holiday.holidayType_id = holidaytypes.id
        INNER JOIN statusholiday ON holiday.holidayStatus_id = statusholiday.id
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.user_id = $user AND holiday.holidayStatus_id = 3
        GROUP BY holidaytypes.name, holidaytypes.color,statusholiday.name, holiday.holidayType_id, holiday.holidayStatus_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Nombre de jour de congés Refusés par utilisateur 
    public function getCountCongesRefused($user, $start, $end)
    {
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holidaytypes.name, holidaytypes.color , holiday.holidayType_id, statusholiday.name AS statusName, holiday.holidayStatus_id, SUM(holiday.nbJours) AS nbJours
        FROM holiday
        INNER JOIN  holidaytypes ON holiday.holidayType_id = holidaytypes.id
        INNER JOIN statusholiday ON holiday.holidayStatus_id = statusholiday.id
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.user_id = $user AND holiday.holidayStatus_id = 4
        GROUP BY holidaytypes.name, holidaytypes.color,statusholiday.name, holiday.holidayType_id, holiday.holidayStatus_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Nombre de jour de congés en attente par utilisateur 
    public function getCountCongesWait($user, $start, $end)
    {
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holidaytypes.name, holidaytypes.color , holiday.holidayType_id, statusholiday.name AS statusName, holiday.holidayStatus_id, SUM(holiday.nbJours) AS nbJours
        FROM holiday
        INNER JOIN  holidaytypes ON holiday.holidayType_id = holidaytypes.id
        INNER JOIN statusholiday ON holiday.holidayStatus_id = statusholiday.id
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.user_id = $user AND holiday.holidayStatus_id IN (1,2)
        GROUP BY holidaytypes.name, holidaytypes.color,statusholiday.name, holiday.holidayType_id, holiday.holidayStatus_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Liste des jours de congés en attente par utilisateur 
    public function getListCongesWait($user, $start, $end)
    {
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.start, holiday.sliceStart, holiday.end, holiday.sliceEnd, holiday.nbJours, holiday.treatmentedAt, holiday.treatmentedBy_id, holiday.holidayType_id, holiday.holidayStatus_id
        FROM holiday
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.user_id = $user AND holiday.holidayStatus_id IN (1,2)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Liste des jours de congés en accepté par utilisateur 
    public function getListCongesAccepted($user, $start, $end)
    {
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.start, holiday.sliceStart, holiday.end, holiday.sliceEnd, holiday.nbJours, holiday.treatmentedAt, holiday.treatmentedBy_id, holiday.holidayType_id, holiday.holidayStatus_id
        FROM holiday
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.user_id = $user AND holiday.holidayStatus_id IN (3)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Liste des jours de congés en refusé par utilisateur 
    public function getListCongesRefused($user, $start, $end)
    {
        // congés non dépassés avec les services
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT holiday.start, holiday.sliceStart, holiday.end, holiday.sliceEnd, holiday.nbJours, holiday.treatmentedAt, holiday.treatmentedBy_id, holiday.holidayType_id, holiday.holidayStatus_id
        FROM holiday
        WHERE holiday.start BETWEEN '$start' AND '$end' AND holiday.end BETWEEN '$start' AND '$end' AND holiday.user_id = $user AND holiday.holidayStatus_id IN (4)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
}
