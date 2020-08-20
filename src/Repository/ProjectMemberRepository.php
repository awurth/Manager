<?php

namespace App\Repository;

use App\Entity\ProjectMember;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ProjectMemberNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectMember|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method ProjectMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectMember[]    findAll()
 * @method ProjectMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProjectMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMember::class);
    }

    public function get(Id $id): ProjectMember
    {
        $member = $this->find($id);

        if (!$member) {
            throw ProjectMemberNotFoundException::byId($id);
        }

        return $member;
    }
}
