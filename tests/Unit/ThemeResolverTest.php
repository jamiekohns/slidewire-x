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

it('builds typography class map with title and text strings', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map)->toHaveKey('default')
        ->and($map['default'])->toHaveKeys(['title', 'text'])
        ->and($map['default']['title'])->toContain('text-slate-50')
        ->and($map['default']['text'])->toContain('text-slate-200');
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
