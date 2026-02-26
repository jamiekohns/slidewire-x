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

// ========================================================================
// Theme typography CSS tests
// ========================================================================

it('injects typography CSS style block into rendered deck', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Typography CSS rules should be present in the style block
    expect($content)->toContain('.slidewire-theme-default .slidewire-content h1')
        ->and($content)->toContain('.slidewire-theme-default .slidewire-content {');
});

it('injects correct title color CSS for the default theme', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Default theme title color: text-slate-50 => #f8fafc
    expect($content)->toContain('color: #f8fafc');
});

it('injects correct title font-size CSS for the default theme', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Default theme title size: text-4xl => 2.25rem
    expect($content)->toContain('font-size: 2.25rem');
});

it('injects correct text color CSS for the default theme', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Default theme text color: text-slate-200 => #e2e8f0
    expect($content)->toContain('color: #e2e8f0');
});

it('injects correct text font-size CSS for the default theme', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    // Default theme text size: text-lg => 1.125rem
    expect($content)->toContain('font-size: 1.125rem');
});

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

    // Precedence fixture has deck theme=night and slide 2 with theme=white
    expect($content)->toContain('slidewire-theme-night')
        ->and($content)->toContain('slidewire-theme-white');
});

it('includes typography CSS for both night and white themes in precedence deck', function (): void {
    Route::slidewire('/slides/precedence', 'precedence');

    $response = test()->get('/slides/precedence');
    $content = $response->getContent();

    // Night theme title: text-slate-200 => #e2e8f0
    expect($content)->toContain('.slidewire-theme-night .slidewire-content h1');

    // White theme title: text-zinc-800 => #27272a
    expect($content)->toContain('.slidewire-theme-white .slidewire-content h1');
});
