<?php

namespace dwy\CookieConsentManager\records;

use craft\db\ActiveRecord;
use craft\records\Site;

class CategorySite extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%ccm_categories_sites}}';
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'categoryId']);
    }

    public function getSite()
    {
        return $this->hasOne(Site::class, ['id' => 'siteId']);
    }
}
