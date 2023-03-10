<?php

namespace dwy\CookieConsentManager\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * General
     */

     // Name of the plugin in navigation
    public string $name = 'Cookies';

    // Accepted values:
    // - opt-in: scripts will not run unless consent is given (gdpr compliant)
    // - opt-out: scripts — that have categories set as enabled by default — will run without consent, until an explicit choice is made
    public string $mode = 'opt-in';

    // Number of milliseconds before showing the consent-modal
    public int $delay = 0;

    // Enable if you want to block page navigation until user action
    public bool $forceConsent = false;

    // Enable if you want to automatically delete cookies when user opts-out of a specific category inside cookie settings
    public bool $autoclearCookies = false;

    // Enable if you want to easily manage existing <script> tags.
    public bool $pageScripts = false;

    // Automatically add the cookie consent banner
    public bool $autorun = true;

    // Enable if you want to remove the html cookie tables (but still want to make use of autoclear_cookies)
    public bool $removeCookieTables = false;

    // Disable if you want the plugin to run when a bot/crawler/webdriver is detected
    public bool $hideFromBots = true;


    /**
     * Layout: Consent & Settings modal
     */

    // Layout of the consent modal (box, cloud, bar)
    public string $layout = 'box';

    // Position of the consent modal (bottom/middle/top + left/right/center)
    public string $position = 'bottom center';

    // Transition effect of the consent modal ('', slide, zoom)
    public string $transition = '';

    // Enable to invert buttons
    public bool $swapButtons = false;

    // Layout of the settings modal (box, bar)
    public string $settingsModalLayout = 'box';

    // Transition of the settings modal ('', slide, zoom)
    public string $settingsModalTransition = '';

    /**
     * Cookies
     */

     // Number of days before the cookie expires (182 days = 6 months)
    public int $cookieExpiration = 182;

    // Specify if you want to set a different number of days - before the cookie expires - when the user accepts only the necessary categories
    public ?int $cookieNecessaryOnlyExpiration = null;

    // SameSite attribute
    public string $cookieSameSite = 'Lax';

    // Enable if you want the value of the cookie to be rfc compliant
    public bool $useRfcCookie = false;
}
