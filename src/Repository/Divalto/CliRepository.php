<?php

namespace App\Repository\Divalto;

use App\Entity\Divalto\Cli;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cli|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cli|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cli[]    findAll()
 * @method Cli[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CliRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cli::class);
    }

}
