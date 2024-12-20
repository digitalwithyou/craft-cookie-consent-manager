<?php

namespace dwy\CookieConsentManager\services;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\helpers\Template;
use craft\models\Site;
use craft\web\View;
use dwy\CookieConsentManager\helpers\ArrayHelper;
use dwy\CookieConsentManager\models\Settings;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\services\Content;
use yii\base\Component;

class Renderer extends Component
{
    private ?Content $_contentService = null;
    private ?Settings $_settings = null;
    private ?Site $_currentSite = null;

    public function __construct()
    {
        $this->_contentService = Plugin::getInstance()->get('content');
        $this->_settings = Plugin::getInstance()->getSettings();
        $this->_currentSite = Craft::$app->getSites()->getCurrentSite();
    }

    public function renderConfig(): void
    {
        $config = [
            'root' => $this->_settings->root,
            'mode' => $this->_settings->mode,
            'autoShow' => $this->_settings->autoShow,
            'manageScriptTags' => $this->_settings->manageScriptTags,
            'autoClearCookies' => $this->_settings->autoClearCookies,
            'hideFromBots' => $this->_settings->hideFromBots,
            'disablePageInteraction' => $this->_settings->disablePageInteraction,
            'lazyHtmlGeneration' => $this->_settings->lazyHtmlGeneration,

            'categories' => $this->_buildCategories(),

            'cookie' => [
                'name' => $this->_settings->cookieName,
                'domain' => $this->_settings->cookieDomain,
                'path' => $this->_settings->cookiePath,
                'expiresAfterDays' => $this->_settings->cookieExpiration,
                'sameSite' => $this->_settings->cookieSameSite,
            ],

            'language' => $this->_buildLanguages(),

            'guiOptions' => [
                'consentModal' => [
                    'layout' => $this->_settings->layout,
                    'position' => $this->_settings->position,
                    'flipButtons' => $this->_settings->flipButtons,
                    'equalWeightButtons' => $this->_settings->equalWeightButtons,
                ],
                'preferencesModal' => [
                    'layout' => $this->_settings->preferencesModalLayout,
                    'position' => $this->_settings->preferencesModalPosition,
                    'flipButtons' => $this->_settings->preferencesModalFlipButtons,
                    'equalWeightButtons' => $this->_settings->preferencesModalEqualWeightButtons,
                ],
            ],
        ];

        $config = ArrayHelper::filter($config);
        $config = Json::encode($config);

        Craft::$app->getView()->registerJs("CookieConsent.run({$config});", View::POS_END);
    }

    private function _buildCategories(): array
    {
        $enabledCategories = Plugin::getInstance()->get('categories')->getAllForSite($this->_currentSite->id, true);
        $data = [];

        foreach ($enabledCategories as $category) {
            $data[$category->handle] = [
                'enabled' => $category->default,
                'readOnly' => $category->required,
            ];
        }

        return $data;
    }

    private function _buildLanguages(): array
    {
        $siteId = $this->_currentSite->id;
        $language = $this->_currentSite->language;

        $data = [
            'default' => $language,

            'translations' => [
                $language => UrlHelper::siteUrl("/ccm-language.json"),
            ],
        ];

        if (!empty($this->_settings->autoDetectLanguage)) {
            $data['autoDetect'] = $this->_settings->autoDetectLanguage;
        }

        if ($this->_currentSite->getLocale()->getOrientation() == 'rtl') {
            $data['rtl'] = [$language];
        }

        return $data;
    }

    public function getLanguage(int $siteId): array
    {
        return [
            'consentModal' =>  [
                'label' => $this->_contentService->get('consentModalLabel', $siteId),
                'title' => $this->_contentService->get('consentModalTitle', $siteId),
                'description' => $this->_parseConsentModalDescription(),
                'acceptAllBtn' => $this->_contentService->get('consentModalAcceptAllButton', $siteId),
                'acceptNecessaryBtn' => $this->_contentService->get('consentModalAcceptNecessaryButton', $siteId),
                'showPreferencesBtn' => $this->_settings->showPreferencesButton ? $this->_contentService->get('consentModalPreferencesLabel', $siteId) : null,
                'footer' => $this->_contentService->get('consentModalFooter', $siteId),
            ],
            'preferencesModal' => [
                'title' => $this->_contentService->get('preferencesModalTitle', $siteId),
                'savePreferencesBtn' => $this->_contentService->get('preferencesModalSavePreferencesButton', $siteId),
                'acceptAllBtn' => $this->_contentService->get('preferencesModalAcceptAllButton', $siteId),
                'acceptNecessaryBtn' => $this->_contentService->get('preferencesModalAcceptNecessaryButton', $siteId),
                'closeIconLabel' => $this->_contentService->get('preferencesModalCloseIconLabel', $siteId),
                'sections' => $this->_buildPreferencesModalSections(),
            ],
        ];
    }

    private function _buildPreferencesModalSections(): array
    {
        $enabledCategories = Plugin::getInstance()->get('categories')->getAllForSite($this->_currentSite->id, true);
        $blocks = [];

        $preferencesModalHeaderTitle = $this->_contentService->get('preferencesModalHeaderTitle', $this->_currentSite->id);
        $preferencesModalHeaderText = $this->_contentService->get('preferencesModalHeaderText', $this->_currentSite->id);

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
                'linkedCategory' => $category->handle,
            ];
        }

        $preferencesModalFooterTitle = $this->_contentService->get('preferencesModalFooterTitle', $this->_currentSite->id);
        $preferencesModalFooterText = $this->_contentService->get('preferencesModalFooterText', $this->_currentSite->id);

        if (!empty($preferencesModalFooterTitle) ||!empty($preferencesModalFooterText) ) {
            $blocks[] = [
                'title' => $preferencesModalFooterTitle,
                'description' => $preferencesModalFooterText,
            ];
        }

        return $blocks;
    }

    private function _parseConsentModalDescription(): string
    {
        $buttonLabel = $this->_contentService->get('consentModalPreferencesLabel', $this->_currentSite->id);
        $text = $this->_contentService->get('consentModalDescription', $this->_currentSite->id);

        return str_replace(
            '{preferences}',
            '<button data-cc="show-preferencesModal">'.$buttonLabel.'</button>',
            $text
        );
    }
}
