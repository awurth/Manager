<?php

namespace App\Repository;

use App\Entity\ProjectGroup;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ProjectGroupNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectGroup|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method ProjectGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectGroup[]    findAll()
 * @method ProjectGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
