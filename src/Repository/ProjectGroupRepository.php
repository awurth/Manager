<?php

namespace App\Repository;

use App\Entity\ProjectGroup;
use App\Repository\Exception\ProjectGroupNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectGroup::class);
    }

    public function getBySlug(string $slug): ProjectGroup
    {
        $projectGroup = $this->findOneBy(['slug' => $slug]);

        if (!$projectGroup) {
            throw ProjectGroupNotFoundException::bySlug($slug);
        }

        return $projectGroup;
    }
}
