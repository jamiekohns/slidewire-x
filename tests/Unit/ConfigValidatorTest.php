<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\ConfigValidator;

it('validates the default config without errors', function (): void {
    $validator = new ConfigValidator();
    $validator->validate();
})->throwsNoExceptions();

it('rejects non-array theme entries', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes(['broken' => 'just-a-string']);
})->throws(InvalidArgumentException::class, 'must be an array');

it('rejects themes missing required keys', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes(['broken' => ['background' => 'bg-red']]);
})->throws(InvalidArgumentException::class, 'missing required key');

it('rejects themes with non-array typography', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes([
        'broken' => [
            'background' => 'bg-red',
            'highlight_theme' => 'github-dark',
            'title' => 'not-an-array',
            'text' => ['font' => '', 'color' => '', 'size' => ''],
        ],
    ]);
})->throws(InvalidArgumentException::class, 'must be an array');

it('rejects themes with missing typography keys', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes([
        'broken' => [
            'background' => 'bg-red',
            'highlight_theme' => 'github-dark',
            'title' => ['font' => '', 'color' => ''],
            'text' => ['font' => '', 'color' => '', 'size' => ''],
        ],
    ]);
})->throws(InvalidArgumentException::class, 'missing required key [size]');

it('rejects fonts with invalid source', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts(['BadFont' => ['source' => 'cdn']]);
})->throws(InvalidArgumentException::class, 'invalid source');

it('rejects fonts missing source key', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts(['BadFont' => ['weights' => [400]]]);
})->throws(InvalidArgumentException::class, 'missing required key [source]');

it('rejects non-array font entries', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts(['BadFont' => 'google']);
})->throws(InvalidArgumentException::class, 'must be an array');

it('rejects invalid transition value', function (): void {
    $validator = new ConfigValidator();
    $validator->validateSlides(['transition' => 'flip']);
})->throws(InvalidArgumentException::class, 'invalid');

it('rejects invalid transition speed', function (): void {
    $validator = new ConfigValidator();
    $validator->validateSlides(['transition_speed' => 'instant']);
})->throws(InvalidArgumentException::class, 'invalid');

it('accepts valid font configurations', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts([
        'Inter' => ['source' => 'google', 'weights' => [400, 700]],
        'Georgia' => ['source' => 'system'],
    ]);
})->throwsNoExceptions();

it('accepts valid slide settings', function (): void {
    $validator = new ConfigValidator();
    $validator->validateSlides([
        'transition' => 'fade',
        'transition_speed' => 'fast',
    ]);
})->throwsNoExceptions();

it('validates font weights must be an array', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts(['Inter' => ['source' => 'google', 'weights' => '400']]);
})->throws(InvalidArgumentException::class, 'weights must be an array');
