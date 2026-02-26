<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('registers a slidewire route macro', function (): void {
    $route = Route::slidewire('/slides/demo', 'demo/showcase');

    expect($route->getName())->toBe('slidewire.demo.showcase')
        ->and($route->defaults['presentation'])->toBe('demo/showcase');
});
