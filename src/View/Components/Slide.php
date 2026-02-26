<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Slide extends Component
{
    public function __construct(
        public ?string $transition = null,
        public ?string $transitionSpeed = null,
        public ?string $background = null,
        public ?string $backgroundImage = null,
        public ?string $backgroundVideo = null,
        public ?string $backgroundVideoLoop = null,
        public ?string $backgroundVideoMuted = null,
        public ?string $backgroundSize = null,
        public ?string $backgroundPosition = null,
        public ?string $backgroundRepeat = null,
        public ?string $backgroundOpacity = null,
        public ?string $backgroundTransition = null,
        public ?string $autoAnimate = null,
        public ?string $autoAnimateDuration = null,
        public ?string $autoAnimateEasing = null,
        public ?string $autoSlide = null,
        public ?string $theme = null,
    ) {
        $this->transition ??= (string) config('slidewire.defaults.transition', 'slide');
    }

    public function render(): View|Closure|string
    {
        return view('slidewire::components.slide');
    }
}
