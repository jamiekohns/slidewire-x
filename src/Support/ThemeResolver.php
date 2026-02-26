<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

/**
 * Resolves theme background classes, typography maps, and Google Fonts URLs
 * from the slidewire config.
 */
class ThemeResolver
{
    /**
     * Tailwind text-color class => CSS color value.
     *
     * @var array<string, string>
     */
    private const array TEXT_COLOR_MAP = [
        'text-white' => '#ffffff',
        'text-black' => '#000000',
        // Slate
        'text-slate-50' => '#f8fafc', 'text-slate-100' => '#f1f5f9', 'text-slate-200' => '#e2e8f0',
        'text-slate-300' => '#cbd5e1', 'text-slate-400' => '#94a3b8', 'text-slate-500' => '#64748b',
        'text-slate-600' => '#475569', 'text-slate-700' => '#334155', 'text-slate-800' => '#1e293b',
        'text-slate-900' => '#0f172a', 'text-slate-950' => '#020617',
        // Zinc
        'text-zinc-50' => '#fafafa', 'text-zinc-100' => '#f4f4f5', 'text-zinc-200' => '#e4e4e7',
        'text-zinc-300' => '#d4d4d8', 'text-zinc-400' => '#a1a1aa', 'text-zinc-500' => '#71717a',
        'text-zinc-600' => '#52525b', 'text-zinc-700' => '#3f3f46', 'text-zinc-800' => '#27272a',
        'text-zinc-900' => '#18181b', 'text-zinc-950' => '#09090b',
        // Neutral
        'text-neutral-50' => '#fafafa', 'text-neutral-100' => '#f5f5f5', 'text-neutral-200' => '#e5e5e5',
        'text-neutral-300' => '#d4d4d4', 'text-neutral-400' => '#a3a3a3', 'text-neutral-500' => '#737373',
        'text-neutral-600' => '#525252', 'text-neutral-700' => '#404040', 'text-neutral-800' => '#262626',
        'text-neutral-900' => '#171717', 'text-neutral-950' => '#0a0a0a',
        // Stone
        'text-stone-50' => '#fafaf9', 'text-stone-100' => '#f5f5f4', 'text-stone-200' => '#e7e5e4',
        'text-stone-300' => '#d6d3d1', 'text-stone-400' => '#a8a29e', 'text-stone-500' => '#78716c',
        'text-stone-600' => '#57534e', 'text-stone-700' => '#44403c', 'text-stone-800' => '#292524',
        'text-stone-900' => '#1c1917', 'text-stone-950' => '#0c0a09',
        // Blue
        'text-blue-50' => '#eff6ff', 'text-blue-100' => '#dbeafe', 'text-blue-200' => '#bfdbfe',
        'text-blue-300' => '#93c5fd', 'text-blue-400' => '#60a5fa', 'text-blue-500' => '#3b82f6',
        'text-blue-600' => '#2563eb', 'text-blue-700' => '#1d4ed8', 'text-blue-800' => '#1e40af',
        'text-blue-900' => '#1e3a5f', 'text-blue-950' => '#172554',
        // Amber
        'text-amber-50' => '#fffbeb', 'text-amber-100' => '#fef3c7', 'text-amber-200' => '#fde68a',
        'text-amber-300' => '#fcd34d', 'text-amber-400' => '#fbbf24', 'text-amber-500' => '#f59e0b',
        'text-amber-600' => '#d97706', 'text-amber-700' => '#b45309', 'text-amber-800' => '#92400e',
        'text-amber-900' => '#78350f', 'text-amber-950' => '#451a03',
        // Yellow
        'text-yellow-50' => '#fefce8', 'text-yellow-100' => '#fef9c3', 'text-yellow-200' => '#fef08a',
        'text-yellow-300' => '#fde047', 'text-yellow-400' => '#facc15', 'text-yellow-500' => '#eab308',
        'text-yellow-600' => '#ca8a04', 'text-yellow-700' => '#a16207', 'text-yellow-800' => '#854d0e',
        'text-yellow-900' => '#713f12', 'text-yellow-950' => '#422006',
        // Red
        'text-red-50' => '#fef2f2', 'text-red-100' => '#fee2e2', 'text-red-200' => '#fecaca',
        'text-red-300' => '#fca5a5', 'text-red-400' => '#f87171', 'text-red-500' => '#ef4444',
        'text-red-600' => '#dc2626', 'text-red-700' => '#b91c1c', 'text-red-800' => '#991b1b',
        'text-red-900' => '#7f1d1d', 'text-red-950' => '#450a0a',
        // Green
        'text-green-50' => '#f0fdf4', 'text-green-100' => '#dcfce7', 'text-green-200' => '#bbf7d0',
        'text-green-300' => '#86efac', 'text-green-400' => '#4ade80', 'text-green-500' => '#22c55e',
        'text-green-600' => '#16a34a', 'text-green-700' => '#15803d', 'text-green-800' => '#166534',
        'text-green-900' => '#14532d', 'text-green-950' => '#052e16',
    ];

