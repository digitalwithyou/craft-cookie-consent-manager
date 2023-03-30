<?php

namespace dwy\CookieConsentManager\plugin;

use craft\events\SiteEvent;
use craft\services\Sites;
use yii\base\Event;

trait EventsSite
{
    public function _registerSiteEvents(): void
    {
        Event::on(
            Sites::class,
            Sites::EVENT_AFTER_SAVE_SITE,
            function (SiteEvent $event) {
                $this->content->insertDefaultDataForSite($event->site->id);
            }
        );

        Event::on(
            Sites::class,
            Sites::EVENT_AFTER_DELETE_SITE,
            function (DeleteSiteEvent $event) {
                $this->content->removeDataForSite($event->site->id);
            }
        );
    }
}
