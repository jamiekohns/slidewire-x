<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationCompiler;

it('extracts deck-level metadata from the deck wrapper', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('precedence');

    expect($compiled['deck_meta'])->toMatchArray([
        'theme' => 'night',
        'transition' => 'fade',
        'auto_slide' => '5000',
    ]);
});

it('preserves slide-level overrides distinct from deck metadata', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('precedence');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    expect($slides)->toHaveCount(3);

    // Slide 0 inherits from deck (no slide-level overrides for theme/transition)
    expect($slides[0]['meta'])->not->toHaveKey('theme');
    expect($slides[0]['meta'])->not->toHaveKey('auto_slide');

    // Slide 1 has explicit overrides
    expect($slides[1]['meta'])->toMatchArray([
        'theme' => 'white',
        'transition' => 'zoom',
        'auto_slide' => '2000',
    ]);

    // Slide 2 also inherits (no overrides)
    expect($slides[2]['meta'])->not->toHaveKey('theme');
});

it('returns the correct 2D grid shape for flat decks', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('demo');
    $columns = $compiled['slides'];

    // Demo has 3 slides, all horizontal (no stacks)
    expect($columns)->toHaveCount(3);

    foreach ($columns as $column) {
        expect($column)->toHaveCount(1);
    }
});
