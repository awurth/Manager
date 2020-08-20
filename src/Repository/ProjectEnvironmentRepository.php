<?php

namespace App\Repository;

use App\Entity\ProjectEnvironment;
use App\Entity\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProjectEnvironment|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method ProjectEnvironment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectEnvironment[]    findAll()
 * @method ProjectEnvironment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProjectEnvironmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectEnvironment::class);
    }
}
