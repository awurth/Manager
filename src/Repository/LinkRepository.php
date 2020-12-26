<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\LinkNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function get(Id $id): Link
    {
        $link = $this->find($id);

        if (!$link) {
            throw LinkNotFoundException::byId($id);
        }

        return $link;
    }
}
