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
            'theme' => 'catppuccin-mocha',
        ],
    ],

    'themes' => [
        'default' => [
            'background' => 'bg-gradient-to-br from-slate-900 via-blue-950 to-slate-950 text-slate-50',
            'highlight_theme' => 'catppuccin-mocha',
            'title' => ['font' => 'Inter', 'color' => 'text-slate-50', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-slate-200', 'size' => 'text-lg'],
        ],
        'black' => [
            'background' => 'bg-zinc-950 text-zinc-100',
            'highlight_theme' => 'catppuccin-mocha',
            'title' => ['font' => 'Inter', 'color' => 'text-zinc-100', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-zinc-300', 'size' => 'text-lg'],
        ],
        'white' => [
            'background' => 'bg-white text-zinc-800',
            'highlight_theme' => 'catppuccin-latte',
            'title' => ['font' => 'Inter', 'color' => 'text-zinc-800', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-zinc-600', 'size' => 'text-lg'],
        ],
        'league' => [
            'background' => 'bg-neutral-800 text-neutral-100',
            'highlight_theme' => 'catppuccin-mocha',
            'title' => ['font' => 'Inter', 'color' => 'text-neutral-100', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-neutral-300', 'size' => 'text-lg'],
        ],
        'beige' => [
            'background' => 'bg-amber-50 text-stone-700',
            'highlight_theme' => 'catppuccin-latte',
            'title' => ['font' => 'Inter', 'color' => 'text-stone-700', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-stone-600', 'size' => 'text-lg'],
        ],
        'night' => [
            'background' => 'bg-slate-900 text-slate-200',
            'highlight_theme' => 'catppuccin-mocha',
            'title' => ['font' => 'Inter', 'color' => 'text-slate-200', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-slate-300', 'size' => 'text-lg'],
        ],
        'serif' => [
            'background' => 'bg-stone-100 text-stone-800',
            'highlight_theme' => 'catppuccin-latte',
            'title' => ['font' => 'Inter', 'color' => 'text-stone-800', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-stone-600', 'size' => 'text-lg'],
        ],
        'simple' => [
            'background' => 'bg-zinc-50 text-zinc-800',
            'highlight_theme' => 'catppuccin-latte',
            'title' => ['font' => 'Inter', 'color' => 'text-zinc-800', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-zinc-600', 'size' => 'text-lg'],
        ],
        'solarized' => [
            'background' => 'bg-yellow-50 text-slate-600',
            'highlight_theme' => 'catppuccin-latte',
            'title' => ['font' => 'Inter', 'color' => 'text-slate-700', 'size' => 'text-4xl'],
            'text' => ['font' => 'Inter', 'color' => 'text-slate-600', 'size' => 'text-lg'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Font Loading
    |--------------------------------------------------------------------------
    |
    | Map font family names used in theme title/text.font or defaults.code_font
    | to their loading strategy. System fonts require no loading; Google Fonts
    | families are loaded via <link> tag automatically.
    |
    */
    'fonts' => [
        'Inter' => ['source' => 'google', 'weights' => [400, 600, 700]],
        'JetBrains Mono' => ['source' => 'google', 'weights' => [400, 700]],
    ],

    'pdf' => [
        'format' => 'a4',
        'orientation' => 'portrait',
        'include_notes' => false,
    ],
];
