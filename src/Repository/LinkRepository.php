<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\LinkNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Link|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
