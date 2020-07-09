<?php

namespace App\Entity;

use App\Form\Model\Admin\CreateClient;
use App\Form\Model\Admin\EditClient;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
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
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClientContact", mappedBy="client", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectGroup", mappedBy="client", cascade={"persist"})
     */
    private $projectGroups;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        $this->contacts = new ArrayCollection();
        $this->projectGroups = new ArrayCollection();
    }

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

    public function getId(): ?int
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

    /**
     * @return Collection|ClientContact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @return Collection|ProjectGroup[]
     */
    public function getProjectGroups(): Collection
    {
        return $this->projectGroups;
    }
}
