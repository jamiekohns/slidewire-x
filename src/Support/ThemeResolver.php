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
}
