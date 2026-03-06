<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

/**
 * Resolves effective runtime settings for each slide using the three-level precedence chain:
 *
 *   slide_meta > deck_meta > config slides
 *
 * @phpstan-type SlideMeta array<string, string>
 * @phpstan-type SlideData array{id: string, html: string, meta: SlideMeta, fragments: int, class: string, h: int, v: int}
 * @phpstan-type EffectiveSlideData array{id: string, html: string, meta: SlideMeta, fragments: int, class: string, h: int, v: int, effective: array<string, string|null>}
 * @phpstan-type DeckMeta array<string, string>
 * @phpstan-type SlidesConfig array{theme?: mixed, show_controls?: mixed, show_progress?: mixed, show_fullscreen_button?: mixed, keyboard?: mixed, touch?: mixed, transition?: mixed, transition_duration?: mixed, transition_speed?: mixed, auto_slide?: mixed, auto_slide_pause_on_interaction?: mixed, highlight?: array{theme?: mixed}}
 */
class EffectiveSettingsResolver
{
    /**
     * Runtime keys resolved through the precedence chain.
     *
     * @var list<string>
     */
    public const array RUNTIME_KEYS = [
        'theme', 'transition', 'transition_speed', 'transition_duration',
        'auto_slide', 'auto_slide_pause_on_interaction',
        'show_controls', 'show_progress', 'show_fullscreen_button',
        'keyboard', 'touch',
    ];

    /**
     * Resolve effective settings for a list of slides given deck-level metadata.
     *
     * @param  array<int, SlideData>  $slides
     * @param  DeckMeta  $deckMeta
     * @return array<int, EffectiveSlideData>
     */
    public function resolve(array $slides, array $deckMeta): array
    {
        $slidesConfig = config(ConfigKeys::SLIDES, []);

        return array_values(array_map(
            fn (array $slide): array => $this->resolveSlide($slide, $deckMeta, $slidesConfig),
            $slides,
        ));
    }

    /**
     * Resolve effective settings for a single slide.
     *
     * @param  SlideData  $slide
     * @param  DeckMeta  $deckMeta
     * @param  SlidesConfig  $slidesConfig
     * @return EffectiveSlideData
     */
    protected function resolveSlide(array $slide, array $deckMeta, array $slidesConfig): array
    {
        $slideMeta = $slide['meta'];
        $effective = [];

        foreach (self::RUNTIME_KEYS as $key) {
            $effective[$key] = $slideMeta[$key]
            ?? $deckMeta[$key]
            ?? (isset($slidesConfig[$key]) ? (string) $slidesConfig[$key] : null);
        }

        // Highlight theme resolution: slide > deck > config
        $effective['highlight_theme'] = $slideMeta['highlight_theme']
            ?? $deckMeta['highlight_theme']
            ?? (string) ($slidesConfig['highlight']['theme'] ?? 'github-dark');

        $slide['effective'] = $effective;

        return $slide;
    }
}