    /**
     * Tailwind text-size class => CSS font-size value.
     *
     * @var array<string, string>
     */
    private const array TEXT_SIZE_MAP = [
        'text-xs' => '0.75rem', 'text-sm' => '0.875rem', 'text-base' => '1rem',
        'text-lg' => '1.125rem', 'text-xl' => '1.25rem', 'text-2xl' => '1.5rem',
        'text-3xl' => '1.875rem', 'text-4xl' => '2.25rem', 'text-5xl' => '3rem',
        'text-6xl' => '3.75rem', 'text-7xl' => '4.5rem', 'text-8xl' => '6rem',
        'text-9xl' => '8rem',
    ];

    /**
     * Tailwind font-family class => CSS font-family value.
     *
     * @var array<string, string>
     */
    private const array FONT_FAMILY_MAP = [
        'font-sans' => "ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'",
        'font-serif' => "ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif",
        'font-mono' => "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace",
    ];

    /**
     * Build a map of theme name => background class string.
     *
     * @return array<string, string>
     */
    public function backgroundClassMap(): array
    {
        return collect(config('slidewire.themes', []))
            ->map(function (mixed $theme): string {
                if (is_array($theme)) {
                    return (string) ($theme['background'] ?? '');
                }

                return (string) $theme;
            })
            ->all();
    }

    /**
     * Build a map of theme name => typography class arrays (title + text).
     *
     * @return array<string, array{title: string, text: string}>
     */
    public function typographyClassMap(): array
    {
        return collect(config('slidewire.themes', []))
            ->map(function (mixed $theme): array {
                if (! is_array($theme)) {
                    return ['title' => '', 'text' => ''];
                }

                $title = $theme['title'] ?? [];
                $text = $theme['text'] ?? [];

                return [
                    'title' => implode(' ', array_filter([
                        $title['font'] ?? '',
                        $title['color'] ?? '',
                        $title['size'] ?? '',
                    ])),
                    'text' => implode(' ', array_filter([
                        $text['font'] ?? '',
                        $text['color'] ?? '',
                        $text['size'] ?? '',
                    ])),
                ];
            })
            ->all();
    }

    /**
     * Generate CSS rules for theme typography, scoped by data-theme attribute.
     *
     * Converts Tailwind utility class names from theme config into real CSS
     * declarations so typography is applied regardless of whether the consuming
     * application has Tailwind CSS installed.
     *
     * @return string Raw CSS rules string
     */
    public function typographyCss(): string
    {
        $rules = [];

        foreach (config('slidewire.themes', []) as $themeName => $theme) {
            if (! is_array($theme)) {
                continue;
            }

            $titleCss = $this->resolveTypographyCssBlock($theme['title'] ?? []);
            $textCss = $this->resolveTypographyCssBlock($theme['text'] ?? []);

            if ($titleCss !== '') {
                $rules[] = ".slidewire-theme-{$themeName} .slidewire-content h1,\n"
                    . ".slidewire-theme-{$themeName} .slidewire-content h2,\n"
                    . ".slidewire-theme-{$themeName} .slidewire-content h3,\n"
                    . ".slidewire-theme-{$themeName} .slidewire-content h4,\n"
                    . ".slidewire-theme-{$themeName} .slidewire-content h5,\n"
                    . ".slidewire-theme-{$themeName} .slidewire-content h6 {\n{$titleCss}}";
            }

            if ($textCss !== '') {
                $rules[] = ".slidewire-theme-{$themeName} .slidewire-content {\n{$textCss}}";
            }
        }

        return implode("\n", $rules);
    }

