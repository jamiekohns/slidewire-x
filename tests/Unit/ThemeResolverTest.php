<?php

declare(strict_types=1);

use Phiki\Theme\Theme;
use WendellAdriel\SlideWire\DTOs\FontConfig;
use WendellAdriel\SlideWire\DTOs\Slide;
use WendellAdriel\SlideWire\DTOs\ThemeConfig;
use WendellAdriel\SlideWire\DTOs\ThemeFont;
use WendellAdriel\SlideWire\Enums\FontSource;
use WendellAdriel\SlideWire\Support\ThemeResolver;

it('builds background class map from nested theme config', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->backgroundClassMap();

    expect($map)->toHaveKey('default')
        ->and($map['default'])->toContain('bg-gradient-to-br')
        ->and($map)->toHaveKey('white')
        ->and($map['white'])->toContain('bg-white');
});

it('builds background class map for all built-in themes', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->backgroundClassMap();

    $builtInThemes = ['default', 'black', 'white', 'aurora', 'sunset', 'neon', 'solarized'];

    foreach ($builtInThemes as $theme) {
        expect($map)->toHaveKey($theme)
            ->and($map[$theme])->not->toBeEmpty();
    }
});

it('includes text color classes in background class map', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->backgroundClassMap();

    expect($map['white'])->toContain('text-zinc-800');
    expect($map['default'])->toContain('text-slate-50');
});

it('builds typography class map with title and text strings', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map)->toHaveKey('default')
        ->and($map['default'])->toHaveKeys(['title', 'text'])
        ->and($map['default']['title'])->toContain('text-slate-50')
        ->and($map['default']['text'])->toContain('text-slate-200');
});

it('builds typography class map for all built-in themes', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    $builtInThemes = ['default', 'black', 'white', 'aurora', 'sunset', 'neon', 'solarized'];

    foreach ($builtInThemes as $theme) {
        expect($map)->toHaveKey($theme)
            ->and($map[$theme])->toHaveKeys(['title', 'text'])
            ->and($map[$theme]['title'])->toBeString()
            ->and($map[$theme]['text'])->toBeString();
    }
});

it('includes font size in typography class map', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map['default']['title'])->toContain('text-4xl')
        ->and($map['default']['text'])->toContain('text-lg');
});

it('includes font class in typography when specified', function (): void {
    $themes = config('slidewire.themes', []);
    $themes['custom-font'] = new ThemeConfig(
        background: 'bg-black text-white',
        highlightTheme: Theme::GithubDark,
        title: new ThemeFont('font-serif', 'text-white', 'text-5xl'),
        text: new ThemeFont('font-mono', 'text-white', 'text-base'),
    );
    config()->set('slidewire.themes', $themes);

    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map['custom-font']['title'])->toContain('font-serif')
        ->and($map['custom-font']['text'])->toContain('font-mono');
});

it('returns google fonts url for default font configuration', function (): void {
    $resolver = app(ThemeResolver::class);
    $url = $resolver->googleFontsUrl();

    expect($url)->toContain('fonts.googleapis.com')
        ->and($url)->toContain('Inter')
        ->and($url)->toContain('JetBrains');
});

it('returns null google fonts url when fonts config is empty', function (): void {
    config()->set('slidewire.fonts', []);
    $resolver = app(ThemeResolver::class);

    expect($resolver->googleFontsUrl())->toBeNull();
});

it('builds google fonts url when google fonts are configured', function (): void {
    config()->set('slidewire.fonts', [
        'Inter' => new FontConfig(FontSource::Google, [400, 600, 700]),
        'Georgia' => new FontConfig(FontSource::System),
    ]);

    $resolver = app(ThemeResolver::class);
    $url = $resolver->googleFontsUrl();

    expect($url)->toContain('fonts.googleapis.com')
        ->and($url)->toContain('Inter')
        ->and($url)->toContain('400;600;700')
        ->and($url)->not->toContain('Georgia');
});

it('resolves code font family from configured mono font', function (): void {
    $resolver = app(ThemeResolver::class);
    $fontFamily = $resolver->codeFontFamily();

    expect($fontFamily)->toContain('JetBrainsMono')
        ->and($fontFamily)->toContain('monospace');
});

it('falls back to system monospace stack when no mono font is configured', function (): void {
    config()->set('slidewire.fonts', []);
    $resolver = app(ThemeResolver::class);
    $fontFamily = $resolver->codeFontFamily();

    expect($fontFamily)->toContain('ui-monospace')
        ->and($fontFamily)->not->toContain('JetBrains');
});

it('extracts slide themes from effective slides', function (): void {
    $resolver = app(ThemeResolver::class);

    $slides = [
        new Slide(id: 'slide-0', html: '', effective: ['theme' => 'black']),
        new Slide(id: 'slide-1', html: '', effective: ['theme' => null]),
        new Slide(id: 'slide-2', html: '', effective: ['theme' => 'white']),
    ];

    expect($resolver->slideThemes($slides))->toBe(['black', null, 'white']);
});

it('detects vertical slides in grid shape', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->hasVerticalSlides([1, 1, 1]))->toBeFalse()
        ->and($resolver->hasVerticalSlides([1, 3, 1]))->toBeTrue()
        ->and($resolver->hasVerticalSlides([]))->toBeFalse();
});
