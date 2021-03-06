<?php

namespace App\Action\Traits;

use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

trait BreadcrumbsTrait
{
    protected Breadcrumbs $breadcrumbs;

    protected function configureBreadcrumbs(): void
    {
    }

    /**
     * @required
     */
    public function setBreadcrumbs(Breadcrumbs $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;

        $this->configureBreadcrumbs();
    }
}
