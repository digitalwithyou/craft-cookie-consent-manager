<?php

namespace dwy\CookieConsentManager\plugin;

use Craft;
use craft\i18n\PhpMessageSource;

trait Translations
{
    private function _registerTranslations(): void
    {
        Craft::$app->i18n->translations['cookie-banner'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => $this->getBasePath() . DIRECTORY_SEPARATOR . 'translations',
            'allowOverrides' => true,
            'forceTranslation' => true,
        ];
    }
}
