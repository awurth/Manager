<?php

namespace App\Entity;

use App\Form\Model\CreateServer;
use App\Form\Model\EditServer;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServerRepository")
 */
class Server
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $operatingSystem;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sshPort;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ServerUser", mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectEnvironment", mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $projectEnvironments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ServerMember", mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $members;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        $this->users = new ArrayCollection();
        $this->projectEnvironments = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public static function createFromCreationForm(CreateServer $createServer, User $owner): self
    {
        $server = new self($createServer->name);
        $server->ip = $createServer->ip;
        $server->operatingSystem = $createServer->operatingSystem;
        $server->sshPort = $createServer->sshPort;

        $server->members[] = ServerMember::createOwner($server, $owner);

        return $server;
    }

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

    public function getId(): ?int
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
     * @return Collection|ServerUser[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection|ProjectEnvironment[]
     */
    public function getProjectEnvironments(): Collection
    {
        return $this->projectEnvironments;
    }

    /**
     * @return Collection|ServerMember[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }
}
