<?php

namespace App\Entity;

use App\Form\Model\AddProjectMember;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectMemberRepository")
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
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="members")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projectMembers")
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

    private function __construct(Project $project, User $user, int $accessLevel)
    {
        $this->project = $project;
        $this->user = $user;
        $this->accessLevel = $accessLevel;
        $this->createdAt = new DateTimeImmutable();
    }

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

    public function getId(): ?int
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
