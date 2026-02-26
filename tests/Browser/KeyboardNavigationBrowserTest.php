<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('navigates forward with right arrow key', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/demo', 'demo');

    $page = visit('/slides/demo');

    $page->waitForText('Demo Intro')
        ->assertSee('Demo Intro')
        ->assertNoJavaScriptErrors();
})->group('browser');

it('renders a deck with fragments in browser', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/fragments', 'fragments');

    $page = visit('/slides/fragments');

    $page->waitForText('Fragment Slide')
        ->assertSee('Fragment Slide')
        ->assertNoJavaScriptErrors();
})->group('browser');

it('renders a deck with hash deep-link in browser', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/demo', 'demo');

    $page = visit('/slides/demo');

    $page->waitForText('Demo Intro')
        ->assertNoJavaScriptErrors();
})->group('browser');

it('renders fullscreen button in browser', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/fullscreen', 'fullscreen');

    $page = visit('/slides/fullscreen');

    $page->waitForText('Fullscreen Test')
        ->assertSee('Fullscreen Test')
        ->assertNoJavaScriptErrors();
})->group('browser');

it('renders precedence deck in browser', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/precedence', 'precedence');

    $page = visit('/slides/precedence');

    $page->waitForText('Inherits Deck Defaults')
        ->assertSee('Inherits Deck Defaults')
        ->assertNoJavaScriptErrors();
})->group('browser');
