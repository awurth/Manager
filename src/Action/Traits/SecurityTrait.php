<?php

namespace App\Action\Traits;

use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Throwable;

trait SecurityTrait
{
    /**
     * @var Security
     */
    protected $security;

    protected function createAccessDeniedException(string $message = 'Access Denied.', ?Throwable $previous = null): AccessDeniedException
    {
        return new AccessDeniedException($message, $previous);
    }

    protected function denyAccessUnlessLoggedIn(): void
    {
        if (!$this->isLoggedIn()) {
            throw new HttpException(401);
        }
    }

    protected function denyAccessUnlessGranted(string $attribute, $subject = null, string $message = 'Access Denied.'): void
    {
        if (!$this->isGranted($attribute, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    protected function getUser(): User
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->security->getUser();
    }

    protected function isGranted(string $attribute, $subject = null): bool
    {
        return $this->security->isGranted($attribute, $subject);
    }

    protected function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * @required
     */
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }
}
