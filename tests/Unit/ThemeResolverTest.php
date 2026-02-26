<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\ThemeResolver;

it('builds background class map from nested theme config', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->backgroundClassMap();

    expect($map)->toHaveKey('default')
        ->and($map['default'])->toContain('bg-gradient-to-br')
        ->and($map)->toHaveKey('white')
        ->and($map['white'])->toContain('bg-white');
});

it('builds typography class map with title and text strings', function (): void {
    $resolver = app(ThemeResolver::class);
    $map = $resolver->typographyClassMap();

    expect($map)->toHaveKey('default')
        ->and($map['default'])->toHaveKeys(['title', 'text'])
        ->and($map['default']['title'])->toContain('text-slate-50')
        ->and($map['default']['text'])->toContain('text-slate-200');
});

it('returns null google fonts url when no google fonts are configured', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->googleFontsUrl())->toBeNull();
});

it('builds google fonts url when google fonts are configured', function (): void {
    config()->set('slidewire.fonts', [
        'Inter' => ['source' => 'google', 'weights' => [400, 600, 700]],
        'Georgia' => ['source' => 'system'],
    ]);

    $resolver = app(ThemeResolver::class);
    $url = $resolver->googleFontsUrl();

    expect($url)->toContain('fonts.googleapis.com')
        ->and($url)->toContain('Inter')
        ->and($url)->toContain('400;600;700')
        ->and($url)->not->toContain('Georgia');
});

it('extracts slide themes from effective slides', function (): void {
    $resolver = app(ThemeResolver::class);

    $slides = [
        ['effective' => ['theme' => 'night']],
        ['effective' => ['theme' => null]],
        ['effective' => ['theme' => 'white']],
    ];

    expect($resolver->slideThemes($slides))->toBe(['night', null, 'white']);
});

it('detects vertical slides in grid shape', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->hasVerticalSlides([1, 1, 1]))->toBeFalse()
        ->and($resolver->hasVerticalSlides([1, 3, 1]))->toBeTrue()
        ->and($resolver->hasVerticalSlides([]))->toBeFalse();
});

// ========================================================================
// Typography CSS generation tests
// ========================================================================

it('generates typography CSS for all built-in themes', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    expect($css)->toBeString()
        ->and($css)->not->toBeEmpty();

    // Every built-in theme should have title and text rules
    $builtInThemes = ['default', 'black', 'white', 'league', 'beige', 'night', 'serif', 'simple', 'solarized'];

    foreach ($builtInThemes as $theme) {
        expect($css)->toContain(".slidewire-theme-{$theme} .slidewire-content h1")
            ->and($css)->toContain(".slidewire-theme-{$theme} .slidewire-content {");
    }
});

it('generates correct CSS color values for title typography', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // Default theme: title color is text-slate-50 => #f8fafc
    expect($css)->toContain('color: #f8fafc');

    // White theme: title color is text-zinc-800 => #27272a
    expect($css)->toContain('color: #27272a');

    // Night theme: title color is text-slate-200 => #e2e8f0
    expect($css)->toContain('color: #e2e8f0');
});

it('generates correct CSS font-size values for title typography', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // All built-in themes use text-4xl for title => 2.25rem
    expect($css)->toContain('font-size: 2.25rem');
});

it('generates correct CSS font-size values for text typography', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // All built-in themes use text-lg for text => 1.125rem
    expect($css)->toContain('font-size: 1.125rem');
});

it('generates CSS targeting heading elements for title typography', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // Title styles should target all heading elements
    expect($css)->toContain('.slidewire-content h1')
        ->and($css)->toContain('.slidewire-content h2')
        ->and($css)->toContain('.slidewire-content h3')
        ->and($css)->toContain('.slidewire-content h4')
        ->and($css)->toContain('.slidewire-content h5')
        ->and($css)->toContain('.slidewire-content h6');
});

it('generates CSS targeting .slidewire-content for text typography', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // Text styles are applied to the content wrapper itself (cascading to p, li, span, etc.)
    expect($css)->toContain('.slidewire-content {');
});

it('includes font-family CSS when theme specifies a font class', function (): void {
    config()->set('slidewire.themes.custom-test', [
        'background' => 'bg-black text-white',
        'highlight_theme' => 'github-dark',
        'title' => ['font' => 'font-serif', 'color' => 'text-white', 'size' => 'text-5xl'],
        'text' => ['font' => 'font-mono', 'color' => 'text-white', 'size' => 'text-base'],
    ]);

    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    expect($css)->toContain('.slidewire-theme-custom-test .slidewire-content h1')
        ->and($css)->toContain("font-family: ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif")
        ->and($css)->toContain("font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace");
});

it('includes custom font-family with sans-serif fallback', function (): void {
    config()->set('slidewire.themes.custom-font', [
        'background' => 'bg-black text-white',
        'highlight_theme' => 'github-dark',
        'title' => ['font' => 'Inter', 'color' => 'text-white', 'size' => 'text-4xl'],
        'text' => ['font' => 'Georgia', 'color' => 'text-white', 'size' => 'text-lg'],
    ]);

    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    expect($css)->toContain("font-family: 'Inter', sans-serif")
        ->and($css)->toContain("font-family: 'Georgia', sans-serif");
});

