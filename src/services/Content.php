<?php

namespace dwy\CookieConsentManager\services;

use Craft;
use craft\db\ActiveRecord;
use craft\db\Query;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\records\Content as ContentRecord;
use yii\base\Component;

class Content extends Component
{
    /**
     * All content data is temporarily stored in this property to avoid multiple database queries.
     * Structure: [siteId => [key => value]]
     *
     * @var array|null
     */
    private array $_data = [];

    public function get($key, $siteId = null): string
    {
        if ($this->useCraftTranslations()) {
            return Craft::t('cookie-banner', $key);
        }

        return $this->getFromDb($key, $siteId);
    }

    public function getFromDb($key, $siteId = null): string
    {
        if ($siteId === null) {
            $siteId = Craft::$app->getSites()->getCurrentSite()->id;
        }

        if (!isset($this->_data[$siteId])) {
            $this->storeDbData($siteId);
        }

        return $this->_data[$siteId][$key] ?? '';
    }

    public function getAllFromDb($siteId = null): array
    {
        if ($siteId === null) {
            $siteId = Craft::$app->getSites()->getCurrentSite()->id;
        }

        if (!isset($this->_data[$siteId])) {
            $this->storeDbData($siteId);
        }

        return $this->_data[$siteId] ?? [];
    }

    private function storeDbData($siteId): void
    {
        $results = ContentRecord::find()
            ->where(['siteId' => $siteId])
            ->all();

        $this->_data[$siteId] = $this->_mapResults($results);
    }

    public function save(array $data, int $siteId): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            foreach ($data as $key => $value) {
                $content = ContentRecord::findOne([
                    'siteId' => $siteId,
                    'key' => $key,
                ]);

                // It should never happen that a content record is missing, but just in case...
                if (!$content) {
                    $content = new ContentRecord();
                    $content->siteId = $siteId;
                    $content->key = $key;
                }

                $content->value = $value;
                $content->save();
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    protected function _mapResults(array $results): array
    {
        $data = [];

        foreach ($results as $result) {
            $data[$result['key']] = $result['value'];
        }

        return $data;
    }

    protected function useCraftTranslations(): bool
    {
        return Plugin::getInstance()->getSettings()->manageContentAsTranslations;
    }

    public function insertDefaultDataForSite($siteId): void
    {
        $site = Craft::$app->getSites()->getSiteById($siteId);

        $data = [
            'consentModalLabel',
            'consentModalTitle',
            'consentModalDescription',
            'consentModalPreferencesLabel',
            'consentModalAcceptAllButton',
            'consentModalAcceptNecessaryButton',
            'consentModalFooter',
            'preferencesModalTitle',
            'preferencesModalAcceptAllButton',
            'preferencesModalAcceptNecessaryButton',
            'preferencesModalSavePreferencesButton',
            'preferencesModalCloseIconLabel',
            'preferencesModalHeaderTitle',
            'preferencesModalHeaderText',
            'preferencesModalFooterTitle',
            'preferencesModalFooterText',
        ];

        foreach ($data as $key) {
            $value = Craft::t('cookie-banner', $key, [], $site->language);

            $content = new ContentRecord();
            $content->siteId = $siteId;
            $content->key = $key;
            $content->value = ($value === $key) ? '' : $value;
            $content->save();
        }
    }

    public function removeDataForSite($siteId): void
    {
        Craft::$app->getDb()->createCommand()
            ->delete(ContentRecord::tableName(), ['siteId' => $siteId])
            ->execute();
    }
}
