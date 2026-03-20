<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Tests\Fixtures\FakeDatabaseDocumentProvider;
use WendellAdriel\SlideWire\DTOs\DatabaseDocument;

beforeEach(function (): void {
    FakeDatabaseDocumentProvider::seed([
        new DatabaseDocument(
            id: 1,
            name: 'First Test',
            content: <<<'BLADE'
<x-slidewire::slide>
# Hello from DB

- SlideWire
- Database
</x-slidewire::slide>
BLADE,
        ),
    ]);
});

it('renders a database-backed presentation by id-slug route token', function (): void {
    Route::slidewire('/presentations', FakeDatabaseDocumentProvider::class);

    test()->get('/presentations/1-First_Test')
        ->assertSuccessful()
        ->assertSee('Hello from DB')
        ->assertSee('SlideWire')
        ->assertSee('Database');
});

it('resolves the same document when only slug text changes', function (): void {
    Route::slidewire('/presentations', FakeDatabaseDocumentProvider::class);

    test()->get('/presentations/1-Another_Name')
        ->assertSuccessful()
        ->assertSee('Hello from DB');
});

it('returns 404 for invalid document tokens', function (): void {
    Route::slidewire('/presentations', FakeDatabaseDocumentProvider::class);

    test()->get('/presentations/not-a-token')
        ->assertNotFound();
});

it('returns 404 when the document id does not exist', function (): void {
    Route::slidewire('/presentations', FakeDatabaseDocumentProvider::class);

    test()->get('/presentations/999-Missing')
        ->assertNotFound();
});
