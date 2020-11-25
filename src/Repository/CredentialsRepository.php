<?php

namespace App\Repository;

use App\Entity\Credentials;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\CredentialsNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Credentials|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method Credentials|null findOneBy(array $criteria, array $orderBy = null)
 * @method Credentials[]    findAll()
 * @method Credentials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CredentialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Credentials::class);
    }

    public function get(Id $id): Credentials
    {
        $credentials = $this->find($id);

        if (!$credentials) {
            throw CredentialsNotFoundException::byId($id);
        }

        return $credentials;
    }
}
