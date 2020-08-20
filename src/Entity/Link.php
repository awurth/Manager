<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\AddProjectLink;
use App\Form\Model\EditProjectLink;
use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LinkRepository::class)
 */
class Link
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\ManyToOne(targetEntity=LinkType::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?LinkType $linkType = null;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     */
    private ?Project $project = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $uri;

    private function __construct(string $name, string $uri)
    {
        $this->id = Id::generate();
        $this->name = $name;
        $this->uri = $uri;
    }

    public function __toString(): string
    {
        return $this->uri;
    }

    public static function createFromProjectLinkCreationForm(AddProjectLink $addProjectLink, Project $project): self
    {
        $link = new self($addProjectLink->name, $addProjectLink->uri);
        $link->linkType = $addProjectLink->linkType;
        $link->project = $project;

        return $link;
    }

    public function updateFromProjectLinkEditionForm(EditProjectLink $editProjectLink): void
    {
        $this->name = $editProjectLink->name;
        $this->uri = $editProjectLink->uri;
        $this->linkType = $editProjectLink->linkType;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getLinkType(): ?LinkType
    {
        return $this->linkType;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
