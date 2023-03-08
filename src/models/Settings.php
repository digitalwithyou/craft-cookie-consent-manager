<?php

namespace dwy\CookieConsentManager\models;

use craft\base\Model;

class Settings extends Model
{
    // Name of the plugin in navigation
    public $name = 'Cookies';

    // Number of milliseconds before showing the consent-modal
    public $delay = 0;

    // Accepted values:
    // - opt-in: scripts will not run unless consent is given (gdpr compliant)
    // - opt-out: scripts — that have categories set as enabled by default — will run without consent, until an explicit choice is made
    public $mode = 'opt-in';

    // Number of days before the cookie expires (182 days = 6 months)
    public $cookieExpiration = 182;

    // Specify if you want to set a different number of days - before the cookie expires - when the user accepts only the necessary categories
    public $cookieNecessaryOnlyExpiration = null;

    // SameSite attribute
    public $cookieSameSite = 'Lax';

    // Enable if you want the value of the cookie to be rfc compliant
    public $useRfcCookie = false;

    // Enable if you want to block page navigation until user action
    public $forceConsent = false;

    // Enable if you want to automatically delete cookies when user opts-out of a specific category inside cookie settings
    public $autoclearCookies = false;

    // Enable if you want to easily manage existing <script> tags.
    public $pageScripts = false;

    // Enable if you want to remove the html cookie tables (but still want to make use of autoclear_cookies)
    public $removeCookieTables = false;

    // Disable if you want the plugin to run when a bot/crawler/webdriver is detected
    public $hideFromBots = true;
}
