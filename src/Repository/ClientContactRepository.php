<?php

namespace App\Repository;

use App\Entity\ClientContact;
use App\Entity\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientContact|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method ClientContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientContact[]    findAll()
 * @method ClientContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ClientContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientContact::class);
    }
}
