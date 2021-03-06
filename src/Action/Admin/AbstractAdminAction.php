<?php

namespace App\Action\Admin;

use App\Action\Traits\BreadcrumbsTrait;

abstract class AbstractAdminAction
{
    use BreadcrumbsTrait;

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->prependRouteItem('breadcrumb.admin.administration', 'app_admin');
    }
}
