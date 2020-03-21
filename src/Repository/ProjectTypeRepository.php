<?php

namespace App\Repository;

use App\Entity\ProjectType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProjectType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectType[]    findAll()
 * @method ProjectType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectType::class);
    }
}
