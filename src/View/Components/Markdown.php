<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use WendellAdriel\SlideWire\Support\CodeHighlighter;

class Markdown extends Component
{
    public function __construct(protected CodeHighlighter $highlighter) {}

    public function render(): View|Closure|string
    {
        return view('slidewire::components.markdown');
    }

    public function toHtml(string $markdown): string
    {
        $withHighlightedCode = $this->highlighter->replaceCodeBlocks($markdown);

        return Str::markdown($withHighlightedCode);
    }
}
