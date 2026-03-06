<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\FontConfig;
use WendellAdriel\SlideWire\Support\FontSource;
use WendellAdriel\SlideWire\Support\ThemeConfig;
use WendellAdriel\SlideWire\Support\ThemeFont;

return [

    /*
    |--------------------------------------------------------------------------
    | Presentation Roots
    |--------------------------------------------------------------------------
    |
    | Here you may configure the directories where SlideWire should look for
    | Blade and Markdown presentations. These paths are used by the compiler,
    | route macro, and scaffold command when resolving presentation files.
    |
    */

    'presentation_roots' => [
        resource_path('views/pages/slides'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Slide Defaults
    |--------------------------------------------------------------------------
    |
    | These values provide the fallback runtime settings for every deck and
    | slide. Slide attributes take priority over deck attributes, and deck
    | attributes take priority over the values defined here.
    |
    */

    'slides' => [
        'theme' => 'default',
        'show_controls' => true,
        'show_progress' => true,
        'show_fullscreen_button' => true,
        'keyboard' => true,
        'touch' => true,
        'transition' => 'slide',
        'transition_duration' => 350,
        'transition_speed' => 'default',
        'auto_slide' => 0,
        'auto_slide_pause_on_interaction' => true,
        'highlight' => [
            'enabled' => true,
            'theme' => 'catppuccin-mocha',
            'font' => 'JetBrainsMono',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Presets
    |--------------------------------------------------------------------------
    |
    | Each theme preset defines the background classes, syntax highlight theme,
    | and typography applied when a deck or slide uses that theme name. The
    | config uses DTOs so the structure stays explicit and strongly typed.
    |
    */

    'themes' => [
        'default' => new ThemeConfig(
            background: 'bg-gradient-to-br from-slate-900 via-blue-950 to-slate-950 text-slate-50',
            highlightTheme: 'catppuccin-mocha',
            title: new ThemeFont('Inter', 'text-slate-50', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-slate-200', 'text-lg'),
        ),
        'black' => new ThemeConfig(
            background: 'bg-slate-900 text-slate-200',
            highlightTheme: 'catppuccin-mocha',
            title: new ThemeFont('Inter', 'text-slate-200', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-slate-300', 'text-lg'),
        ),
        'white' => new ThemeConfig(
            background: 'bg-white text-zinc-800',
            highlightTheme: 'catppuccin-latte',
            title: new ThemeFont('Inter', 'text-zinc-800', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-zinc-600', 'text-lg'),
        ),
        'aurora' => new ThemeConfig(
            background: 'bg-gradient-to-br from-emerald-950 via-cyan-900 to-slate-950 text-emerald-50',
            highlightTheme: 'catppuccin-mocha',
            title: new ThemeFont('Inter', 'text-emerald-50', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-cyan-100', 'text-lg'),
        ),
        'sunset' => new ThemeConfig(
            background: 'bg-gradient-to-br from-rose-950 via-orange-900 to-amber-700 text-orange-50',
            highlightTheme: 'catppuccin-mocha',
            title: new ThemeFont('Inter', 'text-orange-50', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-amber-100', 'text-lg'),
        ),
        'neon' => new ThemeConfig(
            background: 'bg-gradient-to-br from-fuchsia-950 via-violet-900 to-cyan-900 text-fuchsia-50',
            highlightTheme: 'catppuccin-mocha',
            title: new ThemeFont('Inter', 'text-fuchsia-50', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-cyan-100', 'text-lg'),
        ),
        'solarized' => new ThemeConfig(
            background: 'bg-yellow-50 text-slate-600',
            highlightTheme: 'catppuccin-latte',
            title: new ThemeFont('Inter', 'text-slate-700', 'text-4xl'),
            text: new ThemeFont('Inter', 'text-slate-600', 'text-lg'),
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Font Loading
    |--------------------------------------------------------------------------
    |
    | Map font family names used by theme typography and code blocks to their
    | loading strategy. System fonts require no loading, while Google Fonts
    | families are automatically included in the rendered presentation.
    |
    */

    'fonts' => [
        'Inter' => new FontConfig(FontSource::Google, [400, 600, 700]),
        'JetBrainsMono' => new FontConfig(FontSource::Google, [400, 700]),
    ],
];
