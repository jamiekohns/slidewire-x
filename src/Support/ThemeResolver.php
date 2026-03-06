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
     * Build a map of theme name => background class string.
     *
     * @return array<string, string>
     */
    public function backgroundClassMap(): array
    {
        return collect(config('slidewire.themes', []))
            ->map(fn (ThemeConfig $theme): string => (string) $theme)
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
            ->map(fn (ThemeConfig $theme): array => [
                'title' => (string) $theme->title,
                'text' => (string) $theme->text,
            ])
            ->all();
    }

    /**
     * Build the CSS font-family value for code blocks from configured Google fonts.
     *
     * Uses the first font in the 'fonts' config that has a name containing "Mono"
     * or falls back to the system monospace stack.
     */
    public function codeFontFamily(): string
    {
        $slides = config('slidewire.slides', new SlidesConfig());
        $font = $slides->highlight->font;
        $fonts = config('slidewire.fonts', []);

        if ($font === '' || ! array_key_exists($font, $fonts)) {
            return $this->resolveFontStack();
        }

        return $this->resolveFontStack($font);
    }

    /**
     * Build a Google Fonts URL from configured font families, or null if no Google fonts.
     */
    public function googleFontsUrl(): ?string
    {
        $fontConfig = config('slidewire.fonts', []);

        $googleFontFamilies = collect($fontConfig)
            ->filter(fn (FontConfig $config): bool => $config->isGoogle())
            ->map(function (FontConfig $font, string $family): string {
                $weights = $font->weights !== [] ? $font->weights : [400];
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

    public function resolveFontStack(?string $font = null): string
    {
        $fallback = "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace";
        $font = trim((string) $font);

        if ($font === '') {
            return $fallback;
        }

        return "'{$font}', {$fallback}";
    }

    /**
     * Extract the per-slide theme list from effective slides.
     *
     * @param  array<int, Slide>  $effectiveSlides
     * @return array<int, string|null>
     */
    public function slideThemes(array $effectiveSlides): array
    {
        return array_values(array_map(
            fn (Slide $slide): ?string => $slide->effective['theme'] ?? null,
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
}
