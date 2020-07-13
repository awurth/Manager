<?php

namespace App\Repository\Exception;

class ServerNotFoundException extends EntityNotFoundException
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Server not found with id "%s"', $id));
    }
}
