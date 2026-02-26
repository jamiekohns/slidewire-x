<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationCompiler;

it('extracts transition speed and auto slide metadata from blade slides', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('autoslide');
    $columns = $compiled['slides'];

    // Flatten 2D grid for assertion
    $slides = collect($columns)->flatten(1)->values()->all();

    expect($slides)->toHaveCount(2)
        ->and($slides[0]['meta'])->toMatchArray([
            'transition' => 'fade',
            'transition_speed' => 'fast',
            'auto_slide' => '300',
        ]);
});
