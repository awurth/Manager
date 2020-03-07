<?php

namespace App\Repository;

use App\Entity\CryptographicKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CryptographicKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryptographicKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryptographicKey[]    findAll()
 * @method CryptographicKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryptographicKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptographicKey::class);
    }
}
