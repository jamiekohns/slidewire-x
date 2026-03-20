<?php

declare(strict_types=1);

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\Fixtures\FakeDatabaseDocumentProvider;
use WendellAdriel\SlideWire\DTOs\DatabaseDocument;
use WendellAdriel\SlideWire\DTOs\SlidesConfig;
use WendellAdriel\SlideWire\Livewire\PresentationDeck;

beforeEach(function (): void {
    FakeDatabaseDocumentProvider::seed([
        new DatabaseDocument(
            id: 1,
            name: 'Owned deck',
            content: <<<'BLADE'
<x-slidewire::slide>
# Intro
</x-slidewire::slide>
<x-slidewire::slide>
# Next
</x-slidewire::slide>
BLADE,
            ownerId: 7,
        ),
    ]);

    config()->set('slidewire.slides', new SlidesConfig(
        documentSource: 'database',
        presenterMode: true,
    ));

    config()->set('slidewire.presenter_sync', [
        'enabled' => true,
        'poll_interval_ms' => 250,
        'cache_ttl_seconds' => 300,
        'cache_key_prefix' => 'slidewire:test-presenter-sync',
    ]);

    Route::slidewire('/presentations', FakeDatabaseDocumentProvider::class);
});

afterEach(function (): void {
    Auth::logout();
});

it('hides controls and marks non-presenters as followers when presenter mode is enabled', function (): void {
    Auth::setUser(new GenericUser(['id' => 99, 'remember_token' => null]));

    test()->get('/presentations/1-Owned_deck')
        ->assertSuccessful()
        ->assertDontSee('aria-label="Slide controls"')
        ->assertSee('data-can-interact="false"', false)
        ->assertSee('data-follow-presenter="true"', false);
});

it('blocks non-presenters from mutating deck state through livewire actions', function (): void {
    Auth::setUser(new GenericUser(['id' => 99, 'remember_token' => null]));

    Livewire::test(PresentationDeck::class, deckArgs())
        ->assertSet('activeIndex', 0)
        ->call('nextSlide')
        ->assertSet('activeIndex', 0)
        ->call('goToSlide', 1)
        ->assertSet('activeIndex', 0)
        ->call('navigateDown')
        ->assertSet('activeIndex', 0);
});

it('lets presenters navigate and followers poll into presenter state', function (): void {
    Auth::setUser(new GenericUser(['id' => 7, 'remember_token' => null]));

    Livewire::test(PresentationDeck::class, deckArgs())
        ->assertSet('activeIndex', 0)
        ->call('nextSlide')
        ->assertSet('activeIndex', 1);

    Auth::setUser(new GenericUser(['id' => 99, 'remember_token' => null]));

    Livewire::test(PresentationDeck::class, deckArgs())
        ->assertSet('activeIndex', 1);
});

/**
 * @return array<string, string>
 */
function deckArgs(): array
{
    return [
        'presentation' => 'database',
        'document' => '1-Owned_deck',
        'documentSource' => 'database',
        'documentProvider' => FakeDatabaseDocumentProvider::class,
    ];
}
