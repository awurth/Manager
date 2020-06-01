<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait FlashTrait
{
    /**
     * @var Session
     */
    protected $session;

    protected function flash(string $type, string $message): void
    {
        $this->session->getFlashBag()->add($type, $message);
    }

    /**
     * @required
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }
}
