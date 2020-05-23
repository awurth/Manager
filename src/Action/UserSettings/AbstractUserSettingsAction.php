<?php

namespace App\Action\UserSettings;

use App\Action\BreadcrumbsTrait;
use App\Action\SecurityTrait;

abstract class AbstractUserSettingsAction
{
    use BreadcrumbsTrait;
    use SecurityTrait;

    protected function preInvoke(bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        if ($breadcrumb) {
            $this->breadcrumbs->prependRouteItem('breadcrumb.user_settings.user_settings', 'app_profile');
        }
    }
}
