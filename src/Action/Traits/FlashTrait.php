<?php

namespace App\Action\Traits;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait FlashTrait
{
    protected SessionInterface $session;

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
