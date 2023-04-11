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

    public function renderConfig(): void
    {
        $view = Craft::$app->getView();
        $settings = Plugin::getInstance()->getSettings();

        $this->_currentSite = Craft::$app->getSites()->getCurrentSite();
        $currentLang = $this->_currentSite->language;
        $siteId = $this->_currentSite->id;

        $content = Plugin::getInstance()->content;

        $config = [
            'mode' => $settings->mode,
            'autoShow' => $settings->autoShow,
            'manageScriptTags' => $settings->manageScriptTags,
            'autoClearCookies' => $settings->autoClearCookies,
            'hideFromBots' => $settings->hideFromBots,
            'disablePageInteraction' => $settings->disablePageInteraction,
            'lazyHtmlGeneration' => $settings->lazyHtmlGeneration,

            'categories' => $this->_getCategories($content, $siteId),

            'cookie' => [
                'name' => $settings->cookieName,
                'domain' => $settings->cookieDomain,
                'path' => $settings->cookiePath,
                'expiresAfterDays' => $settings->cookieExpiration,
                'sameSite' => $settings->cookieSameSite,
            ],

            'language' => [
                'default' => $currentLang,

                'translations' => [
                    $currentLang => [
                        'consentModal' =>  [
                            'label' => $content->get('consentModalLabel', $siteId),
                            'title' => $content->get('consentModalTitle', $siteId),
                            'description' => $this->_parseConsentModalText($content, $siteId),
                            'acceptAllBtn' => $content->get('consentModalAcceptAllButton', $siteId),
                            'acceptNecessaryBtn' => $content->get('consentModalAcceptNecessaryButton', $siteId),
                            'showPreferencesBtn' => $settings->showPreferencesButton ? $content->get('consentModalPreferencesLabel', $siteId) : null,
                            'footer' => $content->get('consentModalFooter', $siteId),
                        ],
                        'preferencesModal' => [
                            'title' => $content->get('preferencesModalTitle', $siteId),
                            'savePreferencesBtn' => $content->get('preferencesModalSavePreferencesButton', $siteId),
                            'acceptAllBtn' => $content->get('preferencesModalAcceptAllButton', $siteId),
                            'acceptNecessaryBtn' => $content->get('preferencesModalAcceptNecessaryButton', $siteId),
                            'closeIconLabel' => $content->get('preferencesModalCloseIconLabel', $siteId),
                            'sections' => $this->_getSettingsBlocks($content, $siteId),
                        ],
                    ],
                ],
            ],

            'guiOptions' => [
                'consentModal' => [
                    'layout' => $settings->layout,
                    'position' => $settings->position,
                    'flipButtons' => $settings->flipButtons,
                    'equalWeightButtons' => $settings->equalWeightButtons,
                ],
                'preferencesModal' => [
                    'layout' => $settings->preferencesModalLayout,
                    'position' => $settings->preferencesModalPosition,
                    'flipButtons' => $settings->preferencesModalFlipButtons,
                    'equalWeightButtons' => $settings->preferencesModalEqualWeightButtons,
                ],
            ],
        ];

        if ($settings->root) {
            $config['root'] = $settings->root;
        }

        ray($config);

        $config = Json::encode($config);

        $view->registerJs("CookieConsent.run({$config});", View::POS_END);
    }

    private function _getSettingsBlocks($content, $siteId): array
    {
        $enabledCategories = Plugin::getInstance()->get('categories')->getAllForSite($this->_currentSite->id, true);
        $blocks = [];

        $preferencesModalHeaderTitle = $content->get('preferencesModalHeaderTitle', $siteId);
        $preferencesModalHeaderText = $content->get('preferencesModalHeaderText', $siteId);

        if (!empty($preferencesModalHeaderTitle) ||!empty($preferencesModalHeaderText) ) {
            $blocks[] = [
                'title' => $preferencesModalHeaderTitle,
                'description' => $preferencesModalHeaderText,
            ];
        }

        foreach ($enabledCategories as $category) {
            $blocks[] = [
                'title' => $category->name,
                'description' => $category->description,
                'linkedCategory' => $category->uid,
            ];
        }

        $preferencesModalFooterTitle = $content->get('preferencesModalFooterTitle', $siteId);
        $preferencesModalFooterText = $content->get('preferencesModalFooterText', $siteId);

        if (!empty($preferencesModalFooterTitle) ||!empty($preferencesModalFooterText) ) {
            $blocks[] = [
                'title' => $preferencesModalFooterTitle,
                'description' => $preferencesModalFooterText,
            ];
        }

        return $blocks;
    }

    private function _getCategories(): array
    {
        $enabledCategories = Plugin::getInstance()->get('categories')->getAllForSite($this->_currentSite->id, true);
        $data = [];

        foreach ($enabledCategories as $category) {
            $data[$category->uid] = [
                'enabled' => $category->default,
                'readOnly' => $category->required,
            ];
        }

        return $data;
    }

    private function _parseConsentModalText($content, $siteId): string
    {
        $buttonLabel = $content->get('consentModalPreferencesLabel', $siteId);
        $text = $content->get('consentModalDescription', $siteId);

        return str_replace(
            '{preferences}',
            '<button type="button" data-cc="show-preferencesModal">'.$buttonLabel.'</button>',
            $text
        );
    }
}
