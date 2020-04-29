<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CredentialsRepository")
 */
class Credentials
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
     * @ORM\OneToMany(targetEntity="App\Entity\CredentialsUser", mappedBy="credentials", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $credentialsUsers;

    public function __construct(string $name, string $password)
    {
        $this->name = $name;
        $this->password = $password;

        $this->credentialsUsers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users): void
    {
        foreach ($this->getCredentialsUsers() as $credentialsUser) {
            if (!in_array($credentialsUser->getUser(), $users, true)) {
                $this->removeCredentialsUser($credentialsUser);
            }
        }

        foreach ($users as $user) {
            $alreadyAdded = false;
            foreach ($this->getCredentialsUsers() as $credentialsUser) {
                if ($credentialsUser->getUser() === $user) {
                    $alreadyAdded = true;
                }
            }

            if (!$alreadyAdded) {
                $this->addCredentialsUser(
                    (new CredentialsUser())
                        ->setUser($user)
                        ->setAccessLevel(CredentialsUser::ACCESS_LEVEL_USER)
                );
            }
        }
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
     * @return Collection|CredentialsUser[]
     */
    public function getCredentialsUsers(): Collection
    {
        return $this->credentialsUsers;
    }

    public function addCredentialsUser(CredentialsUser $credentialsUser): self
    {
        if (!$this->credentialsUsers->contains($credentialsUser)) {
            $this->credentialsUsers[] = $credentialsUser;
            $credentialsUser->setCredentials($this);
        }

        return $this;
    }

    public function removeCredentialsUser(CredentialsUser $credentialsUser): self
    {
        if ($this->credentialsUsers->contains($credentialsUser)) {
            $this->credentialsUsers->removeElement($credentialsUser);
            // set the owning side to null (unless already changed)
            if ($credentialsUser->getCredentials() === $this) {
                $credentialsUser->setCredentials(null);
            }
        }

        return $this;
    }
}
