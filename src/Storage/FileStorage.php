<?php

namespace DarshPhpDev\LaravelSettings\Storage;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use Illuminate\Support\Facades\File;

class FileStorage implements StorageInterface
{
    protected $path;

    public function __construct()
    {
        $this->path = config('settings.file.path', storage_path('app/settings.json'));
    }

    public function all(): array
    {
        if (!File::exists($this->path)) {
            return [];
        }
        return json_decode(File::get($this->path), true) ?? [];
    }

    public function get(string $key, $default = null): string
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $settings = $this->all();
        $settings[$key] = $value;

        File::put($this->path, json_encode($settings, JSON_PRETTY_PRINT));
    }

    public function forget(string $key): void
    {
        $settings = $this->all();
        unset($settings[$key]);

        File::put($this->path, json_encode($settings, JSON_PRETTY_PRINT));
    }

    public function clear(): void
    {
        File::put($this->path, "");
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }
}