<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationCompiler;

it('extracts metadata and utility classes from blade slides', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('background');
    $columns = $compiled['slides'];

    // Flatten 2D grid for assertion
    $slides = collect($columns)->flatten(1)->values()->all();

    expect($slides)->toHaveCount(2)
        ->and($slides[0]->meta)->toMatchArray([
            'transition' => 'fade',
            'auto_animate' => 'true',
            'auto_animate_duration' => '600',
            'auto_animate_easing' => 'linear',
            'transition_speed' => 'slow',
        ])
        ->and($slides[0]->class)->toContain('bg-contain')
        ->and($slides[1]->class)->toContain('bg-slate-800');
});
