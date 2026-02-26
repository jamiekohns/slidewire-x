<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\EffectiveSettingsResolver;

it('resolves effective settings with slide > deck > config precedence', function (): void {
    $resolver = app(EffectiveSettingsResolver::class);

    $slides = [
        ['id' => 'test-0', 'html' => '', 'meta' => ['theme' => 'white', 'transition' => 'zoom'], 'fragments' => 0, 'class' => '', 'h' => 0, 'v' => 0],
        ['id' => 'test-1', 'html' => '', 'meta' => [], 'fragments' => 0, 'class' => '', 'h' => 1, 'v' => 0],
    ];

    $deckMeta = ['theme' => 'night', 'transition' => 'fade'];

    $result = $resolver->resolve($slides, $deckMeta);

    // Slide 0: slide-level overrides take priority
    expect($result[0]['effective']['theme'])->toBe('white')
        ->and($result[0]['effective']['transition'])->toBe('zoom');

    // Slide 1: inherits deck-level
    expect($result[1]['effective']['theme'])->toBe('night')
        ->and($result[1]['effective']['transition'])->toBe('fade');
});

it('falls back to config defaults when deck and slide have no overrides', function (): void {
    $resolver = app(EffectiveSettingsResolver::class);

    $slides = [
        ['id' => 'test-0', 'html' => '', 'meta' => [], 'fragments' => 0, 'class' => '', 'h' => 0, 'v' => 0],
    ];

    $result = $resolver->resolve($slides, []);

    expect($result[0]['effective']['theme'])->toBe('default')
        ->and($result[0]['effective']['transition'])->toBe('slide')
        ->and($result[0]['effective']['show_controls'])->toBe('1');
});

it('resolves highlight theme through slide > deck > config chain', function (): void {
    $resolver = app(EffectiveSettingsResolver::class);

    // Slide has highlight_theme
    $slides = [
        ['id' => 'test-0', 'html' => '', 'meta' => ['highlight_theme' => 'monokai'], 'fragments' => 0, 'class' => '', 'h' => 0, 'v' => 0],
        ['id' => 'test-1', 'html' => '', 'meta' => [], 'fragments' => 0, 'class' => '', 'h' => 1, 'v' => 0],
    ];

    $deckMeta = ['highlight_theme' => 'dracula'];

    $result = $resolver->resolve($slides, $deckMeta);

    expect($result[0]['effective']['highlight_theme'])->toBe('monokai')
        ->and($result[1]['effective']['highlight_theme'])->toBe('dracula');
});

it('preserves original slide data alongside effective settings', function (): void {
    $resolver = app(EffectiveSettingsResolver::class);

    $slides = [
        ['id' => 'test-0', 'html' => '<h1>Hello</h1>', 'meta' => ['notes' => 'Test notes'], 'fragments' => 2, 'class' => 'bg-red', 'h' => 0, 'v' => 0],
    ];

    $result = $resolver->resolve($slides, []);

    expect($result[0]['id'])->toBe('test-0')
        ->and($result[0]['html'])->toBe('<h1>Hello</h1>')
        ->and($result[0]['meta']['notes'])->toBe('Test notes')
        ->and($result[0]['fragments'])->toBe(2)
        ->and($result[0]['class'])->toBe('bg-red')
        ->and($result[0])->toHaveKey('effective');
});

it('handles empty slides array', function (): void {
    $resolver = app(EffectiveSettingsResolver::class);
    $result = $resolver->resolve([], []);

    expect($result)->toBe([]);
});
