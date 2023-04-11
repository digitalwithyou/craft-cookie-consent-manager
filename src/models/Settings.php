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

    // Where to manage content
    public bool $manageContentAsTranslations = false;

    // Root (parent) element where the modal will be appended as a last child.
    // If null, the plugin will try to find the <body> tag.
    public ?string $root = null;

    // Accepted values:
    // - opt-in: scripts will not run unless consent is given (gdpr compliant)
    // - opt-out: scripts — that have categories set as enabled by default — will run without consent, until an explicit choice is made
    public string $mode = 'opt-in';

    // Automatically show the consent modal if consent is not valid.
    public bool $autoShow = true;

    // Intercepts all <script> tags with a data-category attribute, and enables them based on the accepted categories. Check out the scripts management section for details and examples.
    public bool $manageScriptTags = false;

    // Clears cookies when user rejects a specific category. It requires a valid autoClear array.
    public bool $autoClearCookies = true;

    // Stops the plugin's execution when a bot/crawler is detected, to prevent them from indexing the modal's content.
    public bool $hideFromBots = true;

    // Creates a dark overlay and blocks the page scroll until consent is expressed.
    public bool $disablePageInteraction = false;

    // Delays the generation of the modal's markup until they're about to become visible, to improve the TTI score. You can detect when a modal is ready/created via the onModalReady callback.
    public bool $lazyHtmlGeneration = true;


    /**
     * Cookie
     */

    // Name of the cookie
    public string $cookieName = 'ccm_cookie';

    // Restrict cookie to domain. retrieved automatically if null.
    public ?string $cookieDomain = null;

    // Restrict cookie to path
    public string $cookiePath = '/';

    // Number of days before the cookie expires (182 days = 6 months)
    public int $cookieExpiration = 182;

    // SameSite attribute
    public string $cookieSameSite = 'Lax';


    /**
     * Layout: Consent & Settings modal
     */

    // Layout of the consent modal (box wide, box inline, cloud, bar)
    public string $layout = 'box inline';

    // Position of the consent modal (bottom/middle/top + left/right/center)
    public string $position = 'bottom left';

    // Enable to invert buttons order
    public bool $flipButtons = false;

    // Equal weight buttons in consent modal
    public bool $equalWeightButtons = false;

    // Show preferences button in consent modal
    public bool $showPreferencesButton = true;

    // Layout of the preferences modal (box, bar wide)
    public string $preferencesModalLayout = 'box';

    // Position of the preferences modal (left / right), only for bar wide layout
    public string $preferencesModalPosition = '';

    // Flip buttons in preferences modal
    public bool $preferencesModalFlipButtons = false;

    // Equal weight buttons in preferences modal
    public bool $preferencesModalEqualWeightButtons = false;

}
