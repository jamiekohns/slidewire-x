# Quickstart

## 1) Generate a Presentation

```bash
php artisan make:slidewire demo/product-launch --title="Product Launch"
```

This creates:

- `resources/views/pages/slides/demo/product-launch.blade.php`

## 2) Add Slides In The Same File

```blade
<x-slidewire::deck>
    <x-slidewire::slide class="bg-slate-900 text-white">
        <flux:heading size="xl">Product Launch</flux:heading>

        <x-slidewire::fragment :index="0">
            <flux:text>Private beta</flux:text>
        </x-slidewire::fragment>

        <x-slidewire::fragment :index="1">
            <flux:text>Pilot customers</flux:text>
        </x-slidewire::fragment>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-white text-slate-900" transition="fade">
        <x-slidewire::markdown>
## Launch Metrics

- Activation: 62%
- Churn: 1.8%
        </x-slidewire::markdown>
    </x-slidewire::slide>
</x-slidewire::deck>
```

## 3) Register Route

```php
use Illuminate\Support\Facades\Route;

Route::slidewire('/slides/product-launch', 'demo/product-launch');
```

## 4) Open and Present

Open `/slides/product-launch`.
