<?php

namespace App\Repository;

use App\Entity\CredentialsUser;
use App\Entity\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CredentialsUser|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method CredentialsUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CredentialsUser[]    findAll()
 * @method CredentialsUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CredentialsUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CredentialsUser::class);
    }
}
