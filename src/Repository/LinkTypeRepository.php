<?php

namespace App\Repository;

use App\Entity\LinkType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LinkType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkType[]    findAll()
 * @method LinkType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LinkTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkType::class);
    }
}
