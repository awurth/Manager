<?php

namespace App\Action;

use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

trait BreadcrumbsTrait
{
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

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
