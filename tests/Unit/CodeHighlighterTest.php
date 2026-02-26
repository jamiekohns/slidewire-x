<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\CodeHighlighter;

it('highlights code using phiki output', function (): void {
    $html = app(CodeHighlighter::class)->highlight("<?php\necho 'hello';", 'php')->toHtml();

    expect($html)->toContain('phiki')
        ->and($html)->toContain('language-php');
});
