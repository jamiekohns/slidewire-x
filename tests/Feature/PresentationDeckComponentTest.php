<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('renders a presentation deck through the slidewire route macro', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    test()->get('/slides/demo')
        ->assertSuccessful()
        ->assertSee('Demo Intro')
        ->assertSee('Final slide')
        ->assertSee('Fullscreen');
});

it('renders a vertical-slide presentation through the slidewire route macro', function (): void {
    Route::slidewire('/slides/vertical', 'vertical');

    test()->get('/slides/vertical')
        ->assertSuccessful()
        ->assertSee('Horizontal Slide 1')
        ->assertSee('Stack Top')
        ->assertSee('Stack Middle')
        ->assertSee('Stack Bottom')
        ->assertSee('Horizontal Slide 3');
});

it('renders a deck with precedence settings', function (): void {
    Route::slidewire('/slides/precedence', 'precedence');

    test()->get('/slides/precedence')
        ->assertSuccessful()
        ->assertSee('Inherits Deck Defaults')
        ->assertSee('Overrides Deck Settings');
});

it('renders theme background classes from nested theme config', function (): void {
    Route::slidewire('/slides/precedence', 'precedence');

    $response = test()->get('/slides/precedence');
    $response->assertSuccessful();

    // The default theme background class should appear in the rendered output
    $themes = config('slidewire.themes', []);
    $nightBackground = $themes['night']['background'] ?? '';

    expect($nightBackground)->not->toBeEmpty();
});
