<?php

namespace App\Entity;

use App\Form\Model\AddProjectEnvironment;
use App\Form\Model\EditProjectEnvironment;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectEnvironmentRepository")
 */
class ProjectEnvironment
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="environments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Server", inversedBy="projectEnvironments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $server;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

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

    private function __construct(Project $project, Server $server, string $name, string $path)
    {
        $this->id = Uuid::uuid4();
        $this->project = $project;
        $this->server = $server;
        $this->name = $name;
        $this->path = $path;
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function createFromCreationForm(AddProjectEnvironment $addProjectEnvironment, Project $project): self
    {
        $environment = new self(
            $project,
            $addProjectEnvironment->server,
            $addProjectEnvironment->name,
            $addProjectEnvironment->path
        );

        $environment->url = $addProjectEnvironment->url;
        $environment->description = $addProjectEnvironment->description;

        return $environment;
    }

    public function updateFromEditionForm(EditProjectEnvironment $editProjectEnvironment): void
    {
        $this->name = $editProjectEnvironment->name;
        $this->path = $editProjectEnvironment->path;
        $this->url = $editProjectEnvironment->url;
        $this->description = $editProjectEnvironment->description;
        $this->server = $editProjectEnvironment->server;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): ?string
    {
        return $this->url;
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
}
