<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

/**
 * Blade precompiler that protects fenced code blocks inside
 * <x-slidewire::markdown> and slot content inside <x-slidewire::code>
 * from being processed by the Blade compiler.
 *
 * Without this, code blocks containing Blade component syntax
 * (e.g. <x-slidewire::deck>) would be compiled as actual components
 * instead of being treated as literal code text.
 *
 * The precompiler base64-encodes each fenced code block (or entire
 * code component slot) into a placeholder comment. The Markdown and
 * Code components restore them before highlighting.
 */
class CodeBlockPrecompiler
{
    public const string PLACEHOLDER_PREFIX = '<!--SLIDEWIRE_CODE:';

    public const string PLACEHOLDER_SUFFIX = '-->';

    /**
     * Process a Blade template, protecting code blocks inside markdown
     * components and entire slot content inside code components.
     */
    public function __invoke(string $template): string
    {
        // Protect fenced code blocks inside <x-slidewire::markdown>
        $template = (string) preg_replace_callback(
            '/(<x-slidewire::markdown\b[^>]*>)(.*?)(<\/x-slidewire::markdown>)/is',
            fn (array $match): string => $match[1] . $this->encodeCodeBlocks($match[2]) . $match[3],
            $template,
        );

        // Protect entire slot content inside <x-slidewire::code>
        return (string) preg_replace_callback(
            '/(<x-slidewire::code\b[^>]*>)(.*?)(<\/x-slidewire::code>)/is',
            fn (array $match): string => $match[1] . $this->encodeSlotContent($match[2]) . $match[3],
            $template,
        );
    }

    public static function decode(string $content): string
    {
        return (string) preg_replace_callback(
            '/' . preg_quote(self::PLACEHOLDER_PREFIX, '/') . '([A-Za-z0-9+\/=]+)' . preg_quote(self::PLACEHOLDER_SUFFIX, '/') . '/',
            fn (array $match): string => base64_decode($match[1], true) ?: $match[0],
            $content,
        );
    }

    protected function encodeCodeBlocks(string $content): string
    {
        return (string) preg_replace_callback(
            '/```[\w-]*\n.*?```/s',
            fn (array $match): string => self::PLACEHOLDER_PREFIX . base64_encode($match[0]) . self::PLACEHOLDER_SUFFIX,
            $content,
        );
    }

    protected function encodeSlotContent(string $content): string
    {
        if (trim($content) === '') {
            return $content;
        }

        return self::PLACEHOLDER_PREFIX . base64_encode($content) . self::PLACEHOLDER_SUFFIX;
    }
}
