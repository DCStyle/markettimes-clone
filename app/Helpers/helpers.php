<?php

use App\Services\SettingsService;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        $settingsService = app(SettingsService::class);
        return $settingsService->get($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get the SettingsService instance
     *
     * @return SettingsService
     */
    function settings(): SettingsService
    {
        return app(SettingsService::class);
    }
}
