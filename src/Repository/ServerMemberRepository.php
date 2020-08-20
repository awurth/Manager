<?php

namespace App\Repository;

use App\Entity\ServerMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServerMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerMember[]    findAll()
 * @method ServerMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ServerMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerMember::class);
    }
}
