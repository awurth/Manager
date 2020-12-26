<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\ChangeProjectGroupSlug;
use App\Form\Model\CreateProjectGroup;
use App\Form\Model\EditProjectGroup;
use App\Repository\ProjectGroupRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectGroupRepository::class)
 */
class ProjectGroup
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Client $client = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

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
     * @ORM\OneToMany(targetEntity=ProjectGroupMember::class, mappedBy="projectGroup", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $members;

    private function __construct(string $slug, string $name)
    {
        $this->id = Id::generate();
        $this->slug = $slug;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        $this->members = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public static function createFromCreationForm(CreateProjectGroup $createProjectGroup, User $owner): self
    {
        $projectGroup = new self($createProjectGroup->slug, $createProjectGroup->name);
        $projectGroup->description = $createProjectGroup->description;
        $projectGroup->client = $createProjectGroup->client;

        $projectGroup->members[] = ProjectGroupMember::createOwner($projectGroup, $owner);

        return $projectGroup;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromEditionForm(EditProjectGroup $editProjectGroup): void
    {
        $this->name = $editProjectGroup->name;
        $this->description = $editProjectGroup->description;
        $this->client = $editProjectGroup->client;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromSlugChangeForm(ChangeProjectGroupSlug $changeProjectGroupSlug): void
    {
        $this->slug = $changeProjectGroupSlug->slug;
        $this->updatedAt = new DateTimeImmutable();
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

    public function getId(): Id
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
     * @psalm-suppress MismatchingDocblockReturnType
     * @return ArrayCollection|ProjectGroupMember[]
     */
    public function getMembers(): ArrayCollection
    {
        return new ArrayCollection($this->members->toArray());
    }
}
