<?php

namespace dwy\CookieConsentManager;

use Craft;
use craft\base\Plugin as BasePlugin;
use dwy\CookieConsentManager\plugin\CpSection;
use dwy\CookieConsentManager\plugin\EventsSite;
use dwy\CookieConsentManager\plugin\Routes;
use dwy\CookieConsentManager\plugin\Services;
use dwy\CookieConsentManager\plugin\Settings;
use dwy\CookieConsentManager\plugin\Translations;

/**
 * In an effort to keep the code as clean as possible,
 * all code you would expect here is managed in the plugin folder.
 */

class Plugin extends BasePlugin
{
    use CpSection;
    use EventsSite;
    use Routes;
    use Services;
    use Settings;
    use Translations;

    public function init()
    {
        parent::init();

        $this->_registerServices();
        $this->_registerCpSection();
        $this->_registerCpRoutes();
        $this->_registerSiteRoutes();
        $this->_registerTranslations();
        $this->_registerSiteEvents();
    }
}
