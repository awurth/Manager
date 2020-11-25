<?php

namespace App\Repository;

use App\Entity\ServerUser;
use App\Entity\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServerUser|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method ServerUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerUser[]    findAll()
 * @method ServerUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ServerUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerUser::class);
    }
}
