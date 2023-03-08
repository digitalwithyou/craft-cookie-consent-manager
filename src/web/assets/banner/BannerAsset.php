<?php

namespace dwy\CookieConsentManager\web\assets\banner;

use craft\web\AssetBundle;
use dwy\CookieConsentManager\Plugin;

/**
 * Asset bundle for the Vanilla Cookie Consent JS library
 */
class BannerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->js = [
            'cookieconsent.js',
        ];

        $this->css = [
            'cookieconsent.css',
        ];

        Plugin::getInstance()->renderer->renderConfig();

        parent::init();
    }
}
