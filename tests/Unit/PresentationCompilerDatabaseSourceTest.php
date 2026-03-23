<?php

declare(strict_types=1);

use Tests\Fixtures\FakeDatabaseDocumentProvider;
use WendellAdriel\SlideWire\DTOs\DatabaseDocument;
use WendellAdriel\SlideWire\DTOs\SlidesConfig;
use WendellAdriel\SlideWire\Support\PresentationCompiler;

beforeEach(function (): void {
    FakeDatabaseDocumentProvider::seed([]);
    config()->set('slidewire.slides', new SlidesConfig(documentSource: 'database'));
});

it('compiles database document slides using id token prefix and ignores slug text', function (): void {
    FakeDatabaseDocumentProvider::seed([
        new DatabaseDocument(
            id: 1,
            name: 'First Test',
            content: <<<'BLADE'
<x-slidewire::slide>
## Database Title

- One
- Two
</x-slidewire::slide>
BLADE,
        ),
    ]);

    $compiler = app(PresentationCompiler::class);
    $compiled = $compiler->compile('database', '1-Any_Name', 'database', FakeDatabaseDocumentProvider::class);
    $slides = $compiler->flattenSlides($compiled['slides']);

    expect($slides)->toHaveCount(1)
        ->and($slides[0]->id)->toContain('database-1-0')
        ->and($slides[0]->html)->toContain('Database Title')
        ->and($slides[0]->html)->toContain('<ul>');
});

it('wraps each database slide body in markdown rendering', function (): void {
    FakeDatabaseDocumentProvider::seed([
        new DatabaseDocument(
            id: 2,
            name: 'Markdown Bodies',
            content: <<<'BLADE'
<x-slidewire::slide>
### Heading

`inline code`
</x-slidewire::slide>
<x-slidewire::slide>
1. First
2. Second
</x-slidewire::slide>
BLADE,
        ),
    ]);

    $compiler = app(PresentationCompiler::class);
    $compiled = $compiler->compile('database', '2-Markdown_Bodies', 'database', FakeDatabaseDocumentProvider::class);
    $slides = $compiler->flattenSlides($compiled['slides']);

    expect($slides)->toHaveCount(2)
        ->and($slides[0]->html)->toContain('<h3')
        ->and($slides[0]->html)->toContain('<code>inline code</code>')
        ->and($slides[1]->html)->toContain('<ol>');
});

it('returns empty slides for malformed document tokens', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('database', 'abc', 'database', FakeDatabaseDocumentProvider::class);

    expect($compiled['slides'])->toBe([]);
});

it('returns empty slides when database document cannot be found', function (): void {
    $compiled = app(PresentationCompiler::class)->compile('database', '404-Missing', 'database', FakeDatabaseDocumentProvider::class);

    expect($compiled['slides'])->toBe([]);
});

it('exposes database document metadata for presenter ownership checks', function (): void {
    FakeDatabaseDocumentProvider::seed([
        new DatabaseDocument(
            id: 11,
            name: 'Owner Metadata',
            content: <<<'BLADE'
<x-slidewire::slide>
## Owner
</x-slidewire::slide>
BLADE,
            ownerId: 22,
        ),
    ]);

    $compiled = app(PresentationCompiler::class)->compile('database', '11-Owner_Metadata', 'database', FakeDatabaseDocumentProvider::class);

    expect($compiled['deck_meta']['_slidewire_document_id'])->toBe('11')
        ->and($compiled['deck_meta']['_slidewire_owner_id'])->toBe('22');
});

it('throws when configured provider does not implement the provider contract', function (): void {
    app(PresentationCompiler::class)->compile('database', '1-First_Test', 'database', stdClass::class);
})->throws(RuntimeException::class, 'must implement');
