<?php

namespace App\Repository;

use App\Entity\LinkType;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\LinkTypeNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LinkTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkType::class);
    }

    public function get(Id $id): LinkType
    {
        $linkType = $this->find($id);

        if (!$linkType) {
            throw LinkTypeNotFoundException::byId($id);
        }

        return $linkType;
    }
}
