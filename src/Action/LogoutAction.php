<?php

namespace App\Action;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/logout", name="app_logout")
 */
final class LogoutAction
{
    public function __invoke(): void
    {
    }
}
