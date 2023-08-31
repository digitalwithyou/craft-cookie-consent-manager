<?php

namespace dwy\CookieConsentManager\models;

use craft\base\Model;
use craft\validators\DateTimeValidator;
use DateTime;

class Category extends Model
{
    public ?int $id = null;
    public ?int $siteId = null;
    public ?string $name = null;
    public ?string $handle = null;
    public ?string $description = null;
    public bool $enabled = true;
    public bool $required = false;
    public bool $default = false;
    public ?int $sortOrder = null;
    public ?DateTime $dateDeleted = null;
    public ?string $uid = null;

    /**
     * @var DateTime|null Date updated
     */
    public ?DateTime $dateUpdated = null;

    /**
     * @var DateTime|null Date created
     */
    public ?DateTime $dateCreated = null;

    protected function defineRules(): array
    {
        return [
            [['siteId', 'name', 'handle', 'uid'], 'required'],
            ['handle', 'unique'],
            [['dateUpdated', 'dateCreated'], DateTimeValidator::class],
        ];
    }
}
