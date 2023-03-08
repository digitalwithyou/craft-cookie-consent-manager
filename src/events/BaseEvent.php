<?php

namespace dwy\CookieConsentManager\events;

use craft\base\Model;

use yii\base\Event;

class BaseEvent extends Event
{
    // Properties
    // =========================================================================

    public Model $model;
    public bool $isNew = false;
}
