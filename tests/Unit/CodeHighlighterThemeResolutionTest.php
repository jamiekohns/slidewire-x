<?php

declare(strict_types=1);

use Phiki\Theme\Theme;
use WendellAdriel\SlideWire\Support\CodeHighlighter;

it('resolves highlight theme from explicit parameter', function (): void {
    $highlighter = app(CodeHighlighter::class);
    $resolved = $highlighter->resolveHighlightTheme('monokai', 'black');

    expect($resolved)->toBe(Theme::Monokai);
});

it('resolves highlight theme from nested theme config highlight_theme', function (): void {
    $highlighter = app(CodeHighlighter::class);

    expect($highlighter->resolveHighlightTheme(null, 'white'))->toBe(Theme::CatppuccinLatte);
    expect($highlighter->resolveHighlightTheme(null, 'black'))->toBe(Theme::CatppuccinMocha);
    expect($highlighter->resolveHighlightTheme(null, 'solarized'))->toBe(Theme::CatppuccinLatte);
});

it('falls back to config default when theme has no highlight_theme', function (): void {
    $highlighter = app(CodeHighlighter::class);

    expect($highlighter->resolveHighlightTheme(null, 'custom-unmapped'))->toBe(Theme::CatppuccinMocha);
    expect($highlighter->resolveHighlightTheme())->toBe(Theme::CatppuccinMocha);
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
        $expected = $config->highlightTheme;
        $resolved = $highlighter->resolveHighlightTheme(null, $name);

        expect($resolved)->toBe($expected, "Theme '{$name}' should resolve to '{$expected->value}'");
    }
});

it('gives explicit parameter highest priority over theme and config', function (): void {
    $highlighter = app(CodeHighlighter::class);

    $resolved = $highlighter->resolveHighlightTheme('one-dark-pro', 'white');

    expect($resolved)->toBe(Theme::OneDarkPro);
});
