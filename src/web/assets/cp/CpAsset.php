<?php

namespace dwy\CookieConsentManager\web\assets\cp;

use craft\web\AssetBundle;

/**
 * Asset bundle for the Vanilla Cookie Consent JS library
 */
class CpAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/dist';

        parent::init();
    }
}
