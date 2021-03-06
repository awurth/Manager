<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\AddProjectMember;
use App\Repository\ProjectMemberRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectMemberRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"project_id", "user_id"})
 * })
 */
class ProjectMember
{
    public const ACCESS_LEVEL_GUEST = 0;
    public const ACCESS_LEVEL_MEMBER = 10;
    public const ACCESS_LEVEL_OWNER = 100;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="members")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Project $project;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @ORM\Column(type="integer")
     */
    private int $accessLevel;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    private function __construct(Project $project, User $user, int $accessLevel)
    {
        $this->id = Id::generate();
        $this->project = $project;
        $this->user = $user;
        $this->accessLevel = $accessLevel;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public static function createFromProjectMemberAdditionForm(AddProjectMember $addProjectMember): self
    {
        return new self(
            $addProjectMember->getProject(),
            $addProjectMember->user,
            $addProjectMember->accessLevel
        );
    }

    public static function createOwner(Project $project, User $user): self
    {
        return new self($project, $user, self::ACCESS_LEVEL_OWNER);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAccessLevel(): int
    {
        return $this->accessLevel;
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
