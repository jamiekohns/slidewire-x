<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\DTOs\Slide;
use WendellAdriel\SlideWire\Support\SlideViewDataFactory;

it('builds slide frame data from effective slide metadata', function (): void {
    $slide = new Slide(
        id: 'demo-0',
        html: '<h1>Hello</h1>',
        meta: [
            'background' => '/images/hero.jpg',
            'background_video' => '/videos/intro.mp4',
            'background_video_loop' => 'false',
            'background_opacity' => '0.35',
        ],
        class: 'text-white',
        effective: ['theme' => 'white'],
    );

    $frames = app(SlideViewDataFactory::class)->buildSlideFrames([
        $slide,
    ], [
        'white' => ['title' => 'font-serif', 'text' => 'prose-lg'],
    ], [
        'white' => 'bg-white text-slate-950',
    ], 'black');

    expect($frames)->toHaveCount(1)
        ->and($frames[0]['theme_name'])->toBe('white')
        ->and($frames[0]['text_typography'])->toBe('prose-lg')
        ->and($frames[0]['slide_theme_class'])->toBe('slidewire-theme-white')
        ->and($frames[0]['background_theme_class'])->toBe('bg-white text-slate-950')
        ->and($frames[0]['background_image'])->toBe('/images/hero.jpg')
        ->and($frames[0]['background_video'])->toBe('/videos/intro.mp4')
        ->and($frames[0]['video_loops'])->toBeFalse()
        ->and($frames[0]['video_muted'])->toBeTrue()
        ->and($frames[0]['style'])->toContain('background-image: url(/images/hero.jpg)')
        ->and($frames[0]['style'])->toContain('--slidewire-background-opacity: 0.35');
});

it('normalizes deck flags from string or boolean values', function (): void {
    $factory = app(SlideViewDataFactory::class);

    expect($factory->resolveDeckFlag(['show_progress' => 'false'], 'show_progress', true))->toBeFalse()
        ->and($factory->resolveDeckFlag(['show_progress' => false], 'show_progress', true))->toBeFalse()
        ->and($factory->resolveDeckFlag([], 'show_progress', true))->toBeTrue();
});

it('builds deck payload arrays used by the presentation view', function (): void {
    $payload = app(SlideViewDataFactory::class)->buildDeckPayload([
        new Slide(id: 'demo-0', html: '', h: 0, v: 0, effective: ['transition_duration' => '480', 'auto_slide' => '1200']),
        new Slide(id: 'demo-1', html: '', h: 1, v: 2, effective: []),
    ]);

    expect($payload)->toBe([
        'transition_durations' => [480, 350],
        'auto_slides' => [1200, 0],
        'coords' => [
            ['h' => 0, 'v' => 0],
            ['h' => 1, 'v' => 2],
        ],
    ]);
});
