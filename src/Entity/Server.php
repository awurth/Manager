<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $operatingSystem;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ServerUser", mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectEnvironment", mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $projectEnvironments;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projectEnvironments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
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
}
