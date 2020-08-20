<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security as SymfonySecurity;

final class Security
{
    private SymfonySecurity $security;

    public function __construct(SymfonySecurity $security)
    {
        $this->security = $security;
    }

    public function getUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new HttpException(401);
        }

        return $user;
    }

    public function isGranted(string $attribute, $subject = null): bool
    {
        return $this->security->isGranted($attribute, $subject);
    }
}
