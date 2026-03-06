<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationCompiler;

it('compiles vertical-slide groups into a 2D grid structure', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('vertical');
    $columns = $compiled['slides'];

    // 3 horizontal columns: solo slide, vertical-slide group of 3, solo slide
    expect($columns)->toHaveCount(3);

    // Column 0: single horizontal slide
    expect($columns[0])->toHaveCount(1);
    expect($columns[0][0]->html)->toContain('Horizontal Slide 1');

    // Column 1: vertical-slide group with 3 slides
    expect($columns[1])->toHaveCount(3);
    expect($columns[1][0]->html)->toContain('Stack Top');
    expect($columns[1][1]->html)->toContain('Stack Middle');
    expect($columns[1][2]->html)->toContain('Stack Bottom');

    // Column 2: single horizontal slide
    expect($columns[2])->toHaveCount(1);
    expect($columns[2][0]->html)->toContain('Horizontal Slide 3');
});

it('generates correct slide IDs for vertical slides', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('vertical');
    $columns = $compiled['slides'];

    // Solo slides get simple IDs
    expect($columns[0][0]->id)->toBe('vertical-0');

    // Vertical slides get h-v IDs for v > 0
    expect($columns[1][0]->id)->toBe('vertical-1');
    expect($columns[1][1]->id)->toBe('vertical-1-1');
    expect($columns[1][2]->id)->toBe('vertical-1-2');

    expect($columns[2][0]->id)->toBe('vertical-2');
});

it('extracts deck metadata from a deck with vertical-slide groups', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('vertical');

    expect($compiled['deck_meta'])->toMatchArray([
        'transition' => 'fade',
    ]);
});

it('produces correct grid shape for vertical-slide group decks', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('vertical');
    $columns = $compiled['slides'];

    $gridShape = array_map(count(...), $columns);

    expect($gridShape)->toBe([1, 3, 1]);
});
