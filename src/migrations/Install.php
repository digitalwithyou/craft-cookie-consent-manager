<?php

namespace dwy\CookieConsentManager\migrations;

use Craft;
use craft\db\Migration;
use craft\records\Site;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\models\Category;
use dwy\CookieConsentManager\models\CategorySite;
use dwy\CookieConsentManager\records\Category as CategoryRecord;
use dwy\CookieConsentManager\records\CategorySite as CategorySiteRecord;
use dwy\CookieConsentManager\records\Content as ContentRecord;

class Install extends Migration
{
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();
        $this->insertDefaultData();

        return true;
    }

    public function safeDown()
    {
        $this->dropTables();

        return true;
    }

    protected function createTables()
    {
        $this->createTable(CategoryRecord::tableName(), [
            'id' => $this->primaryKey(),
            'required' => $this->boolean()->notNull()->defaultValue(false),
            'default' => $this->boolean()->notNull()->defaultValue(false),
            'dateDeleted' => $this->dateTime(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable(CategorySiteRecord::tableName(), [
            'categoryId' => $this->integer()->notNull(),
            'siteId' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'handle' => $this->string()->notNull(),
            'enabled' => $this->boolean()->notNull()->defaultValue(true),
            'description' => $this->text(),
            'sortOrder' => $this->integer(),
        ]);

        $this->createTable(ContentRecord::tableName(), [
            'id' => $this->primaryKey(),
            'siteId' => $this->integer()->notNull(),
            'key' => $this->string()->notNull(),
            'value' => $this->text(),
        ]);
    }

    protected function dropTables()
    {
        $this->dropTable(CategorySiteRecord::tableName());
        $this->dropTable(CategoryRecord::tableName());
        $this->dropTable(ContentRecord::tableName());
    }

    protected function createIndexes()
    {
        $this->addPrimaryKey(null, CategorySiteRecord::tableName(), ['categoryId', 'siteId']);
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(null, CategorySiteRecord::tableName(), ['categoryId'], CategoryRecord::tableName(), ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, CategorySiteRecord::tableName(), ['siteId'], Site::tableName(), ['id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey(null, ContentRecord::tableName(), ['siteId'], Site::tableName(), ['id'], 'CASCADE', 'CASCADE');
    }

    protected function insertDefaultData()
    {
        $sites = Craft::$app->getSites()->getAllSites();

        $this->insertDefaultCategoryData($sites);
        $this->insertDefaultContentData($sites);
    }

    protected function insertDefaultCategoryData($sites)
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Essential',
                'handle' => 'essential',
                'enabled' => true,
                'description' => '',
                'required' => true,
                'default' => true,
                'sortOrder' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Analytical',
                'handle' => 'analytical',
                'enabled' => false,
                'description' => '',
                'required' => false,
                'default' => false,
                'sortOrder' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Marketing',
                'handle' => 'marketing',
                'enabled' => false,
                'description' => '',
                'required' => false,
                'default' => false,
                'sortOrder' => 3,
            ],
        ];

        foreach($categories as $category) {
            $this->insert(CategoryRecord::tableName(), [
                'id' => $category['id'],
                'handle' => $category['handle'],
                'required' => $category['required'],
                'default' => $category['default'],
            ]);

            foreach($sites as $site) {
                $this->insert(CategorySiteRecord::tableName(), [
                    'categoryId' => $category['id'],
                    'siteId' => $site->id,
                    'name' => $category['name'],
                    'enabled' => $category['enabled'],
                    'description' => $category['description'],
                    'sortOrder' => $category['sortOrder'],
                ]);
            }
        }
    }

    protected function insertDefaultContentData($sites)
    {
        $contentService = Plugin::getInstance()->content;

        foreach($sites as $site) {
            $contentService->insertDefaultDataForSite($site->id);
        }
    }
}
