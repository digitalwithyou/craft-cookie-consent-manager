<?php

namespace dwy\CookieConsentManager\services;

use Craft;
use craft\helpers\Json;
use craft\helpers\Template;
use craft\models\Site;
use craft\web\View;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\helpers\I18N as I18NHelper;
use yii\base\Component;

class Renderer extends Component
{
    private ?Site $_currentSite = null;

    public function renderConfig()
    {
        $view = Craft::$app->getView();
        $settings = Plugin::getInstance()->getSettings();

        $this->_currentSite = Craft::$app->getSites()->getCurrentSite();
        $currentLang = $this->_currentSite->language;

        $config = [
            'current_lang' => $currentLang,
            'delay' => $settings->delay,
            'mode' => $settings->mode,
            'cookie_expiration' => $settings->cookieExpiration,
            'cookie_necessary_only_expiration' => $settings->cookieNecessaryOnlyExpiration,
            'cookie_samesite' => $settings->cookieSameSite,
            'use_rfc_cookie' => $settings->useRfcCookie,
            'force_consent' => $settings->forceConsent,
            'autoclear_cookies' => $settings->autoclearCookies,
            'page_scripts' => $settings->pageScripts,
            'remove_cookie_tables' => $settings->removeCookieTables,
            'hide_from_bots' => $settings->hideFromBots,
            'languages' => [
                $currentLang => [
                    'consent_modal' =>  [
                        'title' => Craft::t('banner', 'We use cookies'),
                        'description' => Craft::t('banner', 'This website uses cookies. <button type="button" data-cc="c-settings" class="cc-link">Preferences</button>'),
                        'primary_btn' => [
                            'text' => Craft::t('banner', 'Accept all'),
                            'role' => 'accept_all',
                        ],
                        'secondary_btn' => [
                            'text' => Craft::t('banner', 'Reject all'),
                            'role' => 'accept_necessary',
                        ],
                    ],
                    'settings_modal' => [
                        'title' => Craft::t('banner', 'Cookie preferences'),
                        'save_settings_btn' => Craft::t('banner', 'Save settings'),
                        'accept_all_btn' => Craft::t('banner', 'Accept all'),
                        'reject_all_btn' => Craft::t('banner', 'Reject all'),
                        'close_btn_label' => Craft::t('banner', 'Close'),
                        'cookie_table_headers' => [
                            ['col1' => Craft::t('banner', 'Name')],
                            ['col2' => Craft::t('banner', 'Domain')],
                            ['col3' => Craft::t('banner', 'Expiration')],
                            ['col4' => Craft::t('banner', 'Description')],
                        ],
                        'blocks' => $this->_getSettingsBlocks(),
                    ],
                ],
            ],
        ];

        $config = Json::encode($config);

        $view->registerJs("var cc = initCookieConsent(); cc.run({$config});", View::POS_END);
    }

    private function _getSettingsBlocks(): array
    {
        $blocks = [];

        if (I18NHelper::isTranslated('ConsentSettingsHeaderTitle') || I18NHelper::isTranslated('ConsentSettingsHeaderDescription')) {
            $blocks[] = [
                'title' => Craft::t('banner', 'ConsentSettingsHeaderTitle'),
                'description' => Craft::t('banner', 'ConsentSettingsHeaderDescription'),
            ];
        }

        $blocks = array_merge($blocks, $this->_getCategories());

        if (I18NHelper::isTranslated('ConsentSettingsFooterTitle') || I18NHelper::isTranslated('ConsentSettingsFooterDescription')) {
            $blocks[] = [
                'title' => Craft::t('banner', 'ConsentSettingsFooterTitle'),
                'description' => Craft::t('banner', 'ConsentSettingsFooterDescription'),
            ];
        }

        return $blocks;
    }

    private function _getCategories(): array
    {
        $enabledCategories = Plugin::getInstance()->get('categories')->getAllForSite($this->_currentSite->id, true);
        $data = [];

        foreach ($enabledCategories as $category) {
            $data[] = [
                'title' => $category->name,
                'description' => $category->description,
                'toggle' => [
                    'enabled' => $category->default,
                    'readonly' => $category->required,
                ],
            ];
        }

        return $data;
    }
}
