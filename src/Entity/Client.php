<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\Admin\CreateClient;
use App\Form\Model\Admin\EditClient;
use App\Repository\ClientRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
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
    private ?string $address = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $postcode = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $phone = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    private function __construct(string $name)
    {
        $this->id = Id::generate();
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public static function createFromAdminCreationForm(CreateClient $createClient): self
    {
        $client = new self($createClient->name);
        $client->address = $createClient->address;
        $client->postcode = $createClient->postcode;
        $client->city = $createClient->city;
        $client->phone = $createClient->phone;
        $client->email = $createClient->email;

        return $client;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromAdminEditionForm(EditClient $editClient): void
    {
        $this->name = $editClient->name;
        $this->address = $editClient->address;
        $this->postcode = $editClient->postcode;
        $this->city = $editClient->city;
        $this->phone = $editClient->phone;
        $this->email = $editClient->email;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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
