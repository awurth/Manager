<?php

namespace App\Entity;

use App\Repository\ClientContactRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=ClientContactRepository::class)
 */
class ClientContact
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Client $client;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $job;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    private function __construct(Client $client)
    {
        $this->id = Uuid::uuid4();
        $this->client = $client;
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return (string)$this->email;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
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
