<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Tests\Fixtures\FakeDatabaseDocumentProvider;

it('registers slidewire route macro on Route facade', function (): void {
    expect(Route::hasMacro('slidewire'))->toBeTrue();
});

it('generates correct route name for nested presentations', function (): void {
    $route = Route::slidewire('/slides/deep/nested/path', 'deep/nested/path');

    expect($route->getName())->toBe('slidewire.deep.nested.path');
});

it('supports database route registration at the root prefix', function (): void {
    $route = Route::slidewire('/', FakeDatabaseDocumentProvider::class);

    expect($route->uri())->toBe('{document}')
        ->and($route->getName())->toBe('slidewire.database.root');
});
