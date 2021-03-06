<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\CreateProject;
use App\Form\Model\EditProject;
use App\Repository\ProjectRepository;
use Awurth\UploadBundle\Storage\StorageInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectGroup::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ProjectGroup $projectGroup;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $logoFilename = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=ProjectMember::class, mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $members;

    private function __construct(ProjectGroup $projectGroup, string $slug, string $name)
    {
        $this->id = Id::generate();
        $this->projectGroup = $projectGroup;
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
    public static function createFromCreationForm(CreateProject $createProject, User $owner, StorageInterface $uploader): self
    {
        $project = new self($createProject->projectGroup, $createProject->slug, $createProject->name);
        $project->description = $createProject->description;

        $project->members[] = ProjectMember::createOwner($project, $owner);

        if ($createProject->logoFile) {
            $upload = $uploader->upload($createProject->logoFile, $project, 'project_logo');
            $project->logoFilename = $upload->getFilename();
        }

        return $project;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function updateFromEditionForm(EditProject $editProject, StorageInterface $uploader): void
    {
        $this->name = $editProject->name;
        $this->description = $editProject->description;
        $this->updatedAt = new DateTimeImmutable();

        if ($editProject->logoFile) {
            $upload = $uploader->upload($editProject->logoFile, $this, 'project_logo');
            $this->logoFilename = $upload->getFilename();
        }
    }

    public function getMemberByUser(User $user): ?ProjectMember
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

    public function getProjectGroup(): ProjectGroup
    {
        return $this->projectGroup;
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

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
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
     * @return ArrayCollection|ProjectMember[]
     */
    public function getMembers(): ArrayCollection
    {
        return new ArrayCollection($this->members->toArray());
    }
}
