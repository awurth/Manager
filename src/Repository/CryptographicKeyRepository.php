<?php

namespace App\Repository;

use App\Entity\CryptographicKey;
use App\Entity\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CryptographicKey|null find(Id $id, $lockMode = null, $lockVersion = null)
 * @method CryptographicKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryptographicKey[]    findAll()
 * @method CryptographicKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CryptographicKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptographicKey::class);
    }
}
