<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Diagram extends Component
{
    public function __construct(
        public ?string $theme = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('slidewire::components.diagram');
    }
}
