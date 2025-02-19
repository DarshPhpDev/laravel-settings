<?php

use DarshPhpDev\LaravelSettings\Settings;

if (!function_exists('settings')) {
    /**
     * Get or set a setting.
     *
     * @param string|array $key
     * @param mixed $value
     * @return mixed
     */
    function settings($key = null, $value = null)
    {
        $settings = app(Settings::class);

        if (func_num_args() === 0) {
            return $settings;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $settings->set($k, $v);
            }
            return null;
        }

        if (is_null($value)) {
            return $settings->get($key);
        }

        $settings->set($key, $value);
        return null;
    }
}