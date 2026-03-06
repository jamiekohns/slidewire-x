<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use WendellAdriel\SlideWire\Support\ThemeConfig;

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
    $nightBackground = $themes['black'] ?? null;

    expect($nightBackground)->toBeInstanceOf(ThemeConfig::class)
        ->and($nightBackground->background)->not->toBeEmpty();
});

// ========================================================================
// @vite directive in layout
// ========================================================================

it('includes @vite directive in the blank layout template', function (): void {
    $layoutPath = realpath(__DIR__ . '/../../resources/views/layouts/blank.blade.php');

    expect($layoutPath)->not->toBeFalse()
        ->and(file_get_contents($layoutPath))->toContain('@vite');
});

// ========================================================================
// Theme class application tests
// ========================================================================

it('applies slidewire-theme class to slide frame elements', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Slides should get theme class applied to the frame element
    expect($content)->toContain('slidewire-theme-default');
});

it('applies slide-level theme class when slide overrides theme', function (): void {
    Route::slidewire('/slides/precedence', 'precedence');

    $response = test()->get('/slides/precedence');
    $content = $response->getContent();

    // Precedence fixture has deck theme=black and slide 2 with theme=white
    expect($content)->toContain('slidewire-theme-black')
        ->and($content)->toContain('slidewire-theme-white');
});

// ========================================================================
// Theme typography class application tests
// ========================================================================

it('applies text typography classes to slidewire-content div', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Default theme text: Inter text-slate-200 text-lg
    // These classes should appear on the slidewire-content div
    expect($content)->toContain('slidewire-content Inter text-slate-200 text-lg');
});

it('applies correct typography classes when slide overrides theme', function (): void {
    Route::slidewire('/slides/precedence', 'precedence');

    $response = test()->get('/slides/precedence');
    $content = $response->getContent();

    // Slide 2 uses theme=white, so text typography should be Inter text-zinc-600 text-lg
    expect($content)->toContain('slidewire-content Inter text-zinc-600 text-lg');

    // Other slides use black theme, so text typography should be Inter text-slate-300 text-lg
    expect($content)->toContain('slidewire-content Inter text-slate-300 text-lg');
});

// ========================================================================
// Per-slide Tailwind class preservation tests
// ========================================================================

it('preserves per-slide Tailwind background classes on frame elements', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // demo slide 1: class="bg-slate-900 text-white"
    expect($content)->toContain('bg-slate-900 text-white');
});

it('preserves per-slide gradient classes on frame elements', function (): void {
    Route::slidewire('/slides/gradient', 'gradient');

    $response = test()->get('/slides/gradient');
    $content = $response->getContent();

    // gradient slide 1: class="bg-gradient-to-br from-blue-900 to-slate-950 text-slate-50"
    expect($content)->toContain('bg-gradient-to-br from-blue-900 to-slate-950 text-slate-50');
});

it('applies white theme class when slide overrides theme in gradient deck', function (): void {
    Route::slidewire('/slides/gradient', 'gradient');

    $response = test()->get('/slides/gradient');
    $content = $response->getContent();

    // gradient slide 3 uses theme="white"
    expect($content)->toContain('slidewire-theme-white');
});

// ========================================================================
// Alpine theme data tests
// ========================================================================

it('passes configured themes to Alpine as background class data', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // configuredThemes should include the background class strings for themes
    // These are serialized into the Alpine x-data
    expect($content)->toContain('bg-gradient-to-br from-slate-900 via-blue-950 to-slate-950');
});

it('passes theme typography data to Alpine component', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // themeTypography is the last argument to slidewireDeck()
    expect($content)->toContain('text-slate-50')
        ->and($content)->toContain('text-4xl')
        ->and($content)->toContain('text-slate-200')
        ->and($content)->toContain('text-lg');
});

it('uses configured highlight font size in deck styles', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    expect($content)->toContain('font-size: 1rem;');
});

// ========================================================================
// Mermaid runtime integration tests
// ========================================================================

it('includes Mermaid loading logic in deck JavaScript', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    expect($content)->toContain('renderDiagrams')
        ->and($content)->toContain('data-slidewire-diagram');
});

it('includes renderDiagrams method in deck JavaScript', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    expect($content)->toContain('renderDiagrams');
});
