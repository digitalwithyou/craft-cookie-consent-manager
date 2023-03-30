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
        $siteId = $this->_currentSite->id;

        $content = Plugin::getInstance()->content;

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
            'autorun' => $settings->autorun,
            'languages' => [
                $currentLang => [
                    'consent_modal' =>  [
                        'title' => $content->get('consentModalHeading', $siteId),
                        'description' => $this->_parseConsentModalText($content, $siteId),
                        'primary_btn' => [
                            'text' => $content->get('consentModalAcceptButton', $siteId),
                            'role' => 'accept_all',
                        ],
                        'secondary_btn' => [
                            'text' => $content->get('consentModalRejectButton', $siteId),
                            'role' => 'accept_necessary',
                        ],
                    ],
                    'settings_modal' => [
                        'title' => $content->get('settingsModalHeading', $siteId),
                        'save_settings_btn' => $content->get('settingsModalSaveSettingsButton', $siteId),
                        'accept_all_btn' => $content->get('settingsModalAcceptAllButton', $siteId),
                        'reject_all_btn' => $content->get('settingsModalRejectAllButton', $siteId),
                        'close_btn_label' => $content->get('settingsModalCloseButton', $siteId),
                        'cookie_table_headers' => [
                            ['col1' => $content->get('settingsModalCookieName', $siteId)],
                            ['col2' => $content->get('settingsModalCookieDomain', $siteId)],
                            ['col3' => $content->get('settingsModalCookieExpiration', $siteId)],
                            ['col4' => $content->get('settingsModalCookieDescription', $siteId)],
                        ],
                        'blocks' => $this->_getSettingsBlocks($content, $siteId),
                    ],
                ],
            ],
            'gui_options' => [
                'consent_modal' => [
                    'layout' => $settings->layout,
                    'position' => $settings->position,
                    'transition' => $settings->transition,
                    'swap_buttons' => $settings->swapButtons,
                ],
                'settings_modal' => [
                    'layout' => $settings->settingsModalLayout,
                    'transition' => $settings->settingsModalTransition,
                ],
            ],
        ];

        $config = Json::encode($config);

        $view->registerJs("window.cc = initCookieConsent(); window.cc.run({$config}); window.dispatchEvent(new Event('ccmInitiated'));", View::POS_END);
    }

    private function _getSettingsBlocks($content, $siteId): array
    {
        $blocks = [];

        $settingsModalHeaderTitle = $content->get('settingsModalHeaderTitle', $siteId);
        $settingsModalHeaderText = $content->get('settingsModalHeaderText ', $siteId);

        if (!empty($settingsModalHeaderTitle) ||!empty($settingsModalHeaderText) ) {
            $blocks[] = [
                'title' => $settingsModalHeaderTitle,
                'description' => $settingsModalHeaderText,
            ];
        }

        $blocks = array_merge($blocks, $this->_getCategories());

        $settingsModalFooterTitle = $content->get('settingsModalFooterTitle', $siteId);
        $settingsModalFooterText = $content->get('settingsModalFooterText ', $siteId);

        if (!empty($settingsModalFooterTitle) ||!empty($settingsModalFooterText) ) {
            $blocks[] = [
                'title' => $settingsModalFooterTitle,
                'description' => $settingsModalFooterText,
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

    private function _parseConsentModalText($content, $siteId): string
    {
        $settingsLabel = $content->get('consentModalSettingsLabel', $siteId);
        $text = $content->get('consentModalText', $siteId);

        return str_replace(
            '#settings#',
            '<button type="button" data-cc="c-settings" class="cc-link">'.$settingsLabel.'</button>',
            $text
        );
    }
}
