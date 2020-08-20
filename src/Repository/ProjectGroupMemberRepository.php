<?php

namespace App\Repository;

use App\Entity\ProjectGroupMember;
use App\Entity\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectGroupMember|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method ProjectGroupMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectGroupMember[]    findAll()
 * @method ProjectGroupMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProjectGroupMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectGroupMember::class);
    }
}
