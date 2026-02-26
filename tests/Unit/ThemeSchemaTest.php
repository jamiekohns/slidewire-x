<?php

declare(strict_types=1);

it('has nested theme structure with required keys', function (): void {
    $themes = config('slidewire.themes', []);

    expect($themes)->not->toBeEmpty();

    foreach ($themes as $theme) {
        expect($theme)->toBeArray();
        expect($theme)->toHaveKeys(['background', 'highlight_theme', 'title', 'text']);
        expect($theme['title'])->toBeArray();
        expect($theme['text'])->toBeArray();
        expect($theme['title'])->toHaveKeys(['font', 'color', 'size']);
        expect($theme['text'])->toHaveKeys(['font', 'color', 'size']);
    }
});

it('provides all built-in theme presets', function (): void {
    $themes = config('slidewire.themes', []);
    $expected = ['default', 'black', 'white', 'league', 'beige', 'night', 'serif', 'simple', 'solarized'];

    foreach ($expected as $name) {
        expect($themes)->toHaveKey($name);
    }
});

it('has non-empty background class for every built-in theme', function (): void {
    $themes = config('slidewire.themes', []);

    foreach ($themes as $name => $theme) {
        expect($theme['background'])->toBeString()
            ->not->toBeEmpty("Theme '{$name}' must have a background class");
    }
});

it('has valid highlight_theme for every built-in theme', function (): void {
    $themes = config('slidewire.themes', []);

    foreach ($themes as $name => $theme) {
        expect($theme['highlight_theme'])->toBeString()
            ->not->toBeEmpty("Theme '{$name}' must have a highlight_theme");
    }
});

it('does not have legacy theme_highlight_map config key', function (): void {
    expect(config('slidewire.theme_highlight_map'))->toBeNull();
});

it('fonts config includes default google fonts', function (): void {
    $fonts = config('slidewire.fonts');

    expect($fonts)->toHaveKey('Inter')
        ->and($fonts)->toHaveKey('JetBrains Mono')
        ->and($fonts['Inter']['source'])->toBe('google')
        ->and($fonts['JetBrains Mono']['source'])->toBe('google');
});
