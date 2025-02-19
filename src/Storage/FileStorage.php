<?php

namespace DarshPhpDev\LaravelSettings\Storage;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use Illuminate\Support\Facades\File;

/**
 * File-based storage implementation for Laravel Settings.
 * Stores settings in a JSON file on the filesystem.
 */
class FileStorage implements StorageInterface
{
    /** @var string Path to the JSON storage file */
    protected $path;

    /**
     * Initialize file storage with configured path.
     * Default path is 'storage/app/settings.json'.
     */
    public function __construct()
    {
        $this->path = config('settings.file.path', storage_path('app/settings.json'));
    }

    /**
     * Retrieve all settings from the JSON file.
     *
     * @return array Array of all settings, empty array if file doesn't exist
     */
    public function all(): array
    {
        if (!File::exists($this->path)) {
            return [];
        }
        return json_decode(File::get($this->path), true) ?? [];
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key Setting key to retrieve
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed The setting value or default
     */
    public function get(string $key, $default = null): string
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Store a setting value.
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     */
    public function set(string $key, $value): void
    {
        $settings = $this->all();
        $settings[$key] = $value;

        File::put($this->path, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * Remove a specific setting.
     *
     * @param string $key Setting key to remove
     */
    public function forget(string $key): void
    {
        $settings = $this->all();
        unset($settings[$key]);

        File::put($this->path, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * Remove all settings by clearing the file.
     */
    public function clear(): void
    {
        File::put($this->path, "");
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key Setting key to check
     * @return bool Whether the setting exists
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }
}