<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationCompiler;

it('returns empty result for non-existent presentation', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('does-not-exist');

    expect($compiled['deck_meta'])->toBe([])
        ->and($compiled['slides'])->toBe([]);
});

it('extracts fragment count from slides with fragments', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('demo');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    expect($slides[0]->fragments)->toBe(1);

    expect($slides[2]->fragments)->toBe(0);
});

it('generates unique slide IDs per horizontal index', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('demo');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    $ids = array_map(fn ($slide) => $slide->id, $slides);

    expect(count($ids))->toBe(count(array_unique($ids)));
});

it('flattens 2D slide grid for PDF export', function (): void {
    $compiler = app(PresentationCompiler::class);
    $compiled = $compiler->compile('vertical');

    $flat = $compiler->flattenSlides($compiled['slides']);

    expect($flat)->toHaveCount(5)
        ->and($flat[0]->html)->toContain('Horizontal Slide 1')
        ->and($flat[1]->html)->toContain('Stack Top')
        ->and($flat[2]->html)->toContain('Stack Middle')
        ->and($flat[3]->html)->toContain('Stack Bottom')
        ->and($flat[4]->html)->toContain('Horizontal Slide 3');
});

it('flattens simple flat deck correctly', function (): void {
    $compiler = app(PresentationCompiler::class);
    $compiled = $compiler->compile('demo');

    $flat = $compiler->flattenSlides($compiled['slides']);

    expect($flat)->toHaveCount(3);
});

it('handles empty slides gracefully in flatten', function (): void {
    $compiler = app(PresentationCompiler::class);

    expect($compiler->flattenSlides([]))->toBe([]);
});

it('preserves slide HTML content through compilation', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('demo');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    expect($slides[0]->html)->toContain('Demo Intro')
        ->and($slides[2]->html)->toContain('Final slide');
});

it('extracts class attribute from compiled slides', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('demo');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    expect($slides[0]->class)->toContain('bg-slate-900')
        ->and($slides[0]->class)->toContain('text-white');
});

it('compiles autoslide fixture correctly', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('autoslide');
    $slides = collect($compiled['slides'])->flatten(1)->values()->all();

    expect($slides)->toHaveCount(2)
        ->and($slides[0]->meta['auto_slide'])->toBe('300')
        ->and($slides[0]->meta['transition_speed'])->toBe('fast');
});
