<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\SlideMarkdownParser;

it('parses markdown with no frontmatter', function (): void {
    $content = '# Hello World';
    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides)->toHaveCount(1)
        ->and($slides[0]['meta'])->toBe([])
        ->and($slides[0]['html'])->toContain('Hello World');
});

it('parses multiple slides separated by horizontal rules', function (): void {
    $content = "# Slide 1\n\n---\n\n# Slide 2\n\n---\n\n# Slide 3";
    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides)->toHaveCount(3)
        ->and($slides[0]['html'])->toContain('Slide 1')
        ->and($slides[1]['html'])->toContain('Slide 2')
        ->and($slides[2]['html'])->toContain('Slide 3');
});

it('applies global frontmatter to first slide only', function (): void {
    $content = "---\ntheme: black\ntransition: fade\n---\n\n# First\n\n---\n\n# Second";
    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides)->toHaveCount(2)
        ->and($slides[0]['meta']['theme'])->toBe('black')
        ->and($slides[0]['meta']['transition'])->toBe('fade')
        ->and($slides[1]['meta'])->toBe([]);
});

it('supports per-slide frontmatter on subsequent slides', function (): void {
    // Per-slide frontmatter needs to appear at the start of the chunk (after split by separator)
    $content = <<<'MD'
---
title: Global Title
---

# First Slide

---

# Second Slide (no frontmatter)
MD;

    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides)->toHaveCount(2)
        ->and($slides[0]['meta']['title'])->toBe('Global Title')
        ->and($slides[0]['html'])->toContain('First Slide')
        ->and($slides[1]['meta'])->toBe([])
        ->and($slides[1]['html'])->toContain('Second Slide');
});

it('handles empty content gracefully', function (): void {
    $slides = app(SlideMarkdownParser::class)->parse('');

    expect($slides)->toBe([]);
});

it('handles content with only whitespace', function (): void {
    $slides = app(SlideMarkdownParser::class)->parse("   \n\n   ");

    expect($slides)->toBe([]);
});

it('highlights code blocks in markdown slides', function (): void {
    $content = "# Slide\n\n```php\necho 'hi';\n```";
    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides[0]['html'])->toContain('phiki');
});

it('preserves HTML structure in parsed output', function (): void {
    $content = "## Heading\n\n- Item 1\n- Item 2\n\nParagraph text.";
    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides[0]['html'])->toContain('<h2>')
        ->and($slides[0]['html'])->toContain('<li>')
        ->and($slides[0]['html'])->toContain('<p>');
});
