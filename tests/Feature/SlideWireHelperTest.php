<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use WendellAdriel\SlideWire\SlideWire;

it('registers route via SlideWire::route() compatibility helper', function (): void {
    $route = SlideWire::route('/slides/helper-test', 'demo/helper');

    expect($route)->toBeInstanceOf(Illuminate\Routing\Route::class)
        ->and($route->getName())->toBe('slidewire.demo.helper')
        ->and($route->defaults['presentation'])->toBe('demo/helper');
});

it('registers slidewire route macro on Route facade', function (): void {
    expect(Route::hasMacro('slidewire'))->toBeTrue();
});

it('generates correct route name for nested presentations', function (): void {
    $route = Route::slidewire('/slides/deep/nested/path', 'deep/nested/path');

    expect($route->getName())->toBe('slidewire.deep.nested.path');
});
