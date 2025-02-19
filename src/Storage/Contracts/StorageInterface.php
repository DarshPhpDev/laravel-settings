<?php

namespace DarshPhpDev\LaravelSettings\Storage\Contracts;

interface StorageInterface
{
    public function all(): array;
    public function get(string $key, $default = null): string;
    public function set(string $key, $value): void;
    public function forget(string $key): void;
    public function clear(): void;
    public function has(string $key): bool;
}