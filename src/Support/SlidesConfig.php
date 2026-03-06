<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

final readonly class SlidesConfig
{
    public function __construct(
        public string $theme = 'default',
        public bool $showControls = true,
        public bool $showProgress = true,
        public bool $showFullscreenButton = true,
        public bool $keyboard = true,
        public bool $touch = true,
        public SlideTransition $transition = SlideTransition::Slide,
        public int $transitionDuration = 350,
        public SlideTransitionSpeed $transitionSpeed = SlideTransitionSpeed::Default,
        public int $autoSlide = 0,
        public bool $autoSlidePauseOnInteraction = true,
        public HighlightConfig $highlight = new HighlightConfig(),
    ) {}
}
