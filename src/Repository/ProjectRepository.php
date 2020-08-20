<?php

namespace App\Repository;

use App\Entity\Project;
use App\Repository\Exception\ProjectNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
