<?php

namespace dwy\CookieConsentManager\controllers\site;

use craft\web\Controller;

class BaseSiteController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function init(): void
    {
        parent::init();

        $this->requireSiteRequest();
    }
}
