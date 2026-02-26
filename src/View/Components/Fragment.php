<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Fragment extends Component
{
    public function __construct(public ?int $index = null) {}

    public function render(): View|Closure|string
    {
        return view('slidewire::components.fragment');
    }
}
