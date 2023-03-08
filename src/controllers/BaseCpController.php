<?php

namespace dwy\CookieConsentManager\controllers;

use craft\web\Controller;

class BaseCpController extends Controller
{
    public function init(): void
    {
        parent::init();

        $this->requirePermission('accessPlugin-cookie-consent-manager');
    }
}
