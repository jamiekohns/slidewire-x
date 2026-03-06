<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

// Protects markdown/code content from Blade compilation until highlight time.
class CodeBlockPrecompiler
{
    public const string PLACEHOLDER_PREFIX = '<!--SLIDEWIRE_CODE:';

    public const string PLACEHOLDER_SUFFIX = '-->';

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
