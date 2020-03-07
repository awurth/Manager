<?php

namespace App\Repository;

use App\Entity\ServerUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ServerUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerUser[]    findAll()
 * @method ServerUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerUser::class);
    }
}
