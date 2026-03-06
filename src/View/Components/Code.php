<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WendellAdriel\SlideWire\Support\CodeBlockPrecompiler;
use WendellAdriel\SlideWire\Support\CodeHighlighter;
use WendellAdriel\SlideWire\Support\SlideContext;

class Code extends Component
{
    public function __construct(
        protected CodeHighlighter $highlighter,
        protected SlideContext $context,
        public string $language = 'text',
        public ?string $theme = null,
        public ?string $font = null,
        public ?string $size = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('slidewire::components.code');
    }

    public function toHtml(string $code): string
    {
        $code = CodeBlockPrecompiler::decode($code);
        $code = (string) preg_replace('/^\r?\n/', '', $code, 1);
        $code = rtrim($code);

        return $this->highlighter->highlight(
            $code,
            $this->language,
            $this->theme ?? $this->context->highlightTheme(),
            $this->context->presentationTheme(),
            $this->font,
            $this->size,
        )->toHtml();
    }
}
