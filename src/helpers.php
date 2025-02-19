<?php

use DarshPhpDev\LaravelSettings\Settings;

if (!function_exists('settings')) {
    /**
     * Global helper function to get or set settings.
     * Provides a convenient way to interact with the Settings facade.
     * 
     * Usage:
     * - settings()                    Returns Settings instance
     * - settings('key')              Gets a setting value
     * - settings('key', 'value')     Sets a setting value
     *
     * @param string|array|null $key Setting key or array of key-value pairs
     * @param mixed $value Value to set (optional)
     * @return mixed Settings instance, setting value, or null
     */
    function settings($key = null, $value = null)
    {
        $settings = app(Settings::class);

        // Return Settings instance if no arguments
        if (func_num_args() === 0) {
            return $settings;
        }

        // Handle array of settings
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $settings->set($k, $v);
            }
            return null;
        }

        // Get setting value if no value provided
        if (is_null($value)) {
            return $settings->get($key);
        }

        // Set single setting value
        $settings->set($key, $value);
        return null;
    }
}