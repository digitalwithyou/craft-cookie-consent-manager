<?php

namespace dwy\CookieConsentManager;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\helpers\UrlHelper;
use dwy\CookieConsentManager\models\Settings;
use dwy\CookieConsentManager\plugin\CpSection;
use dwy\CookieConsentManager\plugin\Routes;
use dwy\CookieConsentManager\plugin\Services;
use dwy\CookieConsentManager\plugin\Translations;

class Plugin extends BasePlugin
{
    use CpSection;
    use Routes;
    use Services;
    use Translations;

    public bool $hasCpSettings = true;

    public function init()
    {
        parent::init();

        $this->_registerServices();
        $this->_registerCpSection();
        $this->_registerCpRoutes();
        $this->_registerTranslations();
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    public function getSettingsResponse(): mixed
    {
        $url = UrlHelper::cpUrl('cookie-consent-manager/settings');

        return Craft::$app->controller->redirect($url);
    }
}
