<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use WendellAdriel\SlideWire\DTOs\Slide;

class SlideViewDataFactory
{
    /**
     * @param  array<string, mixed>  $deckMeta
     */
    public function resolveDeckFlag(array $deckMeta, string $key, bool $default): bool
    {
        $value = $deckMeta[$key] ?? $default;

        return $value !== 'false' && $value !== false;
    }

    /**
     * @param  array<int, Slide>  $slides
     * @param  array<string, array{title: string, text: string}>  $themeTypography
     * @param  array<string, string>  $configuredThemes
     * @return array<int, array{slide: Slide, theme_name: string, text_typography: string, slide_theme_class: string, background_theme_class: string, background_image: ?string, background_video: ?string, style: string, video_loops: bool, video_muted: bool}>
     */
    public function buildSlideFrames(array $slides, array $themeTypography, array $configuredThemes = [], string $defaultTheme = 'default'): array
    {
        return array_values(array_map(
            fn (Slide $slide): array => $this->buildSlideFrame($slide, $themeTypography, $configuredThemes, $defaultTheme),
            $slides,
        ));
    }

    /**
     * @param  array<int, Slide>  $slides
     * @return array{transition_durations: array<int, int>, auto_slides: array<int, int>, coords: array<int, array{h: int, v: int}>}
     */
    public function buildDeckPayload(array $slides): array
    {
        return [
            'transition_durations' => array_values(array_map(
                fn (Slide $slide): int => (int) ($slide->effective['transition_duration'] ?? 350),
                $slides,
            )),
            'auto_slides' => array_values(array_map(
                fn (Slide $slide): int => (int) ($slide->effective['auto_slide'] ?? 0),
                $slides,
            )),
            'coords' => array_values(array_map(
                fn (Slide $slide): array => ['h' => $slide->h, 'v' => $slide->v],
                $slides,
            )),
        ];
    }

    /**
     * @param  array<string, array{title: string, text: string}>  $themeTypography
     * @param  array<string, string>  $configuredThemes
     * @return array{slide: Slide, theme_name: string, text_typography: string, slide_theme_class: string, background_theme_class: string, background_image: ?string, background_video: ?string, style: string, video_loops: bool, video_muted: bool}
     */
    protected function buildSlideFrame(Slide $slide, array $themeTypography, array $configuredThemes, string $defaultTheme): array
    {
        $meta = $slide->meta;
        $themeName = (string) ($slide->effective['theme'] ?? $defaultTheme);
        $backgroundImage = $this->backgroundImage($meta);

        return [
            'slide' => $slide,
            'theme_name' => $themeName,
            'text_typography' => $themeTypography[$themeName]['text'] ?? '',
            'slide_theme_class' => $themeName === '' ? '' : "slidewire-theme-{$themeName}",
            'background_theme_class' => $configuredThemes[$themeName] ?? '',
            'background_image' => $backgroundImage,
            'background_video' => $this->stringOrNull($meta['background_video'] ?? null),
            'style' => $this->backgroundStyle($meta, $backgroundImage),
            'video_loops' => ($meta['background_video_loop'] ?? 'true') !== 'false',
            'video_muted' => ($meta['background_video_muted'] ?? 'true') !== 'false',
        ];
    }

    /**
     * @param  array<string, string>  $meta
     */
    protected function backgroundImage(array $meta): ?string
    {
        $backgroundImage = $this->stringOrNull($meta['background_image'] ?? null);
        $rawBackground = $this->stringOrNull($meta['background'] ?? null);

        if ($backgroundImage !== null) {
            return $backgroundImage;
        }

        if ($rawBackground === null) {
            return null;
        }

        return preg_match('/^(https?:|\/|\.\/|\.\.\/)/', $rawBackground) === 1
            ? $rawBackground
            : null;
    }

    /**
     * @param  array<string, string>  $meta
     */
    protected function backgroundStyle(array $meta, ?string $backgroundImage): string
    {
        $styles = [];

        if ($backgroundImage !== null) {
            $styles[] = "background-image: url({$backgroundImage})";
            $styles[] = 'background-size: ' . ($meta['background_size'] ?? 'cover');
            $styles[] = 'background-position: ' . ($meta['background_position'] ?? 'center');
            $styles[] = 'background-repeat: ' . ($meta['background_repeat'] ?? 'no-repeat');
        }

        if (($meta['background_opacity'] ?? '') !== '') {
            $styles[] = '--slidewire-background-opacity: ' . $meta['background_opacity'];
        }

        return implode(';', $styles);
    }

    protected function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return $value;
    }
}
