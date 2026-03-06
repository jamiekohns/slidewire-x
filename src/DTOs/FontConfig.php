<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\DTOs;

use Stringable;
use WendellAdriel\SlideWire\Enums\FontSource;

final readonly class FontConfig implements Stringable
{
    /**
     * @param  list<int>  $weights
     */
    public function __construct(
        public FontSource $source,
        public array $weights = [],
    ) {}

    public function __toString(): string
    {
        return $this->source->value;
    }

    public function isGoogle(): bool
    {
        return $this->source === FontSource::Google;
    }
}
