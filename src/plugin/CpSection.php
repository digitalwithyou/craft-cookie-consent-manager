<?php

namespace dwy\CookieConsentManager\plugin;

use Craft;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use yii\base\Event;

trait CpSection
{
    private function _registerCpSection()
    {
        $this->hasCpSection = true;
    }

    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();

        $item['label'] = $this->getSettings()->name;

        // if (Craft::$app->getUser()->checkPermission('cookie-consent-manager-manageCategories')) {
        //     $item['subnav']['categories'] = ['label' => 'Categories', 'url' => 'cookie-consent-manager/categories'];
        // }

        // if (Craft::$app->getUser()->getIsAdmin() && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
        //     $item['subnav']['settings'] = ['label' => 'Settings', 'url' => 'cookie-consent-manager/settings'];
        // }

        return $item;
    }
}
