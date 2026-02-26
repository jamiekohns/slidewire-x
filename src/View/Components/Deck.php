<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Deck extends Component
{
    public function __construct(
        public ?string $theme = null,
        public ?string $transition = null,
        public ?string $transitionSpeed = null,
        public ?string $transitionDuration = null,
        public ?string $autoSlide = null,
        public ?string $autoSlidePauseOnInteraction = null,
        public ?string $showControls = null,
        public ?string $showProgress = null,
        public ?string $showFullscreenButton = null,
        public ?string $keyboard = null,
        public ?string $touch = null,
        public ?string $highlightTheme = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('slidewire::components.deck');
    }
}
