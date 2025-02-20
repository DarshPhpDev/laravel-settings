<p align="center"><img src="/art/socialcard.png" alt="Laravel Settings"></p>

# Laravel Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darshphpdev/laravel-settings?style=flat-square)](https://packagist.org/packages/darshphpdev/laravel-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/darshphpdev/laravel-settings?style=flat-square)](https://packagist.org/packages/darshphpdev/laravel-settings)
[![License](https://img.shields.io/badge/license-MIT-brightgreen)](LICENSE)

A flexible Laravel package for managing application settings with support for file or database storage, encryption, and array format customization.

## ✨ Features

- 💾 Multiple storage drivers (file or database)
- 🔐 Value encryption support
- 📦 Configurable array storage formats (JSON, CSV, or serialized)
- 🛠️ Simple helper function for easy access
- ⚡ Laravel artisan command for easy installation
- 🚀 Cache support for better performance

## 📋 Requirements

- 🐘 PHP 7.4|8.0
- ⚡ Laravel 7.0|8.0|9.0|10.0|11.0

## 📥 Installation

You can install the package via composer:

```bash
composer require darshphpdev/laravel-settings
```

## 🔧 Setup

1. Run the installation command:

```bash
php artisan settings:install
```

<p align="center"><img src="/art/artisan.png" alt="Artisan Command"></p>

This command will:
- 🎯 Guide you through configuration options
- 📝 Create the config file
- 🔄 Publish migrations (if using database driver)

2. If using database driver, run migrations:

```bash
php artisan migrate
```

## ⚙️ Configuration

The package configuration will be published to `config/settings.php`. Here are the available options:

```php
return [
    // Storage driver: 'file' or 'database'
    'driver' => 'file',

    // Enable encryption for stored values
    'encrypt' => false,

    // Format for storing arrays: 'json', 'csv', 'serialize'
    'array_format' => 'json',

    // File storage settings
    'file' => [
        'path' => storage_path('app/settings.json'),
    ],

    // Database storage settings
    'database' => [
        'table' => 'settings',
    ],

    // Cache configurations
    'cache' => [
        'key' => 'laravel-settings',
        'ttl' => 3600,
        'enabled' => true,
    ]
];
```

## 📖 Usage

### 🔨 Using Helper Function

```php
// Get a setting
settings()->get('site_name', 'Default site name');

// Set a setting
settings()->set('site_name', 'My Awesome Site');

// Set multiple settings
settings()->set('site_meta', [
    'name' => 'My Awesome Site',
    'description' => 'A great site'
]);

// Get all settings
$settings = settings()->all();

// Check if a setting exists
settings()->has('site_name');

// Remove a setting
settings()->forget('site_name');

// Clear all settings
settings()->clear();
```

### 📦 Working with Arrays

The package provides three formats for storing arrays:

1. **JSON** (default) 📄:
```php
settings()->set('my_array', ['one', 'two']); // Stored as JSON
```

2. **CSV** 📑:
```php
settings()->set('my_array', ['one', 'two']); // Stored as "one,two"
```

3. **Serialized** 📎:
```php
settings()->set('my_array', ['one', 'two']); // Stored using PHP serialization
```

### 🔐 Encryption

When encryption is enabled in the config, all values are automatically encrypted before storage and decrypted when retrieved:
```php
// With encryption enabled
settings()->set('secret_key', 'sensitive-value'); // Stored encrypted
```

## 🛡️ Security

If you discover any security-related issues, please email mustafa.softcode@gmail.com instead of using the issue tracker.

## 👨‍💻 Credits
- [Mustafa Ahmed](https://github.com/darshphpdev)

## 📄 License

This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
