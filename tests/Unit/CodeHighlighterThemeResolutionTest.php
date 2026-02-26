<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\CodeHighlighter;

it('resolves highlight theme from explicit parameter', function (): void {
    $highlighter = app(CodeHighlighter::class);
    $resolved = $highlighter->resolveHighlightTheme('monokai', 'night');

    expect($resolved)->toBe('monokai');
});

it('resolves highlight theme from nested theme config highlight_theme', function (): void {
    $highlighter = app(CodeHighlighter::class);

    expect($highlighter->resolveHighlightTheme(null, 'white'))->toBe('catppuccin-latte');
    expect($highlighter->resolveHighlightTheme(null, 'night'))->toBe('catppuccin-mocha');
    expect($highlighter->resolveHighlightTheme(null, 'solarized'))->toBe('catppuccin-latte');
});

it('falls back to config default when theme has no highlight_theme', function (): void {
    $highlighter = app(CodeHighlighter::class);

    expect($highlighter->resolveHighlightTheme(null, 'custom-unmapped'))->toBe('catppuccin-mocha');
    expect($highlighter->resolveHighlightTheme())->toBe('catppuccin-mocha');
});

it('produces highlighted output with custom highlight theme', function (): void {
    $highlighter = app(CodeHighlighter::class);
    $html = $highlighter->highlight("<?php\necho 'test';", 'php', 'github-dark')->toHtml();

    expect($html)->toContain('phiki')
        ->and($html)->toContain('language-php');
});

it('resolves highlight theme for all built-in themes', function (): void {
    $highlighter = app(CodeHighlighter::class);
    $themes = config('slidewire.themes', []);

    foreach ($themes as $name => $config) {
        $expected = is_array($config) ? ($config['highlight_theme'] ?? 'catppuccin-mocha') : 'catppuccin-mocha';
        $resolved = $highlighter->resolveHighlightTheme(null, $name);

        expect($resolved)->toBe($expected, "Theme '{$name}' should resolve to '{$expected}'");
    }
});

it('gives explicit parameter highest priority over theme and config', function (): void {
    $highlighter = app(CodeHighlighter::class);

    // Even with a valid presentation theme that has its own highlight_theme,
    // the explicit parameter wins
    $resolved = $highlighter->resolveHighlightTheme('one-dark-pro', 'white');

    expect($resolved)->toBe('one-dark-pro');
});
