<?php

namespace App\Repository;

use App\Entity\Project;
use App\Repository\Exception\ProjectNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function getBySlug(string $slug): Project
    {
        $project = $this->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw ProjectNotFoundException::bySlug($slug);
        }

        return $project;
    }
}
