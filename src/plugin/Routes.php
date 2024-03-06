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

            $event->rules['cookie-consent-manager/categories'] = 'cookie-consent-manager/cp/categories/index';
            $event->rules['cookie-consent-manager/categories/add'] = 'cookie-consent-manager/cp/categories/add';
            $event->rules['cookie-consent-manager/categories/reorder'] = 'cookie-consent-manager/cp/categories/reorder';
            $event->rules['cookie-consent-manager/categories/<categoryId:\d+>'] = 'cookie-consent-manager/cp/categories/edit';

            $event->rules['cookie-consent-manager/content'] = 'cookie-consent-manager/cp/content/index';
            $event->rules['cookie-consent-manager/content/preferences'] = 'cookie-consent-manager/cp/content/preferences';

            $event->rules['cookie-consent-manager/settings'] = 'cookie-consent-manager/cp/settings/index';
            $event->rules['cookie-consent-manager/settings/gtm'] = 'cookie-consent-manager/cp/settings/gtm';
            $event->rules['cookie-consent-manager/settings/layout'] = 'cookie-consent-manager/cp/settings/layout';
            $event->rules['cookie-consent-manager/settings/developer'] = 'cookie-consent-manager/cp/settings/developer';
            $event->rules['cookie-consent-manager/settings/plugin'] = 'cookie-consent-manager/cp/settings/plugin';
        });
    }

    private function _registerSiteRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['ccm-language.json'] = 'cookie-consent-manager/site/language/current';
        });
    }
}
