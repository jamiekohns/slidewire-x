<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\DTOs;

use Stringable;

final readonly class ThemeFont implements Stringable
{
    public function __construct(
        public string $font,
        public string $color,
        public string $size,
    ) {}

    public function __toString(): string
    {
        return implode(' ', array_filter([$this->font, $this->color, $this->size]));
    }
}
