<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('advances slides automatically when auto-slide is configured', function (): void {
    if (! class_exists(Pest\Browser\Plugin::class)) {
        test()->markTestSkipped('Browser plugin is not installed in this environment.');
    }

    config()->set('slidewire.presentation_roots', [__DIR__ . '/../fixtures/views/pages/slides']);

    Route::slidewire('/slides/autoslide', 'autoslide');

    $page = visit('/slides/autoslide');

    $page->waitForText('Auto Slide Start')
        ->waitForText('Auto Slide Reached')
        ->assertNoJavaScriptErrors();
})->group('browser');
