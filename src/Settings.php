<?php

namespace DarshPhpDev\LaravelSettings;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Settings
{
    protected $storage;
    protected $encrypt;
    protected $arrayFormat;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->encrypt = config('settings.encrypt', false);
        $this->arrayFormat = config('settings.array_format', 'json');
    }

    public function all(): array
    {
        return Cache::remember('laravel-settings', 3600, function () {
            $data = $this->storage->all();
            return $this->decryptData($data);
        });
    }

    public function get(string $key, $default = null)
    {
        $value = $this->all()[$key] ?? $default;
        $value = $this->maybeDecrypt($value);
        if (is_array($value)){
            $value = $this->maybeDecodeArray($value);
        }
        return $value;
    }

    public function set(string $key, $value): void
    {
        $value = $this->maybeEncrypt($value);
        $this->storage->set($key, $value);
        Cache::forget('laravel-settings');
    }

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

    public function forget(string $key): void
    {
        $this->storage->forget($key);
        Cache::forget('laravel-settings');
    }

    public function clear(): void
    {
        $this->storage->clear();
        Cache::forget('laravel-settings');
    }

    public function has(string $key): bool
    {
        return $this->storage->has($key);
    }

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

    private function decryptData(array $data): array
    {
        return array_map(function ($value) {
            return $this->maybeDecrypt($value);
        }, $data);
    }
}