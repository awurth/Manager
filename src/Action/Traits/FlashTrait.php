<?php

namespace App\Action\Traits;

use Symfony\Component\HttpFoundation\RequestStack;

trait FlashTrait
{
    protected RequestStack $requestStack;

    protected function flash(string $type, string $message): void
    {
        $this->requestStack->getSession()->getFlashBag()->add($type, $message);
    }

    /**
     * @required
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }
}
