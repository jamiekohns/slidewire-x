<?php

declare(strict_types=1);

use Phiki\Theme\Theme;
use WendellAdriel\SlideWire\Support\ConfigValidator;
use WendellAdriel\SlideWire\Support\FontConfig;
use WendellAdriel\SlideWire\Support\FontSource;
use WendellAdriel\SlideWire\Support\HighlightConfig;
use WendellAdriel\SlideWire\Support\SlidesConfig;
use WendellAdriel\SlideWire\Support\SlideTransition;
use WendellAdriel\SlideWire\Support\SlideTransitionSpeed;
use WendellAdriel\SlideWire\Support\ThemeConfig;
use WendellAdriel\SlideWire\Support\ThemeFont;

it('validates the default config without errors', function (): void {
    $validator = new ConfigValidator();
    $validator->validate();
})->throwsNoExceptions();

it('rejects non-dto theme entries', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes(['broken' => 'just-a-string']);
})->throws(InvalidArgumentException::class, 'must be a ThemeConfig');

it('rejects themes with empty background', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes([
        'broken' => new ThemeConfig(
            background: '',
            highlightTheme: Theme::GithubDark,
            title: new ThemeFont('Inter', 'text-white', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-slate-300', 'text-lg'),
        ),
    ]);
})->throws(InvalidArgumentException::class, 'missing required key [background]');

it('rejects themes with empty title typography values', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes([
        'broken' => new ThemeConfig(
            background: 'bg-red',
            highlightTheme: Theme::GithubDark,
            title: new ThemeFont('', 'text-white', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-slate-300', 'text-lg'),
        ),
    ]);
})->throws(InvalidArgumentException::class, 'missing required key [font]');

it('rejects themes with empty typography size', function (): void {
    $validator = new ConfigValidator();
    $validator->validateThemes([
        'broken' => new ThemeConfig(
            background: 'bg-red',
            highlightTheme: Theme::GithubDark,
            title: new ThemeFont('Inter', 'text-white', ''),
            text: new ThemeFont('Inter', 'text-slate-300', 'text-lg'),
        ),
    ]);
})->throws(InvalidArgumentException::class, 'missing required key [size]');

it('rejects non-dto font entries', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts(['BadFont' => 'google']);
})->throws(InvalidArgumentException::class, 'must be a FontConfig');

it('accepts valid font configurations', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts([
        'Inter' => new FontConfig(FontSource::Google, [400, 700]),
        'Georgia' => new FontConfig(FontSource::System),
    ]);
})->throwsNoExceptions();

it('accepts valid slide settings', function (): void {
    $validator = new ConfigValidator();
    $validator->validateSlides(new SlidesConfig(
        transition: SlideTransition::Fade,
        transitionSpeed: SlideTransitionSpeed::Fast,
        highlight: new HighlightConfig(fontSize: 'lg'),
    ));
})->throwsNoExceptions();

it('validates font weights must be integers', function (): void {
    $validator = new ConfigValidator();
    $validator->validateFonts(['Inter' => new FontConfig(FontSource::Google, ['400'])]);
})->throws(InvalidArgumentException::class, 'weights must be an array of integers');

it('rejects empty highlight font size', function (): void {
    $validator = new ConfigValidator();
    $validator->validateSlides(new SlidesConfig(
        highlight: new HighlightConfig(fontSize: '  '),
    ));
})->throws(InvalidArgumentException::class, 'font_size must be a non-empty string');
