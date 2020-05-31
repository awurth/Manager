<?php

namespace App\Entity;

use App\Repository\LinkTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LinkTypeRepository::class)
 */
class LinkType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uriPrefix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $iconFilename;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getUriPrefix(): ?string
    {
        return $this->uriPrefix;
    }

    public function setUriPrefix(?string $uriPrefix): self
    {
        $this->uriPrefix = $uriPrefix;

        return $this;
    }

    public function getIconFilename(): ?string
    {
        return $this->iconFilename;
    }

    public function setIconFilename(?string $iconFilename): self
    {
        $this->iconFilename = $iconFilename;

        return $this;
    }
}
