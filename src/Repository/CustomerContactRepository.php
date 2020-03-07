<?php

namespace App\Repository;

use App\Entity\CustomerContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CustomerContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerContact[]    findAll()
 * @method CustomerContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerContact::class);
    }
}
