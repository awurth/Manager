<?php

namespace App\Action\Traits;

use App\Security\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

trait SecurityTrait
{
    protected Security $security;

    protected function createAccessDeniedException(string $message = 'Access Denied.', ?Throwable $previous = null): AccessDeniedException
    {
        return new AccessDeniedException($message, $previous);
    }

    protected function denyAccessUnlessLoggedIn(): void
    {
        if (!$this->security->isLoggedIn()) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param string     $attribute
     * @param mixed|null $subject
     * @param string     $message
     */
    protected function denyAccessUnlessGranted(string $attribute, $subject = null, string $message = 'Access Denied.'): void
    {
        if (!$this->security->isGranted($attribute, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    /**
     * @required
     */
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }
}
