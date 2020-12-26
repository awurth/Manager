<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\AddProjectGroupMember;
use App\Repository\ProjectGroupMemberRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectGroupMemberRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"project_group_id", "user_id"})
 * })
 */
class ProjectGroupMember
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
     * @ORM\ManyToOne(targetEntity=ProjectGroup::class, inversedBy="members")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ProjectGroup $projectGroup;

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

    private function __construct(ProjectGroup $projectGroup, User $user, int $accessLevel)
    {
        $this->id = Id::generate();
        $this->projectGroup = $projectGroup;
        $this->user = $user;
        $this->accessLevel = $accessLevel;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public static function createFromGroupMemberAdditionForm(AddProjectGroupMember $addProjectGroupMember): self
    {
        return new self(
            $addProjectGroupMember->getProjectGroup(),
            $addProjectGroupMember->user,
            $addProjectGroupMember->accessLevel
        );
    }

    public static function createOwner(ProjectGroup $projectGroup, User $user): self
    {
        return new self($projectGroup, $user, self::ACCESS_LEVEL_OWNER);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getAccessLevel(): int
    {
        return $this->accessLevel;
    }

    public function getProjectGroup(): ProjectGroup
    {
        return $this->projectGroup;
    }

    public function getUser(): User
    {
        return $this->user;
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
