<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
            throw new AccessDeniedException();
        }

        return $user;
    }

    public function isGranted(string $attribute, ?object $subject = null): bool
    {
        return $this->security->isGranted($attribute, $subject);
    }

    public function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
