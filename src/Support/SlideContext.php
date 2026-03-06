<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Phiki\Theme\Theme;

// Tracks deck and slide theme context during Blade rendering.
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

    // Returns the active presentation theme.
    public function presentationTheme(): ?string
    {
        return $this->slideTheme ?? $this->deckTheme;
    }

    // Returns the deck-level highlight theme, if set.
    public function highlightTheme(): ?Theme
    {
        return $this->deckHighlightTheme;
    }
}
