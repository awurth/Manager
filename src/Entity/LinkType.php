<?php

namespace App\Entity;

use App\Entity\ValueObject\Id;
use App\Form\Model\Admin\CreateLinkType;
use App\Form\Model\Admin\EditLinkType;
use App\Repository\LinkTypeRepository;
use Awurth\UploadBundle\Storage\StorageInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LinkTypeRepository::class)
 */
class LinkType
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private ?string $color = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $uriPrefix = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $iconFilename = null;

    private function __construct(string $name)
    {
        $this->id = Id::generate();
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function createFromAdminCreationForm(CreateLinkType $createLinkType, StorageInterface $uploader): self
    {
        $linkType = new self($createLinkType->name);
        $linkType->color = $createLinkType->color;
        $linkType->uriPrefix = $createLinkType->uriPrefix;

        if ($createLinkType->iconFile) {
            $upload = $uploader->upload($createLinkType->iconFile, $linkType, 'link_type_icon');
            $linkType->iconFilename = $upload->getFilename();
        }

        return $linkType;
    }

    public function updateFromAdminEditionForm(EditLinkType $editLinkType, StorageInterface $uploader): void
    {
        $this->name = $editLinkType->name;
        $this->color = $editLinkType->color;
        $this->uriPrefix = $editLinkType->uriPrefix;

        if ($editLinkType->iconFile) {
            $upload = $uploader->upload($editLinkType->iconFile, $this, 'link_type_icon');
            $this->iconFilename = $upload->getFilename();
        }
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getUriPrefix(): ?string
    {
        return $this->uriPrefix;
    }

    public function getIconFilename(): ?string
    {
        return $this->iconFilename;
    }
}
