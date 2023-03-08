<?php

namespace dwy\CookieConsentManager\records;

use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;

class Category extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName(): string
    {
        return '{{%ccm_categories}}';
    }

    public function getSitesData()
    {
        return $this->hasMany(CategorySite::class, ['categoryId' => 'id']);
    }
}
