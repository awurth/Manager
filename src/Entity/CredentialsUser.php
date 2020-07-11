<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CredentialsUserRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"credentials_id", "user_id"})
 * })
 */
class CredentialsUser
{
    public const ACCESS_LEVEL_USER = 0;
    public const ACCESS_LEVEL_OWNER = 100;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Credentials", inversedBy="credentialsUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $credentials;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="credentialsUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $accessLevel;

    private function __construct(Credentials $credentials, User $user, int $accessLevel)
    {
        $this->id = Uuid::uuid4();
        $this->credentials = $credentials;
        $this->user = $user;
        $this->accessLevel = $accessLevel;
    }

    public static function createOwner(Credentials $credentials, User $user): self
    {
        return new self($credentials, $user, self::ACCESS_LEVEL_OWNER);
    }

    public static function createUser(Credentials $credentials, User $user): self
    {
        return new self($credentials, $user, self::ACCESS_LEVEL_USER);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCredentials(): ?Credentials
    {
        return $this->credentials;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getAccessLevel(): ?int
    {
        return $this->accessLevel;
    }
}
