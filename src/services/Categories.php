<?php

namespace dwy\CookieConsentManager\services;

use Craft;
use craft\base\Model;
use craft\db\ActiveRecord;
use craft\db\Query;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use DateTime;
use dwy\CookieConsentManager\models\Category;
use dwy\CookieConsentManager\records\Category as CategoryRecord;
use dwy\CookieConsentManager\records\CategorySite as CategorySiteRecord;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Categories
{
    protected ?array $_items = null;

    // public const EVENT_BEFORE_SAVE = 'beforeSaveCategory';
    // public const EVENT_AFTER_SAVE = 'afterSaveCategory';
    // public const EVENT_BEFORE_ARCHIVE = 'beforeArchiveCategory';
    // public const EVENT_AFTER_ARCHIVE = 'afterArchiveCategory';

    public function getAllForSite($siteId, $enabled = null, $includeDeleted = false): array
    {
        $filter = [
            'siteId' => $siteId,
        ];

        if ($enabled !== null) {
            $filter['enabled'] = $enabled;
        }

        if (!$includeDeleted) {
            $filter['dateDeleted'] = null;
        }

        $array = ArrayHelper::whereMultiple($this->_getAll(), $filter);

        return $this->populateAll($array);
    }

    public function getEnabledForCategory(int $categoryId = null): array
    {
        $results = $categoryId ? ArrayHelper::whereMultiple($this->_getAll(), [
            'categoryId' => $categoryId,
            'dataDeleted' => null,
        ]) : [];

        $sites = Craft::$app->getSites()->getAllSites();

        $data = [];

        foreach ($sites as $site) {
            $result = ArrayHelper::firstWhere($results, 'siteId', $site->id);

            $data[] = [
                'siteId' => $site->id,
                'name' => $site->name,
                'enabled' => $result ? $result['enabled'] : false,
            ];
        }

        return $data;
    }

    public function get(int $id, int $siteId): ?Category
    {
        $results = ArrayHelper::whereMultiple($this->_getAll(), [
            'id' => $id,
            'siteId' => $siteId,
        ]);

        $firstResult = ArrayHelper::firstValue($results);

        return $firstResult ? $this->populate($firstResult) : null;
    }

    public function reorder(array $ids, $siteId): bool
    {
        $categorySiteTable = CategorySiteRecord::tableName();

        foreach ($ids as $sortOrder => $id) {
            Craft::$app->getDb()->createCommand()
                ->update(
                    $categorySiteTable,
                    [
                        'sortOrder' => $sortOrder + 1,
                    ],
                    [
                        'categoryId' => $id,
                        'siteId' => $siteId,
                    ]
                )
                ->execute();
        }

        return true;
    }

    public function save($params, $siteId): bool
    {
        $isNew = empty($params['id']);

        // category record
        $categoryRecord = ($isNew)
            ? new CategoryRecord()
            : CategoryRecord::findOne($params['id']);

        $categoryRecord->required = !empty($params['required']);
        $categoryRecord->default = !empty($params['default']);

        if (!$categoryRecord->validate()) {
            return false;
        }

        $categoryRecord->save();

        // category site record
        $primaryKey = [
            'categoryId' => $categoryRecord->id,
            'siteId' => $siteId,
        ];

        $categorySiteRecord = ($isNew)
            ? new CategorySiteRecord($primaryKey)
            : CategorySiteRecord::findOne($primaryKey);

        $categorySiteRecord->name = $params['name'];
        $categorySiteRecord->description = $params['description'];
        $categorySiteRecord->enabled = !empty($params['enabled'][$siteId]);

        if (!$categorySiteRecord->validate()) {
            return false;
        }

        $categorySiteRecord->save();

        // Save "enabled" for the other sites
        unset($params['enabled'][$siteId]);

        foreach ($params['enabled'] as $siteId => $enabled) {
            $primaryKey = [
                'categoryId' => $categoryRecord->id,
                'siteId' => $siteId,
            ];

            $categorySiteRecord = ($isNew)
                ? new CategorySiteRecord($primaryKey)
                : CategorySiteRecord::findOne($primaryKey);

            $categorySiteRecord->enabled = !empty($enabled);

            if ($categorySiteRecord->name === null) {
                $categorySiteRecord->name = $params['name'];
            }

            $categorySiteRecord->save();
        }

        return true;
    }

    public function archive(int $id): bool
    {
        $categoryTable = CategoryRecord::tableName();

        $now = Db::prepareDateForDb(new DateTime());

        Craft::$app->getDb()->createCommand()
            ->update($categoryTable, ['dateDeleted' => $now], ['id' => $id])
            ->execute();

        return true;
    }

    protected function _createQuery($siteId = null): Query
    {
        $categoryTable = CategoryRecord::tableName();
        $categorySiteTable = CategorySiteRecord::tableName();

        $query = (new Query())
            ->from($categoryTable)
            ->leftJoin($categorySiteTable, $categorySiteTable . '.categoryId = ' . $categoryTable . '.id')
            ->orderBy([$categorySiteTable . '.sortOrder' => SORT_ASC]);

        if ($siteId) {
            $query->where([$categorySiteTable . '.siteId' => $siteId]);
        }

        return $query;
    }

    protected function _getAll(): array
    {
        if ($this->_items === null) {
            $this->_items = $this->_createQuery()->all();
        }

        return $this->_items;
    }

    public function populate(array $result): Category
    {
        $category = new Category();
        $category->setAttributes($result, false);

        return $category;
    }

    public function populateAll(array $results): array
    {
        $categories = [];

        foreach ($results as $result) {
            $categories[] = $this->populate($result);
        }

        return $categories;
    }

    public function create(): category
    {
        return new category();
    }
}
