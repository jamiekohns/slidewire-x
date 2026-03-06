<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Phiki\Theme\Theme;

final readonly class HighlightConfig
{
    public function __construct(
        public bool $enabled = true,
        public Theme $theme = Theme::CatppuccinMocha,
        public string $font = 'JetBrainsMono',
        public string $fontSize = 'md',
    ) {}
}
