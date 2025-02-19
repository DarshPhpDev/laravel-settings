<?php

namespace DarshPhpDev\LaravelSettings\Storage;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use DB;

class DatabaseStorage implements StorageInterface
{
    protected $table;

    public function __construct()
    {
        $this->table = config('settings.database.table', 'settings');
    }

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

    public function get(string $key, $default = null): string
    {
        $value = $this->all()[$key] ?? $default;
        // Check if the value is JSON before decoding
        if ($this->isJson($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

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

    public function forget(string $key): void
    {
        DB::table($this->table)
        ->where('key', $key)
        ->delete();
    }

    public function clear(): void
    {
        DB::table($this->table)
        ->truncate();
    }

    public function has(string $key): bool
    {
        return DB::table($this->table)
        		->where('key', $key)
        		->exitst();
    }

    private function isJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

}