<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Phiki\Theme\Theme;

/**
 * Tracks the current deck/slide context during Blade compilation so that
 * nested components (e.g. Markdown) can inherit theme settings.
 */
class SlideContext
{
    private ?string $deckTheme = null;

    private ?Theme $deckHighlightTheme = null;

    private ?string $slideTheme = null;

    public function setDeck(?string $theme, Theme|string|null $highlightTheme): void
    {
        $this->deckTheme = $theme;
        $this->deckHighlightTheme = $highlightTheme instanceof Theme
            ? $highlightTheme
            : Theme::tryFrom((string) $highlightTheme);
    }

    public function clearDeck(): void
    {
        $this->deckTheme = null;
        $this->deckHighlightTheme = null;
    }

    public function setSlide(?string $theme): void
    {
        $this->slideTheme = $theme;
    }

    public function clearSlide(): void
    {
        $this->slideTheme = null;
    }

    /**
     * Get the effective presentation theme for the current context.
     *
     * Slide theme overrides deck theme.
     */
    public function presentationTheme(): ?string
    {
        return $this->slideTheme ?? $this->deckTheme;
    }

    /**
     * Get the explicit highlight theme from the deck, if set.
     */
    public function highlightTheme(): ?Theme
    {
        return $this->deckHighlightTheme;
    }
}
