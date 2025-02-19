<?php

return [
    'driver' => 'file',
    'encrypt' => false,
    'array_format' => 'json', // Options: json, csv, serialize
    
    'file' => [
        'path' => storage_path('app/settings.json'),
    ],
    
    'database' => [
        'table' => 'settings',
    ],

    'cache' => [
        'key' => 'laravel-settings',
        'ttl' => 3600,
    ]
];