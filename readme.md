# Scriptoshi Settings

**[Scriptoshi](https://www.scriptoshi.com) Scripts**

`scriptoshi/settings` is a flexible and powerful settings manager for Laravel Scriptoshi applications. This package allows you to easily manage configuration settings with support for caching, grouping, and dynamic retrieval.

## Features

-   **Easily store and retrieve settings** in Scriptoshi Scripts.
-   **Cache settings** for optimized performance.
-   **Group settings** based on categories.
-   **Supports multiple formats** for values (including JSON and booleans).
-   **Simple facade-based API** for accessing settings.
-   **Laravel 11+ compatible**.

---

## Installation

You can install the package via Composer:

```bash
composer require scriptoshi/settings
```

---

## Configuration

Once installed, the package will automatically register the service provider.

You can publish the package's configuration file using the command:

```bash
php artisan vendor:publish --provider="Scriptoshi\Settings\SettingServiceProvider"
```

---

## Usage

You can use the `Setting` facade or the `Settings` class directly to interact with your settings.

### Retrieving Settings

#### Get a single setting:

```php
use Scriptoshi\Settings\Facades\Setting;

$value = Setting::get('setting_name');
```

#### Get a setting with a default value:

```php
$value = Setting::get('setting_name', 'default_value');
```

#### Get all settings:

```php
$settings = Setting::all();
```

#### Get settings grouped by their group name:

```php
$groupedSettings = Setting::group();
```

#### Get settings by group:

```php
$groupSettings = Setting::for('group_name');
```

### Storing Settings

#### Store or update a single setting:

```php
Setting::set('setting_name', 'value');
```

#### Store multiple settings at once:

```php
Setting::set([
    'setting_1' => 'value1',
    'setting_2' => 'value2',
]);
```

### Caching Settings

Settings are cached by default to reduce database queries. You can manually refresh the cache with:

```php
Setting::refresh();
```

---

## Facade

The package includes a `Setting` facade for easy usage. The following methods are available:

-   `Setting::get($key, $default = null, $fresh = false)` – Retrieve a setting.
-   `Setting::set($key, $value)` – Store or update a setting.
-   `Setting::all($fresh = false)` – Retrieve all settings.
-   `Setting::group($fresh = false)` – Retrieve settings grouped by their category.
-   `Setting::for($group)` – Retrieve settings for a specific group.
-   `Setting::json($group)` – Retrieve settings for a specific group as JSON.
-   `Setting::has($key)` – Check if a setting exists.
-   `Setting::remove($key)` – Remove a setting.
-   `Setting::refresh()` – Refresh the cached settings.

---

## Testing

### Unit Tests

Run the unit tests with PHPUnit:

```bash
vendor/bin/phpunit
```

The package includes unit and feature tests to ensure everything is working as expected.

---

## Development

1. **Clone the repository**:

    ```bash
    git clone https://github.com/scriptoshi/settings.git
    ```

2. **Install dependencies**:

    ```bash
    composer install
    ```

3. **Run tests**:
    ```bash
    vendor/bin/phpunit
    ```

---

## License

This package is licensed under the MIT License.
