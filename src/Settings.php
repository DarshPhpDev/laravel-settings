<?php

namespace DarshPhpDev\LaravelSettings;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Settings management class for Laravel applications.
 * Handles storage, retrieval, and encryption of application settings.
 */
class Settings
{
    /** @var StorageInterface Storage implementation for settings */
    protected $storage;

    /** @var bool Whether to encrypt stored values */
    protected $encrypt;

    /** @var string Format for storing arrays (json/csv/serialize) */
    protected $arrayFormat;

    /** @var string Cache key for settings */
    protected $cacheKey;

    /** @var int Cache TTL for settings */
    protected $cacheTtl;

    /** @var bool Whether cache is enabled */
    protected $cacheEnabled;

    /**
     * Initialize settings manager with storage implementation.
     *
     * @param StorageInterface $storage Storage implementation
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->encrypt = config('settings.encrypt', false);
        $this->arrayFormat = config('settings.array_format', 'json');
        $this->cacheKey = config('settings.cache.key');
        $this->cacheTtl = config('settings.cache.ttl');
        $this->cacheEnabled = config('settings.cache.enabled');
    }

    /**
     * Retrieve all settings from storage.
     *
     * @return array All settings with decrypted values
     */
    public function all(): array
    {
        return $this->cacheEnabled
            ? Cache::remember($this->cacheKey, $this->cacheTtl, function () {
                $data = $this->storage->all();
                return $this->decryptData($data);
            })
            : $this->decryptData($this->storage->all());
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key Setting key
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed The setting value or default
     */
    public function get(string $key, $default = null)
    {
        $value = $this->all()[$key] ?? $default;
        $value = $this->maybeDecrypt($value);
        if (is_array($value)){
            $value = $this->maybeDecodeArray($value);
        }
        return $value;
    }

    /**
     * Store a setting value.
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     */
    public function set(string $key, $value): void
    {
        $value = $this->maybeEncrypt($value);
        $this->storage->set($key, $value);

        $this->maybeClearSettingsCache();
    }

    /**
     * Format array values based on configured array format.
     *
     * @param array $value Array to format
     * @return mixed Formatted array value
     */
    private function maybeDecodeArray(array $value)
    {
        switch ($this->arrayFormat) {
            case 'csv':
                return implode(',', $value);
                break;

            case 'serialize':
                return serialize($value);
                break;

            default:
                return $value;
                break;
        }
    }

    /**
     * Remove a specific setting.
     *
     * @param string $key Setting key to remove
     */
    public function forget(string $key): void
    {
        $this->storage->forget($key);

        $this->maybeClearSettingsCache();
    }

    /**
     * Remove all settings.
     */
    public function clear(): void
    {
        $this->storage->clear();

        $this->maybeClearSettingsCache();
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key Setting key to check
     * @return bool Whether the setting exists
     */
    public function has(string $key): bool
    {
        return $this->storage->has($key);
    }

    /**
     * Encrypt value if encryption is enabled.
     *
     * @param mixed $value Value to potentially encrypt
     * @return mixed Encrypted or original value
     */
    private function maybeEncrypt($value)
    {
        if(!$this->encrypt){
            return $value;
        }
        if(is_array($value)){
            return array_map(function ($val) {
                return Crypt::encryptString($val);
            }, $value);
        }
        return Crypt::encryptString($value);
    }

    /**
     * Decrypt value if encryption is enabled.
     *
     * @param mixed $value Value to potentially decrypt
     * @return mixed Decrypted or original value
     */
    private function maybeDecrypt($value)
    {
        try {
            if(!$this->encrypt){
                return $value;
            }
            if(is_array($value)){
                return array_map(function ($val) {
                    return Crypt::decryptString($val);
                }, $value);
            }
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Decrypt all values in an array of settings.
     *
     * @param array $data Array of settings to decrypt
     * @return array Decrypted settings
     */
    private function decryptData(array $data): array
    {
        return array_map(function ($value) {
            return $this->maybeDecrypt($value);
        }, $data);
    }

    /**
     * Clear settings cache if cache is enabled
     *
     * @param string $key Setting key to remove
     */
    private function maybeClearSettingsCache(): void
    {
        if ($this->cacheEnabled)
        {
            Cache::forget($this->cacheKey);
        }
    }
}