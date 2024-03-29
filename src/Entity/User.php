<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\Admin\CreateUser;
use App\Form\Model\ChangePassword;
use App\Form\Model\EditProfile;
use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private string $password;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastname;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    private function __construct(string $email, string $firstname, string $lastname)
    {
        $this->id = Id::generate();
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public static function createFromAdminCreationForm(CreateUser $createUser, UserPasswordHasherInterface $userPasswordHasher): self
    {
        $user = new self($createUser->email, $createUser->firstname, $createUser->lastname);
        $user->roles[] = $createUser->role;

        $user->password = $userPasswordHasher->hashPassword($user, $createUser->plainPassword);

        return $user;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromPasswordChangeForm(ChangePassword $changePassword, UserPasswordHasherInterface $userPasswordHasher): void
    {
        $this->password = $userPasswordHasher->hashPassword($this, $changePassword->newPassword);
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromProfileEditionForm(EditProfile $editProfile): void
    {
        $this->email = $editProfile->email;
        $this->firstname = $editProfile->firstname;
        $this->lastname = $editProfile->lastname;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getFullName(): string
    {
        return $this->getFirstname().' '.$this->getLastname();
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

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    private function addRole(string $role): self
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

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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
}
