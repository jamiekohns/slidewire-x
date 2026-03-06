<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationCompiler;

it('extracts deck-level metadata from the deck wrapper', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('precedence');

    expect($compiled['deck_meta'])->toMatchArray([
        'theme' => 'black',
        'transition' => 'fade',
        'auto_slide' => '5000',
    ]);
});

it('preserves slide-level overrides distinct from deck metadata', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('precedence');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    expect($slides)->toHaveCount(3);

    expect($slides[0]->meta)->not->toHaveKey('theme');
    expect($slides[0]->meta)->not->toHaveKey('auto_slide');

    expect($slides[1]->meta)->toMatchArray([
        'theme' => 'white',
        'transition' => 'zoom',
        'auto_slide' => '2000',
    ]);

    expect($slides[2]->meta)->not->toHaveKey('theme');
});

it('returns the correct 2D grid shape for flat decks', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('demo');
    $columns = $compiled['slides'];

    expect($columns)->toHaveCount(3);

    foreach ($columns as $column) {
        expect($column)->toHaveCount(1);
    }
});
