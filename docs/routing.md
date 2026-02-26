# Routing

SlideWire registers a route macro: `Route::slidewire()`.

## Preferred Registration

```php
use Illuminate\Support\Facades\Route;

Route::slidewire('/slides/product-launch', 'demo/product-launch');
```

This macro:

- registers the full-page Livewire SFC route
- passes the presentation key through route defaults
- generates route names like `slidewire.demo.product-launch`

## Nested Presentations

Nested keys are supported:

- `sales/q1-launch`
- `engineering/retro/sprint-42`

## Hash Deep-Linking

SlideWire updates browser hash while navigating:

- `#/slide/1`
- `#/slide/5`
