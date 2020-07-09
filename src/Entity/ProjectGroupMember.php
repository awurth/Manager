<?php

namespace App\Entity;

use App\Form\Model\AddProjectGroupMember;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectGroupMemberRepository")
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
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectGroup", inversedBy="members")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projectGroupMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $accessLevel;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    private function __construct(ProjectGroup $projectGroup, User $user, int $accessLevel)
    {
        $this->projectGroup = $projectGroup;
        $this->user = $user;
        $this->accessLevel = $accessLevel;
        $this->createdAt = new DateTimeImmutable();
    }

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

    public function getId(): ?int
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
