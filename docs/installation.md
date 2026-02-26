# Installation

## Requirements

- PHP `^8.4`
- Laravel `^12`
- Livewire `^4`

## Install Package

```bash
composer require wendelladriel/slidewire
```

## Publish Assets (Optional)

### Config

```bash
php artisan vendor:publish --tag=slidewire-config
```

Publishes:

- `config/slidewire.php`

### Views

```bash
php artisan vendor:publish --tag=slidewire-views
```

Publishes package views to:

- `resources/views/vendor/slidewire`

### Stubs

```bash
php artisan vendor:publish --tag=slidewire-stubs
```

Publishes stubs to:

- `stubs/slidewire`

## Verify Installation

```bash
php artisan make:slidewire demo/hello --title="Hello SlideWire"
```

Then register a route and open it:

```php
use Illuminate\Support\Facades\Route;

Route::slidewire('/slides/hello', 'demo/hello');
```

Open `/slides/hello`.
