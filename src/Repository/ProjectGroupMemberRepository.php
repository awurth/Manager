<?php

namespace App\Repository;

use App\Entity\ProjectGroupMember;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ProjectGroupMemberNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectGroupMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectGroupMember::class);
    }

    public function get(Id $id): ProjectGroupMember
    {
        $projectGroupMember = $this->find($id);

        if (!$projectGroupMember) {
            throw ProjectGroupMemberNotFoundException::byId($id);
        }

        return $projectGroupMember;
    }
}
