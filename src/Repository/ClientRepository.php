<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ClientNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function get(Id $id): Client
    {
        $client = $this->find($id);

        if (!$client) {
            throw ClientNotFoundException::byId($id);
        }

        return $client;
    }
}
