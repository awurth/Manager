<?php

namespace App\Entity;

use App\Form\Model\Admin\CreateUser;
use App\Form\Model\EditProfile;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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

    private function __construct(string $email, string $firstname, string $lastname)
    {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->createdAt = new DateTimeImmutable();

        $this->cryptographicKeys = new ArrayCollection();
        $this->projectGroupMembers = new ArrayCollection();
        $this->projectMembers = new ArrayCollection();
        $this->serverMembers = new ArrayCollection();
        $this->credentialsUsers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public static function createFromAdminCreationForm(CreateUser $createUser, UserPasswordEncoderInterface $userPasswordEncoder): self
    {
        $user = new self($createUser->email, $createUser->firstname, $createUser->lastname);
        $user->gender = $createUser->gender;
        $user->roles[] = $createUser->role;

        $user->password = $userPasswordEncoder->encodePassword($user, $createUser->plainPassword);

        return $user;
    }

    public function updateFromProfileEditionForm(EditProfile $editProfile): void
    {
        $this->email = $editProfile->email;
        $this->gender = $editProfile->gender;
        $this->firstname = $editProfile->firstname;
        $this->lastname = $editProfile->lastname;
        $this->updatedAt = new DateTimeImmutable();
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

    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    private function addRole($role): self
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
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
     * @return Collection|CryptographicKey[]
     */
    public function getCryptographicKeys(): Collection
    {
        return $this->cryptographicKeys;
    }

    /**
     * @return Collection|ProjectGroupMember[]
     */
    public function getProjectGroupMembers(): Collection
    {
        return $this->projectGroupMembers;
    }

    /**
     * @return Collection|ProjectMember[]
     */
    public function getProjectMembers(): Collection
    {
        return $this->projectMembers;
    }

    /**
     * @return Collection|ServerMember[]
     */
    public function getServerMembers(): Collection
    {
        return $this->serverMembers;
    }

    /**
     * @return Collection|CredentialsUser[]
     */
    public function getCredentialsUsers(): Collection
    {
        return $this->credentialsUsers;
    }
}
