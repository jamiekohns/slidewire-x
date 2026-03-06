<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\CodeHighlighter;

it('returns fallback HTML when highlighting is disabled', function (): void {
    config()->set('slidewire.slides.highlight.enabled', false);

    $html = app(CodeHighlighter::class)->highlight('echo "test";', 'php')->toHtml();

    expect($html)->toContain('slidewire-code')
        ->and($html)->toContain('language-php')
        ->and($html)->not->toContain('phiki');
});

it('returns fallback HTML for unknown language gracefully', function (): void {
    $html = app(CodeHighlighter::class)->highlight('some code', 'unknown-lang-xyz')->toHtml();

    // Should either fall back or highlight with best guess
    expect($html)->toBeString()
        ->and($html)->not->toBeEmpty();
});

it('escapes code in fallback mode', function (): void {
    config()->set('slidewire.slides.highlight.enabled', false);

    $html = app(CodeHighlighter::class)->highlight('<script>alert("xss")</script>', 'html')->toHtml();

    expect($html)->not->toContain('<script>')
        ->and($html)->toContain('&lt;script&gt;');
});

it('replaces code blocks in markdown content', function (): void {
    $highlighter = app(CodeHighlighter::class);

    $markdown = "Some text\n\n```php\necho 'hi';\n```\n\nMore text";
    $result = $highlighter->replaceCodeBlocks($markdown);

    expect($result)->toContain('phiki')
        ->and($result)->toContain('Some text')
        ->and($result)->toContain('More text');
});

it('handles multiple code blocks in markdown', function (): void {
    $highlighter = app(CodeHighlighter::class);

    $markdown = "```php\necho 'a';\n```\n\n```php\necho 'b';\n```";
    $result = $highlighter->replaceCodeBlocks($markdown);

    // Both code blocks should be highlighted
    expect(substr_count($result, 'language-php'))->toBe(2);
});

it('uses text language for unspecified code blocks', function (): void {
    $highlighter = app(CodeHighlighter::class);

    $markdown = "```\nplain text\n```";
    $result = $highlighter->replaceCodeBlocks($markdown);

    // The block should still be processed (language defaults to 'text')
    expect($result)->toBeString()
        ->and($result)->not->toContain('```');
});

it('passes highlight theme and presentation theme to code blocks', function (): void {
    $highlighter = app(CodeHighlighter::class);

    $markdown = "```php\necho 'test';\n```";
    $result = $highlighter->replaceCodeBlocks($markdown, 'github-light', 'white');

    expect($result)->toContain('phiki');
});

it('applies configured font size to highlighted output', function (): void {
    $html = app(CodeHighlighter::class)->highlight('echo "test";', 'php')->toHtml();

    expect($html)->toContain('font-size: 1rem');
});

it('applies explicit size override to highlighted output', function (): void {
    $html = app(CodeHighlighter::class)->highlight('echo "test";', 'php', null, null, null, 'xl')->toHtml();

    expect($html)->toContain('font-size: 1.25rem')
        ->and($html)->not->toContain('font-size: 1rem');
});

it('preserves non-code content during replaceCodeBlocks', function (): void {
    $highlighter = app(CodeHighlighter::class);

    $markdown = '# Title

Some paragraph with `inline code` and **bold**.';
    $result = $highlighter->replaceCodeBlocks($markdown);

    expect($result)->toBe($markdown);
});
