<?php

namespace App\EventListener;

use App\Repository\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class ExceptionListener
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        do {
            if ($exception instanceof HttpException) {
                $this->handleHttpException($event, $exception);
                return;
            }

            if ($exception instanceof EntityNotFoundException) {
                $this->handleEntityNotFoundException($event, $exception);
                return;
            }
        } while (null !== $exception = $exception->getPrevious());
    }

    private function handleHttpException(ExceptionEvent $event, HttpException $exception): void
    {
        if ($exception->getStatusCode() === 401) {
            $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
        }
    }

    private function handleEntityNotFoundException(ExceptionEvent $event, EntityNotFoundException $exception): void
    {
        $event->setThrowable(new NotFoundHttpException($exception->getMessage(), $exception));
    }
}
