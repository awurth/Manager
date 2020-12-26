<?php

namespace App\Repository;

use App\Entity\Credentials;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\CredentialsNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