    /**
     * Build a Google Fonts URL from configured font families, or null if no Google fonts.
     */
    public function googleFontsUrl(): ?string
    {
        $fontConfig = config('slidewire.fonts', []);

        $googleFontFamilies = collect($fontConfig)
            ->filter(fn (mixed $config): bool => is_array($config) && ($config['source'] ?? 'system') === 'google')
            ->map(function (array $config, string $family): string {
                $weights = $config['weights'] ?? [400];
                $weightStr = implode(';', array_map(intval(...), $weights));

                return urlencode($family) . ':wght@' . $weightStr;
            })
            ->values()
            ->all();

        if ($googleFontFamilies === []) {
            return null;
        }

        return 'https://fonts.googleapis.com/css2?'
            . implode('&', array_map(fn (string $f): string => 'family=' . $f, $googleFontFamilies))
            . '&display=swap';
    }

    /**
     * Extract the per-slide theme list from effective slides.
     *
     * @param  array<int, array{effective: array<string, string|null>}>  $effectiveSlides
     * @return array<int, string|null>
     */
    public function slideThemes(array $effectiveSlides): array
    {
        return array_values(array_map(
            fn (array $slide): ?string => $slide['effective']['theme'] ?? null,
            $effectiveSlides,
        ));
    }

    /**
     * Determine if the grid contains any vertical stacks.
     *
     * @param  array<int, int>  $gridShape
     */
    public function hasVerticalSlides(array $gridShape): bool
    {
        return collect($gridShape)->contains(fn (int $count): bool => $count > 1);
    }

    /**
     * Resolve a Tailwind text-color class to a CSS color value.
     */
    public function resolveTextColor(string $class): ?string
    {
        return self::TEXT_COLOR_MAP[$class] ?? null;
    }

    /**
     * Resolve a Tailwind text-size class to a CSS font-size value.
     */
    public function resolveTextSize(string $class): ?string
    {
        return self::TEXT_SIZE_MAP[$class] ?? null;
    }

    /**
     * Resolve a Tailwind font-family class to a CSS font-family value.
     * Also supports quoted custom font names (e.g. "'Inter', sans-serif").
     */
    public function resolveFontFamily(string $class): ?string
    {
        if ($class === '') {
            return null;
        }

        // Standard Tailwind font classes
        if (isset(self::FONT_FAMILY_MAP[$class])) {
            return self::FONT_FAMILY_MAP[$class];
        }

        // Custom font name (not a Tailwind class): use as-is with sans-serif fallback
        if (! str_starts_with($class, 'font-')) {
            return "'{$class}', sans-serif";
        }

        return null;
    }

    /**
     * Convert a theme typography array (font, color, size) into CSS declarations.
     *
     * @param  array{font?: string, color?: string, size?: string}  $typography
     */
    protected function resolveTypographyCssBlock(array $typography): string
    {
        $declarations = [];

        $font = $typography['font'] ?? '';
        $color = $typography['color'] ?? '';
        $size = $typography['size'] ?? '';

        if ($font !== '') {
            $resolved = $this->resolveFontFamily($font);

            if ($resolved !== null) {
                $declarations[] = "  font-family: {$resolved};";
            }
        }

        if ($color !== '') {
            $resolved = $this->resolveTextColor($color);

            if ($resolved !== null) {
                $declarations[] = "  color: {$resolved};";
            }
        }

        if ($size !== '') {
            $resolved = $this->resolveTextSize($size);

            if ($resolved !== null) {
                $declarations[] = "  font-size: {$resolved};";
            }
        }

        if ($declarations === []) {
            return '';
        }

        return implode("\n", $declarations) . "\n";
    }
}
