<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="update")
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

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->users = new ArrayCollection();
        $this->projectEnvironments = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getOperatingSystem(): ?string
    {
        return $this->operatingSystem;
    }

    public function setOperatingSystem(?string $operatingSystem): self
    {
        $this->operatingSystem = $operatingSystem;

        return $this;
    }

    public function getSshPort(): ?int
    {
        return $this->sshPort;
    }

    public function setSshPort(?int $sshPort): self
    {
        $this->sshPort = $sshPort;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|ServerUser[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(ServerUser $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setServer($this);
        }

        return $this;
    }

    public function removeUser(ServerUser $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getServer() === $this) {
                $user->setServer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectEnvironment[]
     */
    public function getProjectEnvironments(): Collection
    {
        return $this->projectEnvironments;
    }

    public function addProjectEnvironment(ProjectEnvironment $projectEnvironment): self
    {
        if (!$this->projectEnvironments->contains($projectEnvironment)) {
            $this->projectEnvironments[] = $projectEnvironment;
            $projectEnvironment->setServer($this);
        }

        return $this;
    }

    public function removeProjectEnvironment(ProjectEnvironment $projectEnvironment): self
    {
        if ($this->projectEnvironments->contains($projectEnvironment)) {
            $this->projectEnvironments->removeElement($projectEnvironment);
            // set the owning side to null (unless already changed)
            if ($projectEnvironment->getServer() === $this) {
                $projectEnvironment->setServer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServerMember[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(ServerMember $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setServer($this);
        }

        return $this;
    }

    public function removeMember(ServerMember $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            // set the owning side to null (unless already changed)
            if ($member->getServer() === $this) {
                $member->setServer(null);
            }
        }

        return $this;
    }
}
