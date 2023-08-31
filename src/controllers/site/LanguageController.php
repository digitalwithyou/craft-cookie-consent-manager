<?php

namespace dwy\CookieConsentManager\controllers\site;

use Craft;
use dwy\CookieConsentManager\Plugin;
use yii\web\Response;

class LanguageController extends BaseSiteController
{
    public function actionCurrent(): Response
    {
        $rendererService = Plugin::getInstance()->get('renderer');
        $site = Craft::$app->getSites()->getCurrentSite();

        $config = $rendererService->getLanguage($site->id);

        return $this->asJson($config);
    }
}
