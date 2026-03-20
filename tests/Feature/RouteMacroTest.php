<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Tests\Fixtures\FakeDatabaseDocumentProvider;

it('registers a slidewire route macro', function (): void {
    $route = Route::slidewire('/slides/demo', 'demo/showcase');

    expect($route->getName())->toBe('slidewire.demo.showcase')
        ->and($route->defaults['presentation'])->toBe('demo/showcase');
});

it('registers a database route macro using a provider class', function (): void {
    $route = Route::slidewire('/presentations', FakeDatabaseDocumentProvider::class);

    expect($route->uri())->toBe('presentations/{document}')
        ->and($route->getName())->toBe('slidewire.database.presentations')
        ->and($route->defaults['documentSource'])->toBe('database')
        ->and($route->defaults['documentProvider'])->toBe(FakeDatabaseDocumentProvider::class);
});
