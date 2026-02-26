<?php

declare(strict_types=1);

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

    $builtInThemes = ['default', 'black', 'white', 'league', 'beige', 'night', 'serif', 'simple', 'solarized'];

    foreach ($builtInThemes as $theme) {
        expect($map)->toHaveKey($theme)
            ->and($map[$theme])->not->toBeEmpty();
    }
});

it('includes text color classes in background class map', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->backgroundClassMap();

    // white theme: bg-white text-zinc-800
    expect($map['white'])->toContain('text-zinc-800');
    // default theme includes text-slate-50
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

    $builtInThemes = ['default', 'black', 'white', 'league', 'beige', 'night', 'serif', 'simple', 'solarized'];

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

    // All built-in themes use text-4xl for title and text-lg for text
    expect($map['default']['title'])->toContain('text-4xl')
        ->and($map['default']['text'])->toContain('text-lg');
});

it('includes font class in typography when specified', function (): void {
    config()->set('slidewire.themes.custom-font', [
        'background' => 'bg-black text-white',
        'highlight_theme' => 'github-dark',
        'title' => ['font' => 'font-serif', 'color' => 'text-white', 'size' => 'text-5xl'],
        'text' => ['font' => 'font-mono', 'color' => 'text-white', 'size' => 'text-base'],
    ]);

    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map['custom-font']['title'])->toContain('font-serif')
        ->and($map['custom-font']['text'])->toContain('font-mono');
});

it('returns empty typography strings for non-array theme entries', function (): void {
    config()->set('slidewire.themes', ['legacy' => 'bg-black text-white']);

    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map['legacy']['title'])->toBe('')
        ->and($map['legacy']['text'])->toBe('');
});

it('returns null google fonts url when no google fonts are configured', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->googleFontsUrl())->toBeNull();
});

it('builds google fonts url when google fonts are configured', function (): void {
    config()->set('slidewire.fonts', [
        'Inter' => ['source' => 'google', 'weights' => [400, 600, 700]],
        'Georgia' => ['source' => 'system'],
    ]);

    $resolver = app(ThemeResolver::class);
    $url = $resolver->googleFontsUrl();

    expect($url)->toContain('fonts.googleapis.com')
        ->and($url)->toContain('Inter')
        ->and($url)->toContain('400;600;700')
        ->and($url)->not->toContain('Georgia');
});

it('extracts slide themes from effective slides', function (): void {
    $resolver = app(ThemeResolver::class);

    $slides = [
        ['effective' => ['theme' => 'night']],
        ['effective' => ['theme' => null]],
        ['effective' => ['theme' => 'white']],
    ];

    expect($resolver->slideThemes($slides))->toBe(['night', null, 'white']);
});

it('detects vertical slides in grid shape', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->hasVerticalSlides([1, 1, 1]))->toBeFalse()
        ->and($resolver->hasVerticalSlides([1, 3, 1]))->toBeTrue()
        ->and($resolver->hasVerticalSlides([]))->toBeFalse();
});
