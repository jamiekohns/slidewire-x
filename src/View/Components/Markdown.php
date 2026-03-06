<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use WendellAdriel\SlideWire\Support\CodeBlockPrecompiler;
use WendellAdriel\SlideWire\Support\CodeHighlighter;
use WendellAdriel\SlideWire\Support\SlideContext;

class Markdown extends Component
{
    public function __construct(
        protected CodeHighlighter $highlighter,
        protected SlideContext $context,
        public ?string $size = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('slidewire::components.markdown');
    }

    public function toHtml(string $markdown): string
    {
        $markdown = CodeBlockPrecompiler::decode($markdown);

        $withHighlightedCode = $this->highlighter->replaceCodeBlocks(
            $markdown,
            $this->context->highlightTheme(),
            $this->context->presentationTheme(),
            null,
            $this->size,
        );

        return Str::markdown($withHighlightedCode);
    }
}
