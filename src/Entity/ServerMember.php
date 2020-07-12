<?php

namespace App\Entity;

use App\Form\Model\AddServerMember;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServerMemberRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"server_id", "user_id"})
 * })
 */
class ServerMember
{
    public const ACCESS_LEVEL_GUEST = 0;
    public const ACCESS_LEVEL_MEMBER = 10;
    public const ACCESS_LEVEL_OWNER = 100;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Server", inversedBy="members")
     * @ORM\JoinColumn(nullable=false)
     */
    private Server $server;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="serverMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="integer")
     */
    private int $accessLevel;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    private function __construct(Server $server, User $user, int $accessLevel)
    {
        $this->id = Uuid::uuid4();
        $this->server = $server;
        $this->user = $user;
        $this->accessLevel = $accessLevel;
        $this->createdAt = new DateTimeImmutable();
    }

    public static function createFromServerMemberAdditionForm(AddServerMember $addServerMember): self
    {
        return new self(
            $addServerMember->getServer(),
            $addServerMember->user,
            $addServerMember->accessLevel
        );
    }

    public static function createOwner(Server $server, User $user): self
    {
        return new self($server, $user, self::ACCESS_LEVEL_OWNER);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAccessLevel(): int
    {
        return $this->accessLevel;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
