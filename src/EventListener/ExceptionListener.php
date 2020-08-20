<?php

namespace App\EventListener;

use App\Repository\Exception\EntityNotFoundException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        do {
            if ($exception instanceof EntityNotFoundException) {
                $this->handleEntityNotFoundException($event, $exception);
                return;
            }
        } while (null !== $exception = $exception->getPrevious());
    }

    private function handleEntityNotFoundException(ExceptionEvent $event, EntityNotFoundException $exception): void
    {
        $event->setThrowable(new NotFoundHttpException($exception->getMessage(), $exception));
    }
}
