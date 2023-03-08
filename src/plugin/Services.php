<?php

namespace dwy\CookieConsentManager\plugin;

use dwy\CookieConsentManager\services\Categories;
use dwy\CookieConsentManager\services\Renderer;

trait Services
{
    private function _registerServices(): void
    {
        $this->setComponents([
            'categories' => Categories::class,
            'renderer' => Renderer::class,
        ]);
    }
}
