<?php

namespace dwy\CookieConsentManager\records;

use craft\db\ActiveRecord;

class Content extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%ccm_content}}';
    }
}
