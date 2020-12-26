<?php

namespace App\Repository;

use App\Entity\Server;
use App\Entity\ValueObject\Id;
use App\Repository\Exception\ServerNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Server::class);
    }

    public function get(Id $id): Server
    {
        $server = $this->find($id);

        if (!$server) {
            throw ServerNotFoundException::byId($id);
        }

        return $server;
    }
}
