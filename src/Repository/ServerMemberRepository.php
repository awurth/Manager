<?php

namespace App\Repository;

use App\Entity\ServerMember;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ServerMemberNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ServerMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerMember::class);
    }

    public function get(Id $id): ServerMember
    {
        $member = $this->find($id);

        if (!$member) {
            throw ServerMemberNotFoundException::byId($id);
        }

        return $member;
    }
}
