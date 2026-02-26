<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

/**
 * Blade precompiler that protects fenced code blocks inside
 * <x-slidewire::markdown> from being processed by the Blade compiler.
 *
 * Without this, code blocks containing Blade component syntax
 * (e.g. <x-slidewire::deck>) would be compiled as actual components
 * instead of being treated as literal code text.
 *
 * The precompiler base64-encodes each fenced code block into a
 * placeholder comment. The Markdown component restores them before
 * highlighting and markdown conversion.
 */
class CodeBlockPrecompiler
{
    /**
     * The placeholder prefix used to identify encoded code blocks.
     */
    public const string PLACEHOLDER_PREFIX = '<!--SLIDEWIRE_CODE:';

    public const string PLACEHOLDER_SUFFIX = '-->';

    /**
     * Process a Blade template, protecting code blocks inside markdown components.
     */
    public function __invoke(string $template): string
    {
        return (string) preg_replace_callback(
            '/(<x-slidewire::markdown\b[^>]*>)(.*?)(<\/x-slidewire::markdown>)/is',
            fn (array $match): string => $match[1] . $this->encodeCodeBlocks($match[2]) . $match[3],
            $template,
        );
    }

    /**
     * Restore encoded code blocks back to their original fenced format.
     */
    public static function decode(string $content): string
    {
        return (string) preg_replace_callback(
            '/' . preg_quote(self::PLACEHOLDER_PREFIX, '/') . '([A-Za-z0-9+\/=]+)' . preg_quote(self::PLACEHOLDER_SUFFIX, '/') . '/',
            fn (array $match): string => base64_decode($match[1], true) ?: $match[0],
            $content,
        );
    }

    /**
     * Find and encode all fenced code blocks within a markdown slot.
     */
    protected function encodeCodeBlocks(string $content): string
    {
        return (string) preg_replace_callback(
            '/```[\w-]*\n.*?```/s',
            fn (array $match): string => self::PLACEHOLDER_PREFIX . base64_encode($match[0]) . self::PLACEHOLDER_SUFFIX,
            $content,
        );
    }
}
