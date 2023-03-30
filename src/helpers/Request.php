<?php

namespace dwy\CookieConsentManager\helpers;

use Craft;
use craft\models\Site;

class Request
{
    /**
     * Returns all request parameters except for the ones that are used by Craft.
     *
     * @return array
     */
    public static function getAllParams(): array
    {
        $request = Craft::$app->request;

        $keysToFilter = [
            'action',
            'redirect',
            Craft::$app->getConfig()->getGeneral()->csrfTokenName,
        ];

        $params = $request->getBodyParams();

        return array_filter($params, function($key) use ($keysToFilter) {
            return !in_array($key, $keysToFilter);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Returns the current site as stated in the url.
     *
     * @return Site
     */
    public static function getSite(): Site
    {
        $siteHandle = Craft::$app->request->getParam('site') ?? Craft::$app->getSites()->currentSite->handle;

        return Craft::$app->getSites()->getSiteByHandle($siteHandle);
    }
}
