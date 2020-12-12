<?php

namespace App\Entity\ValueObject;

use Symfony\Component\Uid\Uuid;

class Id
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $id): self
    {
        return new static($id);
    }

    public static function generate(): self
    {
        return new static((string)Uuid::v4());
    }

    public function equals(Id $id): bool
    {
        return $id->toString() === $this->id;
    }

    public function toString(): string
    {
        return $this->id;
    }
}
