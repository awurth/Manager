<?php

namespace App\Entity;

use App\Form\Model\CreateProject;
use App\Form\Model\EditProject;
use Awurth\UploadBundle\Storage\StorageInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectGroup", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectGroup;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoFilename;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectEnvironment", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $environments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectMember", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=Link::class, mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $links;

    private function __construct(ProjectGroup $projectGroup, string $slug, string $name)
    {
        $this->projectGroup = $projectGroup;
        $this->slug = $slug;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        $this->environments = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->links = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectGroup(): ?ProjectGroup
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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    /**
     * @return Collection|ProjectEnvironment[]
     */
    public function getEnvironments(): Collection
    {
        return $this->environments;
    }

    /**
     * @return Collection|ProjectMember[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @return Collection|Link[]
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }
}
