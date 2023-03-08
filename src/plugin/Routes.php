<?php

namespace dwy\CookieConsentManager\plugin;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

trait Routes
{
    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['cookie-consent-manager'] = ['template' => 'cookie-consent-manager/index'];

            $event->rules['cookie-consent-manager/categories'] = 'cookie-consent-manager/categories/index';
            $event->rules['cookie-consent-manager/categories/add'] = 'cookie-consent-manager/categories/add';
            $event->rules['cookie-consent-manager/categories/reorder'] = 'cookie-consent-manager/categories/reorder';
            $event->rules['cookie-consent-manager/categories/<categoryId:\d+>'] = 'cookie-consent-manager/categories/edit';

            $event->rules['cookie-consent-manager/settings'] = 'cookie-consent-manager/settings/index';
        });
    }
}
