<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('navigates a presentation in the browser', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/demo', 'demo');

    $page = visit('/slides/demo');

    // Verify deck renders with initial slide and controls
    $page->waitForText('Demo Intro')
        ->assertSee('Demo Intro')
        ->assertNoJavaScriptErrors();
})->group('browser');

it('renders a vertical-slide presentation in the browser', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/vertical', 'vertical');

    $page = visit('/slides/vertical');

    $page->waitForText('Horizontal Slide 1')
        ->assertSee('Horizontal Slide 1')
        ->assertNoJavaScriptErrors();
})->group('browser');
