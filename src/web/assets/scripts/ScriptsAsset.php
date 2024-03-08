<?php

namespace dwy\CookieConsentManager\web\assets\scripts;

use Craft;
use craft\web\AssetBundle;
use craft\web\View;
use dwy\CookieConsentManager\Plugin;

/**
 * Includes Tracking Scripts
 */
class ScriptsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $settings = Plugin::getInstance()->getSettings();

        $script = <<<EOD
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}

gtag('consent', 'default', {
    'ad_personalization': 'denied',
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'analytics_storage': 'denied',
    'functionality_storage': 'granted',
});

(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{$settings->gtmId}');

function consentChanged(event) {
    const cookie = event.detail.cookie;

    const functional = cookie.categories && cookie.categories.includes('functional') ? 'granted' : 'denied';
    const analytics = cookie.categories && cookie.categories.includes('analytics') ? 'granted' : 'denied';
    const marketing = cookie.categories && cookie.categories.includes('marketing') ? 'granted' : 'denied';

    gtag('consent', 'update', {
        'ad_personalization': marketing,
        'ad_storage': marketing,
        'ad_user_data': marketing,
        'analytics_storage': analytics,
        'functionality_storage': functional,
    });
}

window.addEventListener('cc:onConsent', consentChanged);
window.addEventListener('cc:onChange', consentChanged);

EOD;

        Craft::$app->getView()->registerJs($script, View::POS_HEAD);

        parent::init();
    }
}
