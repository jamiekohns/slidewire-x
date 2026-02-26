<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use WendellAdriel\SlideWire\Support\EffectiveSettingsResolver;
use WendellAdriel\SlideWire\Support\PresentationCompiler;
use WendellAdriel\SlideWire\Support\ThemeResolver;

#[Layout('slidewire::layouts.blank')]
class PresentationDeck extends Component
{
    #[Locked]
    public string $presentation = 'index';

    /**
     * 2D grid of slides: columns[horizontal][vertical].
     *
     * @var array<int, array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>>
     */
    public array $columns = [];

    /**
     * Flattened slide list for linear indexing and rendering.
     *
     * @var array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string, h: int, v: int}>
     */
    public array $slides = [];

    /**
     * @var array<string, string>
     */
    public array $deckMeta = [];

    /**
     * Grid structure: number of vertical slides per column.
     *
     * @var array<int, int>
     */
    public array $gridShape = [];

    public int $activeIndex = 0;

    public int $activeFragment = -1;

    public function mount(string $presentation = 'index', ?int $startSlide = null): void
    {
        $this->presentation = trim($presentation, '/');
        $compiled = app(PresentationCompiler::class)->compile($this->presentation);
        $this->deckMeta = $compiled['deck_meta'];
        $this->columns = $compiled['slides'];

        // Flatten 2D grid to linear list with h/v coordinates
        $flat = [];
        $gridShape = [];

        foreach ($this->columns as $h => $column) {
            $gridShape[] = count($column);

            foreach ($column as $v => $entry) {
                $entry['h'] = $h;
                $entry['v'] = $v;
                $flat[] = $entry;
            }
        }

        $this->slides = $flat;
        $this->gridShape = $gridShape;

        abort_if($this->slides === [], 404, "SlideWire presentation [{$this->presentation}] was not found.");

        if ($startSlide !== null) {
            $this->activeIndex = $this->normalizeIndex($startSlide);
        }
    }

    public function nextSlide(): void
    {
        $fragmentCount = $this->currentSlide()['fragments'] ?? 0;

        if ($fragmentCount > 0 && $this->activeFragment < $fragmentCount - 1) {
            ++$this->activeFragment;

            return;
        }

        $this->activeFragment = -1;
        $this->activeIndex = min($this->activeIndex + 1, count($this->slides) - 1);
    }

    public function previousSlide(): void
    {
        if ($this->activeFragment > -1) {
            --$this->activeFragment;

            return;
        }

        $this->activeIndex = max($this->activeIndex - 1, 0);
        $this->activeFragment = -1;
    }

    public function goToSlide(int $index): void
    {
        $this->activeIndex = $this->normalizeIndex($index);
        $this->activeFragment = -1;
    }

    /**
     * Navigate down within a vertical stack.
     */
    public function navigateDown(): void
    {
        $current = $this->currentSlide();
        $h = $current['h'];
        $v = $current['v'];
        $maxV = ($this->gridShape[$h] ?? 1) - 1;

        if ($v >= $maxV) {
            return;
        }

        $targetIndex = $this->findFlatIndex($h, $v + 1);

        if ($targetIndex !== null) {
            $this->activeFragment = -1;
            $this->activeIndex = $targetIndex;
        }
    }

    /**
     * Navigate up within a vertical stack.
     */
    public function navigateUp(): void
    {
        $current = $this->currentSlide();
        $h = $current['h'];
        $v = $current['v'];

        if ($v <= 0) {
            return;
        }

        $targetIndex = $this->findFlatIndex($h, $v - 1);

        if ($targetIndex !== null) {
            $this->activeFragment = -1;
            $this->activeIndex = $targetIndex;
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $settingsResolver = app(EffectiveSettingsResolver::class);
        $themeResolver = app(ThemeResolver::class);

        $effectiveSlides = $settingsResolver->resolve($this->slides, $this->deckMeta);
        $configuredThemes = $themeResolver->backgroundClassMap();
        $themeTypography = $themeResolver->typographyClassMap();
        $googleFontsUrl = $themeResolver->googleFontsUrl();
        $codeFontFamily = $themeResolver->codeFontFamily();
        $slideThemes = $themeResolver->slideThemes($effectiveSlides);
        $hasVerticalSlides = $themeResolver->hasVerticalSlides($this->gridShape);

        return view('slidewire::livewire.presentation-deck', [
            'effectiveSlides' => $effectiveSlides,
            'configuredThemes' => $configuredThemes,
            'themeTypography' => $themeTypography,
            'googleFontsUrl' => $googleFontsUrl,
            'codeFontFamily' => $codeFontFamily,
            'slideThemes' => $slideThemes,
            'hasVerticalSlides' => $hasVerticalSlides,
        ]);
    }

    /**
     * @return array{id: string, html: string, meta: array<string, string>, fragments: int, class: string, h: int, v: int}
     */
    protected function currentSlide(): array
    {
        return $this->slides[$this->activeIndex];
    }

    protected function normalizeIndex(int $index): int
    {
        return max(0, min($index, count($this->slides) - 1));
    }

    protected function findFlatIndex(int $h, int $v): ?int
    {
        return array_find_key($this->slides, fn (array $slide): bool => $slide['h'] === $h && $slide['v'] === $v);
    }
}
