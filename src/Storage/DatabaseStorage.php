<?php

namespace DarshPhpDev\LaravelSettings\Storage;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use DB;

/**
 * Database storage implementation for Laravel Settings.
 * Stores settings in a database table with key-value pairs.
 */
class DatabaseStorage implements StorageInterface
{
    /** @var string Name of the database table for storing settings */
    protected $table;

    /**
     * Initialize database storage with configured table name.
     * Default table name is 'settings'.
     */
    public function __construct()
    {
        $this->table = config('settings.database.table', 'settings');
    }

    /**
     * Retrieve all settings from the database.
     * Automatically decodes JSON values.
     *
     * @return array Array of all settings
     */
    public function all(): array
    {
        $data = DB::table($this->table)
            ->pluck('value', 'key')
            ->toArray();
        foreach ($data as $key => $value) {
            if ($this->isJson($value)) {
                $data[$key] = json_decode($value, true);
            }
        }

        return $data;
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
        $value = $this->all()[$key] ?? $default;
        // Check if the value is JSON before decoding
        if ($this->isJson($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * Store a setting value.
     * Automatically encodes arrays as JSON.
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     */
    public function set(string $key, $value): void
    {
        if (is_array($value)) {
            $value = json_encode($value); // Convert array to JSON string
        }

        DB::table($this->table)
            ->updateOrInsert(
                ['key' => $key],
                ['value' => $value]
            );
    }

    /**
     * Remove a specific setting.
     *
     * @param string $key Setting key to remove
     */
    public function forget(string $key): void
    {
        DB::table($this->table)
            ->where('key', $key)
            ->delete();
    }

    /**
     * Remove all settings by truncating the table.
     */
    public function clear(): void
    {
        DB::table($this->table)
            ->truncate();
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key Setting key to check
     * @return bool Whether the setting exists
     */
    public function has(string $key): bool
    {
        return DB::table($this->table)
                ->where('key', $key)
                ->exists();
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param mixed $value Value to check
     * @return bool Whether the value is valid JSON
     */
    private function isJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
}