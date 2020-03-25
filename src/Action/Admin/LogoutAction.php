<?php

namespace App\Action\Admin;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/logout", name="admin_logout")
 */
class LogoutAction
{
    public function __invoke(): void
    {
    }
}
