<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    public const GENDER_FEMALE = 0;
    public const GENDER_MALE = 1;
    public const GENDER_NEUTRAL = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=255)
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
     *
     * @Assert\NotBlank()
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
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
     * @ORM\OneToMany(targetEntity="App\Entity\CryptographicKey", mappedBy="user")
     */
    private $cryptographicKeys;

    private $plainPassword;

    public function __construct()
    {
        $this->cryptographicKeys = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->email;
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

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
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
}
