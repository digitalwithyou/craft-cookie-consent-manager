<?php

namespace dwy\CookieConsentManager\plugin;

use Craft;
use craft\base\Model;
use craft\helpers\UrlHelper;
use dwy\CookieConsentManager\models\Settings as SettingsModel;

trait Settings
{
    protected function createSettingsModel(): ?Model
    {
        return new SettingsModel();
    }

    public function getSettingsResponse(): mixed
    {
        $url = UrlHelper::cpUrl('cookie-consent-manager/settings');

        return Craft::$app->controller->redirect($url);
    }
}
