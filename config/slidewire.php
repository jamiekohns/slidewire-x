<?php

declare(strict_types=1);

return [
    'presentation_roots' => [
        resource_path('views/pages/slides'),
    ],

    'defaults' => [
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
        'markdown' => [
            'enabled' => true,
            'separator' => "\n---\n",
        ],
        'highlight' => [
            'enabled' => true,
            'theme' => 'github-dark',
        ],
    ],

    'themes' => [
        'default' => [
            'background' => 'bg-gradient-to-br from-slate-900 via-blue-950 to-slate-950 text-slate-50',
            'highlight_theme' => 'github-dark',
            'title' => ['font' => '', 'color' => 'text-slate-50', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-slate-200', 'size' => 'text-lg'],
        ],
        'black' => [
            'background' => 'bg-zinc-950 text-zinc-100',
            'highlight_theme' => 'github-dark',
            'title' => ['font' => '', 'color' => 'text-zinc-100', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-zinc-300', 'size' => 'text-lg'],
        ],
        'white' => [
            'background' => 'bg-white text-zinc-800',
            'highlight_theme' => 'github-light',
            'title' => ['font' => '', 'color' => 'text-zinc-800', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-zinc-600', 'size' => 'text-lg'],
        ],
        'league' => [
            'background' => 'bg-neutral-800 text-neutral-100',
            'highlight_theme' => 'github-dark',
            'title' => ['font' => '', 'color' => 'text-neutral-100', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-neutral-300', 'size' => 'text-lg'],
        ],
        'beige' => [
            'background' => 'bg-amber-50 text-stone-700',
            'highlight_theme' => 'github-light',
            'title' => ['font' => '', 'color' => 'text-stone-700', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-stone-600', 'size' => 'text-lg'],
        ],
        'night' => [
            'background' => 'bg-slate-900 text-slate-200',
            'highlight_theme' => 'github-dark',
            'title' => ['font' => '', 'color' => 'text-slate-200', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-slate-300', 'size' => 'text-lg'],
        ],
        'serif' => [
            'background' => 'bg-stone-100 text-stone-800',
            'highlight_theme' => 'github-light',
            'title' => ['font' => '', 'color' => 'text-stone-800', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-stone-600', 'size' => 'text-lg'],
        ],
        'simple' => [
            'background' => 'bg-zinc-50 text-zinc-800',
            'highlight_theme' => 'github-light',
            'title' => ['font' => '', 'color' => 'text-zinc-800', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-zinc-600', 'size' => 'text-lg'],
        ],
        'solarized' => [
            'background' => 'bg-yellow-50 text-slate-600',
            'highlight_theme' => 'solarized-light',
            'title' => ['font' => '', 'color' => 'text-slate-700', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-slate-600', 'size' => 'text-lg'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Font Loading
    |--------------------------------------------------------------------------
    |
    | Map font family names used in theme title/text.font to their loading
    | strategy. System fonts require no loading; Google Fonts families are
    | loaded via <link> tag automatically.
    |
    | Example:
    |   'fonts' => [
    |       'Inter'        => ['source' => 'google', 'weights' => [400, 600, 700]],
    |       'Georgia'      => ['source' => 'system'],
    |   ],
    |
    */
    'fonts' => [],

    'pdf' => [
        'format' => 'a4',
        'orientation' => 'portrait',
        'include_notes' => false,
    ],
];
