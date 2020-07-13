<?php

namespace App\Entity;

use App\Form\Model\ChangeProjectGroupSlug;
use App\Form\Model\CreateProjectGroup;
use App\Form\Model\EditProjectGroup;
use App\Repository\ProjectGroupRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=ProjectGroupRepository::class)
 */
class ProjectGroup
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Client $client;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=ProjectGroupMember::class, mappedBy="projectGroup", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $members;

    private function __construct(string $slug, string $name)
    {
        $this->id = Uuid::uuid4();
        $this->slug = $slug;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        $this->members = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function createFromCreationForm(CreateProjectGroup $createProjectGroup, User $owner): self
    {
        $projectGroup = new self($createProjectGroup->slug, $createProjectGroup->name);
        $projectGroup->description = $createProjectGroup->description;
        $projectGroup->client = $createProjectGroup->client;

        $projectGroup->members[] = ProjectGroupMember::createOwner($projectGroup, $owner);

        return $projectGroup;
    }

    public function updateFromEditionForm(EditProjectGroup $editProjectGroup): void
    {
        $this->name = $editProjectGroup->name;
        $this->description = $editProjectGroup->description;
        $this->client = $editProjectGroup->client;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateFromSlugChangeForm(ChangeProjectGroupSlug $changeProjectGroupSlug): void
    {
        $this->slug = $changeProjectGroupSlug->slug;
    }

    public function getMemberByUser(User $user): ?ProjectGroupMember
    {
        foreach ($this->getMembers() as $member) {
            if ($member->getUser() === $user) {
                return $member;
            }
        }

        return null;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
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
     * @return Collection|ProjectGroupMember[]
     */
    public function getMembers(): Collection
    {
        return new ArrayCollection($this->members->toArray());
    }
}
