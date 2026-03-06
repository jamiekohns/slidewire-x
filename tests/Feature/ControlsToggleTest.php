<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('hides controls when show_controls is false at deck level', function (): void {
    Route::slidewire('/slides/no-controls', 'no-controls');

    test()->get('/slides/no-controls')
        ->assertSuccessful()
        ->assertSee('No Controls')
        ->assertDontSee('aria-label="Slide controls"');
});

it('hides progress bar when show_progress is false at deck level', function (): void {
    Route::slidewire('/slides/no-controls', 'no-controls');

    test()->get('/slides/no-controls')
        ->assertSuccessful()
        ->assertDontSee('role="progressbar"');
});

it('hides fullscreen button when show_fullscreen_button is false at deck level', function (): void {
    Route::slidewire('/slides/no-controls', 'no-controls');

    test()->get('/slides/no-controls')
        ->assertSuccessful()
        ->assertDontSee('Enter fullscreen');
});

it('renders fragments markup in slides with fragments', function (): void {
    Route::slidewire('/slides/fragments', 'fragments');

    test()->get('/slides/fragments')
        ->assertSuccessful()
        ->assertSee('Fragment Slide')
        ->assertSee('Fragment Zero')
        ->assertSee('data-fragment');
});

it('renders fullscreen button when explicitly enabled', function (): void {
    Route::slidewire('/slides/fullscreen', 'fullscreen');

    test()->get('/slides/fullscreen')
        ->assertSuccessful()
        ->assertSee('Enter fullscreen')
        ->assertSee('<svg x-show="!isFullscreen" x-cloak', false)
        ->assertSee('<svg x-show="isFullscreen" x-cloak', false);
});
