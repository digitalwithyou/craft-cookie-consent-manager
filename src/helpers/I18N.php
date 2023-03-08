<?php

namespace dwy\CookieConsentManager\helpers;

use Craft;

class I18N
{
    /**
     * Returns whether a message is translated.
     *
     * @param string $message
     * @param string $category
     * @return bool
     */
    public static function isTranslated($message, $category = 'site'): bool
    {
        $translation = Craft::t($category, $message);

        return !empty($translation) && $translation !== $message;
    }
}
