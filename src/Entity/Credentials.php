<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\CreateCredentials;
use App\Form\Model\EditCredentials;
use App\Repository\CredentialsRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CredentialsRepository::class)
 */
class Credentials
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
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $website = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=CredentialsUser::class, mappedBy="credentials", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $credentialsUsers;

    private function __construct(string $name, string $password)
    {
        $this->id = Id::generate();
        $this->name = $name;
        $this->password = $password;
        $this->createdAt = new DateTimeImmutable();

        $this->credentialsUsers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public static function createFromCreationForm(CreateCredentials $createCredentials, User $owner): self
    {
        $credentials = new self($createCredentials->name, $createCredentials->password);
        $credentials->username = $createCredentials->username;
        $credentials->email = $createCredentials->email;
        $credentials->website = $createCredentials->website;
        $credentials->description = $createCredentials->description;

        $credentials->credentialsUsers[] = CredentialsUser::createOwner($credentials, $owner);
        foreach ($createCredentials->users as $user) {
            $credentials->credentialsUsers[] = CredentialsUser::createUser($credentials, $user);
        }

        return $credentials;
    }

    public function updateFromEditionForm(EditCredentials $editCredentials, User $currentUser): void
    {
        $this->name = $editCredentials->name;
        $this->username = $editCredentials->username;
        $this->password = $editCredentials->password;
        $this->email = $editCredentials->email;
        $this->website = $editCredentials->website;
        $this->description = $editCredentials->description;
        $this->updatedAt = new DateTimeImmutable();

        foreach ($this->credentialsUsers as $credentialsUser) {
            if ($credentialsUser->getUser() !== $currentUser && !in_array($credentialsUser->getUser(), $editCredentials->users, true)) {
                $this->credentialsUsers->removeElement($credentialsUser);
            }
        }

        foreach ($editCredentials->users as $user) {
            $alreadyAdded = false;
            foreach ($this->credentialsUsers as $credentialsUser) {
                if ($credentialsUser->getUser() === $user) {
                    $alreadyAdded = true;
                    break;
                }
            }

            if (!$alreadyAdded) {
                $this->credentialsUsers[] = CredentialsUser::createUser($this, $user);
            }
        }
    }


    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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
     * @return ArrayCollection|CredentialsUser[]
     */
    public function getCredentialsUsers(): ArrayCollection
    {
        return new ArrayCollection($this->credentialsUsers->toArray());
    }
}
