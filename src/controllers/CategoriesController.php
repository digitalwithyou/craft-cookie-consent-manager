<?php

namespace dwy\CookieConsentManager\controllers;

use Craft;
use craft\helpers\Json;
use dwy\CookieConsentManager\Plugin;
use dwy\CookieConsentManager\helpers\Request as RequestHelpers;
use dwy\CookieConsentManager\services\Categories as CategoriesService;
use yii\web\Response;

class CategoriesController extends BaseCpController
{
    protected CategoriesService $service;

    public function init(): void
    {
        parent::init();

        $this->requirePermission('cookie-consent-manager-manageCategories');

        $this->service = Plugin::getInstance()->get('categories');
    }

    public function actionIndex(): Response
    {
        $site = RequestHelpers::getSite();

        $categories =  $this->service->getAllForSite($site->id);

        return $this->renderTemplate('cookie-consent-manager/categories/_index', compact(
            'site',
            'categories',
        ));
    }

    public function actionAdd(): Response
    {
        $site = RequestHelpers::getSite();

        $category =  $this->service->create();

        $enabledForSites =  $this->service->getEnabledForCategory();

        return $this->renderTemplate('cookie-consent-manager/categories/_edit', compact(
            'site',
            'enabledForSites',
            'category',
        ));
    }

    public function actionEdit(int $categoryId = null): Response
    {
        $site = RequestHelpers::getSite();

        $category =  $this->service->get($categoryId, $site->id);
        $enabledForSites =  $this->service->getEnabledForCategory($categoryId);

        return $this->renderTemplate('cookie-consent-manager/categories/_edit', compact(
            'site',
            'category',
            'enabledForSites',
        ));
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $params = RequestHelpers::getAllParams();
        $site = RequestHelpers::getSite();

        if (!$this->service->save($params, $site->id)) {
            return $this->asFailure(
                Craft::t('cookie-consent-manager', 'Couldn’t save category.'),
            );
        }

        return $this->asSuccess(
            Craft::t('cookie-consent-manager', 'Category saved.'),
        );
    }

    public function actionArchive(): ?Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $categoryId = $this->request->getRequiredBodyParam('id');

        try {
            $this->service->archive($categoryId);
        } catch (Exception $exception) {
            return $this->asFailure($exception->getMessage());
        }

        return $this->asSuccess();
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $ids = $this->request->getRequiredBodyParam('ids');
        $site = RequestHelpers::getSite();

        $ids = Json::decode($ids);

        if (!$this->service->reorder($ids, $site->id)) {
            return $this->asFailure(Craft::t('cookie-consent-manager', 'Couldn’t reorder categories.'));
        }

        return $this->asSuccess();
    }
}