it('omits font-family CSS when theme font is empty', function (): void {
    // Create a theme where font is empty
    config()->set('slidewire.themes', [
        'nofont' => [
            'background' => 'bg-black text-white',
            'highlight_theme' => 'github-dark',
            'title' => ['font' => '', 'color' => 'text-white', 'size' => 'text-4xl'],
            'text' => ['font' => '', 'color' => 'text-white', 'size' => 'text-lg'],
        ],
    ]);

    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    expect($css)->not->toContain('font-family');
});

it('generates different color values for different theme text elements', function (): void {
    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // Default theme: title=text-slate-50=#f8fafc, text=text-slate-200=#e2e8f0
    // Both should appear in the CSS
    expect($css)->toContain('#f8fafc')
        ->and($css)->toContain('#e2e8f0');

    // Beige theme: title=text-stone-700=#44403c, text=text-stone-600=#57534e
    expect($css)->toContain('#44403c')
        ->and($css)->toContain('#57534e');
});

// ========================================================================
// Individual resolver method tests
// ========================================================================

it('resolves Tailwind text-color classes to hex values', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->resolveTextColor('text-white'))->toBe('#ffffff')
        ->and($resolver->resolveTextColor('text-black'))->toBe('#000000')
        ->and($resolver->resolveTextColor('text-slate-50'))->toBe('#f8fafc')
        ->and($resolver->resolveTextColor('text-slate-900'))->toBe('#0f172a')
        ->and($resolver->resolveTextColor('text-zinc-800'))->toBe('#27272a')
        ->and($resolver->resolveTextColor('text-stone-600'))->toBe('#57534e')
        ->and($resolver->resolveTextColor('text-neutral-300'))->toBe('#d4d4d4')
        ->and($resolver->resolveTextColor('text-blue-400'))->toBe('#60a5fa')
        ->and($resolver->resolveTextColor('text-amber-700'))->toBe('#b45309')
        ->and($resolver->resolveTextColor('text-unknown'))->toBeNull();
});

it('resolves Tailwind text-size classes to CSS font-size values', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->resolveTextSize('text-xs'))->toBe('0.75rem')
        ->and($resolver->resolveTextSize('text-sm'))->toBe('0.875rem')
        ->and($resolver->resolveTextSize('text-base'))->toBe('1rem')
        ->and($resolver->resolveTextSize('text-lg'))->toBe('1.125rem')
        ->and($resolver->resolveTextSize('text-xl'))->toBe('1.25rem')
        ->and($resolver->resolveTextSize('text-2xl'))->toBe('1.5rem')
        ->and($resolver->resolveTextSize('text-3xl'))->toBe('1.875rem')
        ->and($resolver->resolveTextSize('text-4xl'))->toBe('2.25rem')
        ->and($resolver->resolveTextSize('text-5xl'))->toBe('3rem')
        ->and($resolver->resolveTextSize('text-6xl'))->toBe('3.75rem')
        ->and($resolver->resolveTextSize('text-7xl'))->toBe('4.5rem')
        ->and($resolver->resolveTextSize('text-8xl'))->toBe('6rem')
        ->and($resolver->resolveTextSize('text-9xl'))->toBe('8rem')
        ->and($resolver->resolveTextSize('text-unknown'))->toBeNull();
});

it('resolves Tailwind font-family classes to CSS font-family values', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->resolveFontFamily('font-sans'))->toContain('ui-sans-serif')
        ->and($resolver->resolveFontFamily('font-serif'))->toContain('ui-serif')
        ->and($resolver->resolveFontFamily('font-mono'))->toContain('ui-monospace')
        ->and($resolver->resolveFontFamily('font-unknown'))->toBeNull()
        ->and($resolver->resolveFontFamily(''))->toBeNull();
});

it('resolves custom font names with sans-serif fallback', function (): void {
    $resolver = app(ThemeResolver::class);

    expect($resolver->resolveFontFamily('Inter'))->toBe("'Inter', sans-serif")
        ->and($resolver->resolveFontFamily('JetBrains Mono'))->toBe("'JetBrains Mono', sans-serif")
        ->and($resolver->resolveFontFamily('Roboto'))->toBe("'Roboto', sans-serif");
});

it('returns empty string for non-array theme entries in typographyCss', function (): void {
    config()->set('slidewire.themes', ['legacy' => 'bg-black text-white']);

    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    expect($css)->not->toContain('slidewire-theme-legacy');
});

it('handles themes with all typography fields populated', function (): void {
    config()->set('slidewire.themes.full', [
        'background' => 'bg-slate-900 text-white',
        'highlight_theme' => 'github-dark',
        'title' => ['font' => 'font-sans', 'color' => 'text-blue-400', 'size' => 'text-5xl'],
        'text' => ['font' => 'font-serif', 'color' => 'text-slate-300', 'size' => 'text-xl'],
    ]);

    $resolver = app(ThemeResolver::class);
    $css = $resolver->typographyCss();

    // Title: font-sans, text-blue-400=#60a5fa, text-5xl=3rem
    expect($css)->toContain('.slidewire-theme-full .slidewire-content h1')
        ->and($css)->toContain('font-family: ui-sans-serif')
        ->and($css)->toContain('color: #60a5fa')
        ->and($css)->toContain('font-size: 3rem');

    // Text: font-serif, text-slate-300=#cbd5e1, text-xl=1.25rem
    expect($css)->toContain('.slidewire-theme-full .slidewire-content {')
        ->and($css)->toContain('color: #cbd5e1')
        ->and($css)->toContain('font-size: 1.25rem');
});
