<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    public const GENDER_NEUTRAL = 0;
    public const GENDER_FEMALE = 1;
    public const GENDER_MALE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $gender = self::GENDER_NEUTRAL;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

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
     * @ORM\OneToMany(targetEntity="App\Entity\CryptographicKey", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $cryptographicKeys;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectGroupMember", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $projectGroupMembers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectMember", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $projectMembers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ServerMember", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true))
     */
    private $serverMembers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CredentialsUser", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $credentialsUsers;

    public function __construct(string $email, string $firstname, string $lastname)
    {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        $this->cryptographicKeys = new ArrayCollection();
        $this->projectGroupMembers = new ArrayCollection();
        $this->projectMembers = new ArrayCollection();
        $this->serverMembers = new ArrayCollection();
        $this->credentialsUsers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getEmail();
    }

    public function getFullName(): string
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    /**
     * @return CryptographicKey[]
     */
    public function getSshKeys(): array
    {
        $sshKeys = [];
        foreach ($this->getCryptographicKeys() as $cryptographicKey) {
            if ($cryptographicKey->getType() === CryptographicKey::TYPE_SSH) {
                $sshKeys[] = $cryptographicKey;
            }
        }

        return $sshKeys;
    }

    /**
     * @return ProjectGroup[]
     */
    public function getProjectGroups(): array
    {
        $projectGroups = [];
        foreach ($this->getProjectGroupMembers() as $projectGroupMember) {
            $projectGroups[] = $projectGroupMember->getProjectGroup();
        }

        return $projectGroups;
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        $projects = [];
        foreach ($this->getProjectMembers() as $projectMember) {
            $projects[] = $projectMember->getProject();
        }

        return $projects;
    }

    /**
     * @return Credentials[]
     */
    public function getCredentialsList(): array
    {
        $credentialsList = [];
        foreach ($this->getCredentialsUsers() as $credentialsUser) {
            $credentialsList[] = $credentialsUser->getCredentials();
        }

        return $credentialsList;
    }

    public function eraseCredentials(): void
    {
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole($role): self
    {
        $role = strtoupper($role);
        if ($role === 'ROLE_USER') {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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
     * @return Collection|CryptographicKey[]
     */
    public function getCryptographicKeys(): Collection
    {
        return $this->cryptographicKeys;
    }

    public function addCryptographicKey(CryptographicKey $cryptographicKey): self
    {
        if (!$this->cryptographicKeys->contains($cryptographicKey)) {
            $this->cryptographicKeys[] = $cryptographicKey;
            $cryptographicKey->setUser($this);
        }

        return $this;
    }

    public function removeCryptographicKey(CryptographicKey $cryptographicKey): self
    {
        if ($this->cryptographicKeys->contains($cryptographicKey)) {
            $this->cryptographicKeys->removeElement($cryptographicKey);
            // set the owning side to null (unless already changed)
            if ($cryptographicKey->getUser() === $this) {
                $cryptographicKey->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectGroupMember[]
     */
    public function getProjectGroupMembers(): Collection
    {
        return $this->projectGroupMembers;
    }

    public function addProjectGroupMember(ProjectGroupMember $projectGroupMember): self
    {
        if (!$this->projectGroupMembers->contains($projectGroupMember)) {
            $this->projectGroupMembers[] = $projectGroupMember;
            $projectGroupMember->setUser($this);
        }

        return $this;
    }

    public function removeProjectGroupMember(ProjectGroupMember $projectGroupMember): self
    {
        if ($this->projectGroupMembers->contains($projectGroupMember)) {
            $this->projectGroupMembers->removeElement($projectGroupMember);
            // set the owning side to null (unless already changed)
            if ($projectGroupMember->getUser() === $this) {
                $projectGroupMember->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectMember[]
     */
    public function getProjectMembers(): Collection
    {
        return $this->projectMembers;
    }

    public function addProjectMember(ProjectMember $projectMember): self
    {
        if (!$this->projectMembers->contains($projectMember)) {
            $this->projectMembers[] = $projectMember;
            $projectMember->setUser($this);
        }

        return $this;
    }

    public function removeProjectMember(ProjectMember $projectMember): self
    {
        if ($this->projectMembers->contains($projectMember)) {
            $this->projectMembers->removeElement($projectMember);
            // set the owning side to null (unless already changed)
            if ($projectMember->getUser() === $this) {
                $projectMember->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServerMember[]
     */
    public function getServerMembers(): Collection
    {
        return $this->serverMembers;
    }

    public function addServerMember(ServerMember $serverMember): self
    {
        if (!$this->serverMembers->contains($serverMember)) {
            $this->serverMembers[] = $serverMember;
            $serverMember->setUser($this);
        }

        return $this;
    }

    public function removeServerMember(ServerMember $serverMember): self
    {
        if ($this->serverMembers->contains($serverMember)) {
            $this->serverMembers->removeElement($serverMember);
            // set the owning side to null (unless already changed)
            if ($serverMember->getUser() === $this) {
                $serverMember->setUser(null);
            }
        }

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
            $credentialsUser->setUser($this);
        }

        return $this;
    }

    public function removeCredentialsUser(CredentialsUser $credentialsUser): self
    {
        if ($this->credentialsUsers->contains($credentialsUser)) {
            $this->credentialsUsers->removeElement($credentialsUser);
            // set the owning side to null (unless already changed)
            if ($credentialsUser->getUser() === $this) {
                $credentialsUser->setUser(null);
            }
        }

        return $this;
    }
}
