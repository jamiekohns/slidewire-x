<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Phiki\Theme\Theme;
use Stringable;

final readonly class ThemeConfig implements Stringable
{
    public function __construct(
        public string $background,
        public Theme $highlightTheme,
        public ThemeFont $title,
        public ThemeFont $text,
    ) {}

    public function __toString(): string
    {
        return $this->background;
    }
}
