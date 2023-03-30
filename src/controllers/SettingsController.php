<?php

namespace dwy\CookieConsentManager\controllers;

use Craft;
use craft\web\Response;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\helpers\Request as RequestHelpers;

class SettingsController extends BaseCpController
{
    public function init(): void
    {
        parent::init();

        $this->requireAdmin();
    }

    public function actionIndex(): Response
    {
        $settings = Plugin::getInstance()->getSettings();

        return $this->renderTemplate('cookie-consent-manager/settings/_index', compact(
            'settings',
        ));
    }

    public function actionLayout(): Response
    {
        $settings = Plugin::getInstance()->getSettings();

        return $this->renderTemplate('cookie-consent-manager/settings/_layout', compact(
            'settings',
        ));
    }

    public function actionCookies(): Response
    {
        $settings = Plugin::getInstance()->getSettings();

        return $this->renderTemplate('cookie-consent-manager/settings/_cookies', compact(
            'settings',
        ));
    }

    public function actionPlugin(): Response
    {
        $settings = Plugin::getInstance()->getSettings();

        return $this->renderTemplate('cookie-consent-manager/settings/_plugin', compact(
            'settings',
        ));
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $params = RequestHelpers::getAllParams();

        $settings = Plugin::getInstance()->getSettings();

        foreach ($params as $key => $value) {
            $settings->$key = $value;
        }

        if (!$settings->validate()) {
            $this->setFailFlash(Craft::t('app', 'Please fill in all required settings.'));
        }
        elseif (!Craft::$app->getPlugins()->savePluginSettings(Plugin::getInstance(), $settings->toArray())) {
            $this->setFailFlash(Craft::t('app', 'Couldn’t save settings.'));
        }
        else {
            $this->setSuccessFlash(Craft::t('app', 'Settings saved.'));
        }

        return $this->redirectToPostedUrl();
    }
}
