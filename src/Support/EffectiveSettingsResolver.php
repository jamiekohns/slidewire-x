<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

/**
 * Resolves effective runtime settings for each slide using the three-level precedence chain:
 *
 *   slide_meta > deck_meta > config defaults
 */
class EffectiveSettingsResolver
{
    /**
     * Runtime keys resolved through the precedence chain.
     *
     * @var list<string>
     */
    public const RUNTIME_KEYS = [
        'theme', 'transition', 'transition_speed', 'transition_duration',
        'auto_slide', 'auto_slide_pause_on_interaction',
        'show_controls', 'show_progress', 'show_fullscreen_button',
        'keyboard', 'touch',
    ];

    /**
     * Resolve effective settings for a list of slides given deck-level metadata.
     *
     * @param  array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string, h: int, v: int}>  $slides
     * @param  array<string, string>  $deckMeta
     * @return array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string, h: int, v: int, effective: array<string, string|null>}>
     */
    public function resolve(array $slides, array $deckMeta): array
    {
        $defaults = config('slidewire.defaults', []);

        return array_values(array_map(
            fn (array $slide): array => $this->resolveSlide($slide, $deckMeta, $defaults),
            $slides,
        ));
    }

    /**
     * Resolve effective settings for a single slide.
     *
     * @param  array<string, string>  $deckMeta
     * @param  array<string, mixed>  $defaults
     * @return array{id: string, html: string, meta: array<string, string>, fragments: int, class: string, h: int, v: int, effective: array<string, string|null>}
     */
    protected function resolveSlide(array $slide, array $deckMeta, array $defaults): array
    {
        $slideMeta = $slide['meta'];
        $effective = [];

        foreach (self::RUNTIME_KEYS as $key) {
            $effective[$key] = $slideMeta[$key]
                ?? $deckMeta[$key]
                ?? (isset($defaults[$key]) ? (string) $defaults[$key] : null);
        }

        // Highlight theme resolution: slide > deck > config
        $effective['highlight_theme'] = $slideMeta['highlight_theme']
            ?? $deckMeta['highlight_theme']
            ?? (string) ($defaults['highlight']['theme'] ?? 'github-dark');

        $slide['effective'] = $effective;

        return $slide;
    }
}
