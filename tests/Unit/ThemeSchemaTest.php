<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\FontConfig;
use WendellAdriel\SlideWire\Support\FontSource;
use WendellAdriel\SlideWire\Support\ThemeConfig;
use WendellAdriel\SlideWire\Support\ThemeFont;

it('has nested theme structure with required keys', function (): void {
    $themes = config('slidewire.themes', []);

    expect($themes)->not->toBeEmpty();

    foreach ($themes as $theme) {
        expect($theme)->toBeInstanceOf(ThemeConfig::class)
            ->and($theme->title)->toBeInstanceOf(ThemeFont::class)
            ->and($theme->text)->toBeInstanceOf(ThemeFont::class);
    }
});

it('provides all built-in theme presets', function (): void {
    $themes = config('slidewire.themes', []);
    $expected = ['default', 'black', 'white', 'aurora', 'sunset', 'neon', 'solarized'];

    foreach ($expected as $name) {
        expect($themes)->toHaveKey($name);
    }
});

it('has non-empty background class for every built-in theme', function (): void {
    $themes = config('slidewire.themes', []);

    foreach ($themes as $name => $theme) {
        expect($theme->background)->toBeString()
            ->not->toBeEmpty("Theme '{$name}' must have a background class");
    }
});

it('has valid highlight_theme for every built-in theme', function (): void {
    $themes = config('slidewire.themes', []);

    foreach ($themes as $name => $theme) {
        expect($theme->highlightTheme)->toBeString()
            ->not->toBeEmpty("Theme '{$name}' must have a highlight_theme");
    }
});

it('does not have legacy theme_highlight_map config key', function (): void {
    expect(config('slidewire.theme_highlight_map'))->toBeNull();
});

it('fonts config includes default google fonts', function (): void {
    $fonts = config('slidewire.fonts');

    expect($fonts)->toHaveKey('Inter')
        ->and($fonts)->toHaveKey('JetBrainsMono')
        ->and($fonts['Inter'])->toBeInstanceOf(FontConfig::class)
        ->and($fonts['JetBrainsMono'])->toBeInstanceOf(FontConfig::class)
        ->and($fonts['Inter']->source)->toBe(FontSource::Google)
        ->and($fonts['JetBrainsMono']->source)->toBe(FontSource::Google);
});
