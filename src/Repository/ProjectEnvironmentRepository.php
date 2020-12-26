<?php

namespace App\Repository;

use App\Entity\ProjectEnvironment;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ProjectEnvironmentNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectEnvironmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectEnvironment::class);
    }

    public function get(Id $id): ProjectEnvironment
    {
        $environment = $this->find($id);

        if (!$environment) {
            throw ProjectEnvironmentNotFoundException::byId($id);
        }

        return $environment;
    }
}
