<?php

namespace DarshPhpDev\LaravelSettings\Storage\Contracts;

/**
 * Interface for settings storage implementations.
 * Defines the contract that all storage drivers must implement.
 */
interface StorageInterface
{
    /**
     * Retrieve all settings from storage.
     *
     * @return array Array of all settings as key-value pairs
     */
    public function all(): array;

    /**
     * Get a specific setting value.
     *
     * @param string $key Setting key to retrieve
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed The setting value or default
     */
    public function get(string $key, $default = null);

    /**
     * Store a setting value.
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     */
    public function set(string $key, $value): void;

    /**
     * Remove a specific setting.
     *
     * @param string $key Setting key to remove
     */
    public function forget(string $key): void;

    /**
     * Remove all settings from storage.
     */
    public function clear(): void;

    /**
     * Check if a setting exists.
     *
     * @param string $key Setting key to check
     * @return bool Whether the setting exists
     */
    public function has(string $key): bool;
}