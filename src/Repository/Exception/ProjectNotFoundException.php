<?php

namespace App\Repository\Exception;

class ProjectNotFoundException extends EntityNotFoundException
{
    public static function bySlug(string $slug): self
    {
        return new self(sprintf('Project not found with slug "%s"', $slug));
    }
}
