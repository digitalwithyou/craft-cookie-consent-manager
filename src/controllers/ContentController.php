<?php

namespace dwy\CookieConsentManager\controllers;

use Craft;
use craft\helpers\Json;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\helpers\Request as RequestHelpers;
use dwy\CookieConsentManager\services\Content as ContentService;
use yii\web\Response;

class ContentController extends BaseCpController
{
    protected ContentService $service;

    public function init(): void
    {
        parent::init();

        $this->requirePermission('cookie-consent-manager-manageContent');

        $this->service = Plugin::getInstance()->get('content');
    }

    public function actionIndex(): Response
    {
        $site = RequestHelpers::getSite();

        $content = $this->service->getAllFromDb($site->id);

        return $this->renderTemplate('cookie-consent-manager/content/_index', compact(
            'site',
            'content',
        ));
    }

    public function actionPreferences(): Response
    {
        $site = RequestHelpers::getSite();

        $content = $this->service->getAllFromDb($site->id);

        return $this->renderTemplate('cookie-consent-manager/content/_preferences', compact(
            'site',
            'content',
        ));
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $params = RequestHelpers::getAllParams();
        $site = RequestHelpers::getSite();

        if (!$this->service->save($params, $site->id)) {
            $this->setFailFlash(Craft::t('app', 'Couldn’t save content.'));
        }
        else {
            $this->setSuccessFlash(Craft::t('app', 'Content saved.'));
        }

        return $this->redirectToPostedUrl();
    }
}
