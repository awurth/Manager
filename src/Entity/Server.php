<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\CreateServer;
use App\Form\Model\EditServer;
use App\Repository\ServerRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServerRepository::class)
 */
class Server
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ip = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $operatingSystem = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $sshPort = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=ServerMember::class, mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $members;

    private function __construct(string $name)
    {
        $this->id = Id::generate();
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        $this->members = new ArrayCollection();
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public static function createFromCreationForm(CreateServer $createServer, User $owner): self
    {
        $server = new self($createServer->name);
        $server->ip = $createServer->ip;
        $server->operatingSystem = $createServer->operatingSystem;
        $server->sshPort = $createServer->sshPort;

        $server->members[] = ServerMember::createOwner($server, $owner);

        return $server;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromEditionForm(EditServer $editServer): void
    {
        $this->name = $editServer->name;
        $this->ip = $editServer->ip;
        $this->operatingSystem = $editServer->operatingSystem;
        $this->sshPort = $editServer->sshPort;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getMemberByUser(User $user): ?ServerMember
    {
        foreach ($this->getMembers() as $member) {
            if ($member->getUser() === $user) {
                return $member;
            }
        }

        return null;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getOperatingSystem(): ?string
    {
        return $this->operatingSystem;
    }

    public function getSshPort(): ?int
    {
        return $this->sshPort;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @psalm-suppress MismatchingDocblockReturnType
     * @return ArrayCollection|ServerMember[]
     */
    public function getMembers(): ArrayCollection
    {
        return new ArrayCollection($this->members->toArray());
    }
}
