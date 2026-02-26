<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\SlideMarkdownParser;

it('parses markdown with frontmatter and separators', function (): void {
    $content = <<<'MD'
---
title: Intro
---

# Hello

---

## World

```php
echo 'ok';
```
MD;

    $slides = app(SlideMarkdownParser::class)->parse($content);

    expect($slides)->toHaveCount(2)
        ->and($slides[0]['meta']['title'])->toBe('Intro')
        ->and($slides[0]['html'])->toContain('<h1>Hello</h1>')
        ->and($slides[1]['html'])->toContain('<h2>World</h2>')
        ->and($slides[1]['html'])->toContain('phiki');
});
